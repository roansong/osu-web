<?php

/**
 *    Copyright (c) ppy Pty Ltd <contact@ppy.sh>.
 *
 *    This file is part of osu!web. osu!web is distributed with the hope of
 *    attracting more community contributions to the core ecosystem of osu!.
 *
 *    osu!web is free software: you can redistribute it and/or modify
 *    it under the terms of the Affero GNU General Public License version 3
 *    as published by the Free Software Foundation.
 *
 *    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
 *    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *    See the GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Libraries\Search;

use App\Libraries\Elasticsearch\Sort;
use App\Models\Beatmap;
use App\Models\Genre;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;

class BeatmapsetSearchRequestParams extends BeatmapsetSearchParams
{
    const AVAILABLE_STATUSES = ['any', 'leaderboard', 'ranked', 'qualified', 'loved', 'favourites', 'pending', 'graveyard', 'mine'];
    const AVAILABLE_EXTRAS = ['video', 'storyboard'];
    const AVAILABLE_GENERAL = ['recommended', 'converts'];
    const AVAILABLE_PLAYED = ['any', 'played', 'unplayed'];
    const AVAILABLE_RANKS = ['XH', 'X', 'SH', 'S', 'A', 'B', 'C', 'D'];

    const LEGACY_STATUS_MAP = [
        '0' => 'ranked',
        '2' => 'favourites',
        '3' => 'qualified',
        '4' => 'pending',
        '5' => 'graveyard',
        '6' => 'mine',
        '7' => 'any',
        '8' => 'loved',
    ];

    public function __construct(Request $request, ?User $user = null)
    {
        parent::__construct();

        static $validExtras = ['video', 'storyboard'];
        static $validRanks = ['A', 'B', 'C', 'D', 'S', 'SH', 'X', 'XH'];

        $this->user = $user;
        $this->from = $this->pageAsFrom(get_int($request['page']));

        if (is_array($request['cursor'])) {
            $this->searchAfter = array_values($request['cursor']);
        }

        if ($this->user !== null) {
            $this->queryString = es_query_escape_with_caveats($request['q'] ?? $request['query']);

            $status = presence($request['s']);
            $this->status = static::LEGACY_STATUS_MAP[$status] ?? $status;

            $this->genre = get_int($request['g']);
            $this->language = get_int($request['l']);
            $this->extra = array_intersect(
                explode('.', $request['e']),
                $validExtras
            );

            $this->mode = get_int($request['m']);
            if (!in_array($this->mode, Beatmap::MODES, true)) {
                $this->mode = null;
            }

            $generals = explode('.', $request['c']) ?? [];
            $this->includeConverts = in_array('converts', $generals, true);
            $this->showRecommended = in_array('recommended', $generals, true);
        }

        $this->parseSortOrder($request['sort']);

        // Supporter-only options.
        $this->rank = array_intersect(
            explode('.', $request['r'] ?? null),
            $validRanks
        );

        $this->playedFilter = $request['played'];
        if (!in_array($this->playedFilter, static::PLAYED_STATES, true)) {
            $this->playedFilter = null;
        }
    }

    public static function getAvailableFilters()
    {
        $languages = Language::listing();
        $genres = Genre::listing();

        $modes = [['id' => null, 'name' => trans('beatmaps.mode.any')]];
        foreach (Beatmap::MODES as $name => $id) {
            $modes[] = ['id' => $id, 'name' => trans("beatmaps.mode.{$name}")];
        }

        $extras = [];
        $general = [];
        $played = [];
        $ranks = [];
        $statuses = [];

        foreach (static::AVAILABLE_EXTRAS as $id) {
            $extras[] = ['id' => $id, 'name' => trans("beatmaps.extra.{$id}")];
        }

        foreach (static::AVAILABLE_GENERAL as $id) {
            $general[] = ['id' => $id, 'name' => trans("beatmaps.general.{$id}")];
        }

        foreach (static::AVAILABLE_PLAYED as $id) {
            $played[] = ['id' => $id, 'name' => trans("beatmaps.played.{$id}")];
        }

        foreach (static::AVAILABLE_RANKS as $id) {
            $ranks[] = ['id' => $id, 'name' => trans("beatmaps.rank.{$id}")];
        }

        foreach (static::AVAILABLE_STATUSES as $id) {
            $statuses[] = ['id' => $id, 'name' => trans("beatmaps.status.{$id}")];
        }

        return compact('extras', 'general', 'genres', 'languages', 'modes', 'played', 'ranks', 'statuses');
    }

    private function getDefaultSort(string $order) : array
    {
        if (present($this->queryString)) {
            return [new Sort('_score', $order)];
        }

        if ($this->status === 'qualified') {
            return [
                new Sort('queued_at', $order),
                new Sort('approved_date', $order), // fallback
            ];
        }

        if (in_array($this->status, ['pending', 'graveyard', 'mine'], true)) {
            return [new Sort('last_update', $order)];
        }

        return [new Sort('approved_date', $order)];
    }

    /**
     * Generate sort parameters for the elasticsearch query.
     */
    private function normalizeSort(Sort $sort) : array
    {
        // additional options
        static $orderOptions = [
            'difficulties.difficultyrating' => [
                'asc' => ['mode' => 'min'],
                'desc' => ['mode' => 'max'],
            ],
        ];

        $newSort = [];
        // assign sort modes if any.
        $options = ($orderOptions[$sort->field] ?? [])[$sort->order] ?? [];
        if ($options !== []) {
            $sort->mode = $options['mode'];
        }

        $newSort[] = $sort;

        // append/prepend extra sort orders.
        if ($sort->field === 'nominations') {
            $newSort[] = new Sort('hype', $sort->order);
        } elseif ($sort->field === 'approved_date' && $this->status === 'qualified') {
            array_unshift($newSort, new Sort('queued_at', $sort->order));
        }

        return $newSort;
    }

    private function parseSortOrder(?string $value)
    {
        $array = explode('_', $value);
        $field = static::remapSortField($array[0]);
        $order = $array[1] ?? null;

        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        if (empty($field)) {
            $this->sorts = $this->getDefaultSort($order);
        } else {
            $this->sorts = $this->normalizeSort(new Sort($field, $order));
        }

        // generic tie-breaker.
        $this->sorts[] = new Sort('_id', $order);
    }

    private static function remapSortField(?string $name)
    {
        static $fields = [
            'artist' => 'artist.raw',
            'creator' => 'creator.raw',
            'difficulty' => 'difficulties.difficultyrating',
            'favourites' => 'favourite_count',
            'nominations' => 'nominations',
            'plays' => 'play_count',
            'ranked' => 'approved_date',
            'rating' => 'rating',
            'relevance' => '_score',
            'title' => 'title.raw',
            'updated' => 'last_update',
        ];

        return $fields[$name] ?? null;
    }
}
