###
#    Copyright (c) ppy Pty Ltd <contact@ppy.sh>.
#
#    This file is part of osu!web. osu!web is distributed with the hope of
#    attracting more community contributions to the core ecosystem of osu!.
#
#    osu!web is free software: you can redistribute it and/or modify
#    it under the terms of the Affero GNU General Public License version 3
#    as published by the Free Software Foundation.
#
#    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
#    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#    See the GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
###

import { Rank } from '../profile-page/rank'
import { BlockButton } from 'block-button'
import { FriendButton } from 'friend-button'
import * as React from 'react'
import { a, button, div, i, span} from 'react-dom-factories'
import { ReportUser } from 'report-user'
el = React.createElement


export class DetailBar extends React.PureComponent
  bn = 'profile-detail-bar'


  constructor: (props) ->
    super props

    @eventId = "profile-page-#{osu.uuid()}"
    @state = currentUser: osu.jsonClone(currentUser)


  componentDidMount: =>
    $.subscribe "user:update.#{@eventId}", @updateCurrentUser


  componentWillUnmount: =>
    $.unsubscribe ".#{@eventId}"


  render: =>
    isBlocked = _.find(@state.currentUser.blocks, target_id: @props.user.id)?

    div className: bn,
      div className: "#{bn}__column #{bn}__column--left",
        div className: "#{bn}__menu-item",
          el FriendButton,
            userId: @props.user.id
            showFollowerCounter: true
            followers: @props.user.follower_count
            modifiers: ['profile-page']
            alwaysVisible: true
        if @state.currentUser.id != @props.user.id && !isBlocked
          div className: "#{bn}__menu-item",
            a
              className: 'user-action-button user-action-button--profile-page'
              href: laroute.route 'messages.users.show', user: @props.user.id
              title: osu.trans('users.card.send_message')
              i className: 'fas fa-envelope'

        @renderExtraMenu()

      div className: "#{bn}__column #{bn}__column--right",
        div className: "#{bn}__entry #{bn}__entry--ranking",
          el Rank, type: 'global', stats: @props.stats

        div className: "#{bn}__entry #{bn}__entry--ranking",
          el Rank, type: 'country', stats: @props.stats

        div className: "#{bn}__entry #{bn}__entry--level",
          div
            className: "#{bn}__level"
            title: osu.trans('users.show.stats.level', level: @props.stats.level.current)
            @props.stats.level.current

  renderExtraMenu: =>
    items = []

    if @state.currentUser.id? && @state.currentUser.id != @props.user.id
      blockButton = el BlockButton,
        key: 'block'
        userId: @props.user.id
        wrapperClass: 'simple-menu__item'
        modifiers: ['inline']
      items.push blockButton

      reportButton = el ReportUser,
        key: 'report'
        user: @props.user
        wrapperClass: 'simple-menu__item'
        modifiers: ['inline']
      items.push reportButton

    return null if items.length == 0

    div className: "#{bn}__menu-item",
      button
        className: 'profile-page-toggle js-click-menu'
        title: osu.trans('common.buttons.show_more_options')
        'data-click-menu-target': "profile-page-bar-#{@id}"
        span className: 'fas fa-ellipsis-v'
      div
        className: 'simple-menu simple-menu--profile-page-bar js-click-menu'
        'data-click-menu-id': "profile-page-bar-#{@id}"
        'data-visibility': 'hidden'
        items


  updateCurrentUser: (_e, user) =>
    return unless @state.currentUser.id == user.id

    @setState currentUser: osu.jsonClone(user)
