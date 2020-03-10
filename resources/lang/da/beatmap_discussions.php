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

return [
    'authorizations' => [
        'update' => [
            'null_user' => 'Du skal være logget ind for at kunne redigere.',
            'system_generated' => 'System-genererede opslag kan ikke redigeres.',
            'wrong_user' => 'Du skal være ejeren af opslaget for at kunne redigere det.',
        ],
    ],

    'events' => [
        'empty' => 'Der er ikke sket noget... endnu.',
    ],

    'index' => [
        'deleted_beatmap' => 'slettet',
        'none_found' => '',
        'title' => 'Beatmap Diskussioner',

        'form' => [
            '_' => 'Søg',
            'deleted' => 'Inkluder slettede diskussioner',
            'only_unresolved' => 'Vis kun uløste diskussioner',
            'types' => 'Meddelelsestyper',
            'username' => 'Brugernavn',

            'beatmapset_status' => [
                '_' => 'Beatmap Status',
                'all' => 'Alle',
                'disqualified' => 'Diskvalificeret',
                'never_qualified' => 'Ikke kvalificeret',
                'qualified' => 'Kvalificeret',
                'ranked' => 'Ranked',
            ],

            'user' => [
                'label' => 'Bruger',
                'overview' => 'Aktivitets-oversigt',
            ],
        ],
    ],

    'item' => [
        'created_at' => 'Opslagsdato',
        'deleted_at' => 'Sletnings dato',
        'message_type' => 'Skriv',
        'permalink' => 'Permalink',
    ],

    'nearby_posts' => [
        'confirm' => 'Ingen af opslagene angår mine bekymringer',
        'notice' => 'Der er opslag omkring :timestamp (:existing_timestamps). Vær venlig at tjekke dem inden du slår noget op.',
    ],

    'reply' => [
        'open' => [
            'guest' => 'Log ind for at svare',
            'user' => 'Svar',
        ],
    ],

    'review' => [
        'go_to_parent' => 'Vis Anmeldelses-opslag',
        'go_to_child' => 'Vis Diskussion',
        'validation' => [
            'invalid_block_type' => 'ugyldig block type',
            'invalid_document' => 'ugyldig vurdering',
            'minimum_issues' => 'vurdering skal som minimum indeholde :count fejl|vurdering skal som minimum indholde :count fejl',
            'missing_text' => 'block mangler tekst',
            'too_many_blocks' => 'vurderinger må kun indeholde :count afsnit/problem|vurderinger må kun indeholde op til :count afsnit/problemer',
        ],
    ],

    'system' => [
        'resolved' => [
            'true' => 'Marker som løst af :user',
            'false' => 'Genåbnet af :user',
        ],
    ],

    'timestamp_display' => [
        'general' => 'generelt',
        'general_all' => 'generelt (alle)',
    ],

    'user_filter' => [
        'everyone' => 'Alle',
        'label' => 'Filtrer efter bruger',
    ],
];
