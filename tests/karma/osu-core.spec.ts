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

import { UserJSON } from 'chat/chat-api-responses';
import User from 'models/user';
import OsuCore from 'osu-core';

describe('OsuCore user:update subscriber testing thing', () => {
  it('user:update should update the user store from a JSON value', () => {
    const core = new OsuCore(window);

    const json: UserJSON = {
      avatar_url: '',
      blocks: [],
      can_moderate: false,
      country_code: '',
      id: 1,
      is_active: true,
      is_bot: false,
      is_online: true,
      is_supporter: true,
      pm_friends_only: false,
      profile_colour: '',
      username: 'foo',
    };

    const user = User.fromJSON(json);

    $.publish('user:update', json);

    expect(core.dataStore.userStore.users.get(1)).toEqual(user);
    expect(core.dataStore.userStore.users.size).toEqual(1);
  });
});
