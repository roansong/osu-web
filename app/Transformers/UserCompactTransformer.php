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

namespace App\Transformers;

use App\Models\User;
use App\Models\UserGroup;
use League\Fractal;

class UserCompactTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'country',
        'cover',
        'groups',
        'support_level',
    ];

    public function transform(User $user)
    {
        return [
            'id' => $user->user_id,
            'username' => $user->username,
            'profile_colour' => $user->user_colour,
            'avatar_url' => $user->user_avatar,
            'country_code' => $user->country_acronym,
            'default_group' => $user->defaultGroup(),
            'is_active' => $user->isActive(),
            'is_bot' => $user->isBot(),
            'is_online' => $user->isOnline(),
            'is_supporter' => $user->isSupporter(),
            'last_visit' => json_time($user->displayed_last_visit),
            'pm_friends_only' => $user->pm_friends_only,
        ];
    }

    public function includeCountry(User $user)
    {
        return $user->country === null
            ? $this->primitive(null)
            : $this->item($user->country, new CountryTransformer);
    }

    public function includeCover(User $user)
    {
        return $this->item($user, function ($user) {
            $profileCustomization = $user->userProfileCustomization;

            return [
                'custom_url' => $profileCustomization ? $profileCustomization->cover()->fileUrl() : null,
                'url' => $profileCustomization ? $profileCustomization->cover()->url() : null,
                'id' => $profileCustomization ? $profileCustomization->cover()->id() : null,
            ];
        });
    }

    public function includeGroups(User $user)
    {
        return $this->item($user, function ($user) {
            $groups = [];

            foreach ($user->groupIds() as $id) {
                $name = array_search_null($id, UserGroup::GROUPS);
                if ($name !== null && $id !== UserGroup::GROUPS['admin']) {
                    $groups[] = $name;
                }
            }

            return $groups;
        });
    }

    public function includeSupportLevel(User $user)
    {
        return $this->primitive($user->supportLevel());
    }
}
