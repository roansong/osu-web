{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>.

    This file is part of osu!web. osu!web is distributed with the hope of
    attracting more community contributions to the core ecosystem of osu!.

    osu!web is free software: you can redistribute it and/or modify
    it under the terms of the Affero GNU General Public License version 3
    as published by the Free Software Foundation.

    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
--}}

<?php
    $percentage = sprintf('%.2f%%', 100.0 * $pollOption['total'] / max($pollSummary['total'], 1));
?>

<tr class="forum-poll-row {{ $pollOption['voted_by_user'] ? 'forum-poll-row--voted' : '' }}">
    <td class="forum-poll-row__column">
        <label class="forum-poll-row__checkbox-container">
            @if (priv_check('ForumTopicVote', $topic)->can())
                @include('objects._switch', [
                    'checked' => $pollOption['voted_by_user'],
                    'name' => 'forum_topic_vote[option_ids][]',
                    'type' => $topic->poll_max_options === 1 ? 'radio' : 'checkbox',
                    'value' => $pollOptionId,
                ])
            @endif
        </label>
    </td>

    <td class="forum-poll-row__column forum-poll-row__column--bar">
        <div class="bar bar--forum-poll {{ $pollOption['voted_by_user'] ? 'bar--forum-poll-voted' : '' }}">
            <div
                class="bar__fill"
                style="width: {{ $canViewResults ? $percentage : '100%' }}"
            >
            </div>
            <div class="forum-poll-row__option-text">
                {!! $pollOption['textHTML'] !!}
            </div>
        </div>
    </td>

    @if ($canViewResults)
        <td class="forum-poll-row__column forum-poll-row__column--percentage">
            {{ $percentage }}
        </td>

        <td class="forum-poll-row__column">
            {{ $pollOption['total'] }}
        </td>
    @endif
</tr>
