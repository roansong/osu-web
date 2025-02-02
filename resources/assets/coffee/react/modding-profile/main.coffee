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

import { Events } from './events'
import { ExtraTab } from '../profile-page/extra-tab'
import { Discussions} from './discussions'
import { Header } from './header'
import { Kudosu } from '../profile-page/kudosu'
import { Votes } from './votes'
import { BlockButton } from 'block-button'
import { NotificationBanner } from 'notification-banner'
import { Posts } from "./posts"
import * as React from 'react'
import { a, button, div, i, span} from 'react-dom-factories'
el = React.createElement

pages = document.getElementsByClassName("js-switchable-mode-page--scrollspy")
pagesOffset = document.getElementsByClassName("js-switchable-mode-page--scrollspy-offset")

currentLocation = ->
  "#{document.location.pathname}#{document.location.search}"


export class Main extends React.PureComponent
  constructor: (props) ->
    super props

    @cache = {}
    @tabs = React.createRef()
    @pages = React.createRef()
    @state = JSON.parse(props.container.dataset.profilePageState ? null)
    @restoredState = @state?

    if !@restoredState
      page = location.hash.slice(1)
      @initialPage = page if page?

      @state =
        discussions: props.discussions
        events: props.events
        user: props.user
        users: props.users
        posts: props.posts
        votes: props.votes
        profileOrder: ['events', 'discussions', 'posts', 'votes', 'kudosu']
        rankedAndApprovedBeatmapsets: @props.extras.rankedAndApprovedBeatmapsets
        lovedBeatmapsets: @props.extras.lovedBeatmapsets
        unrankedBeatmapsets: @props.extras.unrankedBeatmapsets
        graveyardBeatmapsets: @props.extras.graveyardBeatmapsets
        recentlyReceivedKudosu: @props.extras.recentlyReceivedKudosu
        showMorePagination: {}

      for own elem, perPage of @props.perPage
        @state.showMorePagination[elem] ?= {}
        @state.showMorePagination[elem].hasMore = @state[elem].length > perPage

        if @state.showMorePagination[elem].hasMore
          @state[elem].pop()


  componentDidMount: =>
    $.subscribe 'user:update.profilePage', @userUpdate
    $.subscribe 'profile:showMore.moddingProfilePage', @showMore
    $.subscribe 'profile:page:jump.moddingProfilePage', @pageJump
    $.subscribe 'beatmapsetDiscussions:update.moddingProfilePage', @discussionUpdate
    $(document).on 'ajax:success.moddingProfilePage', '.js-beatmapset-discussion-update', @ujsDiscussionUpdate
    $(window).on 'throttled-scroll.moddingProfilePage', @pageScan

    osu.pageChange()

    @modeScrollUrl = currentLocation()

    if !@restoredState
      Timeout.set 0, => @pageJump null, @initialPage


  componentWillUnmount: =>
    $.unsubscribe '.moddingProfilePage'
    $(window).off '.moddingProfilePage'

    $(window).stop()
    Timeout.clear @modeScrollTimeout


  discussionUpdate: (_e, options) =>
    {beatmapset} = options
    return unless beatmapset?

    discussions = @state.discussions
    posts = @state.posts
    users = @state.users

    discussionIds = _.map discussions, 'id'
    postIds = _.map posts, 'id'
    userIds = _.map users, 'id'

    # Due to the entire hierarchy of discussions being sent back when a post is updated (instead of just the modified post),
    #   we need to iterate over each discussion and their posts to extract the updates we want.
    _.each beatmapset.discussions, (newDiscussion) ->
      if discussionIds.includes(newDiscussion.id)
        discussion = _.find discussions, id: newDiscussion.id
        discussions = _.reject discussions, id: newDiscussion.id
        newDiscussion = _.merge(discussion, newDiscussion)
        # The discussion list shows discussions started by the current user, so it can be assumed that the first post is theirs
        newDiscussion.starting_post = newDiscussion.posts[0]
        discussions.push(newDiscussion)

      _.each newDiscussion.posts, (newPost) ->
        if postIds.includes(newPost.id)
          post = _.find posts, id: newPost.id
          posts = _.reject posts, id: newPost.id
          posts.push(_.merge(post, newPost))

    _.each beatmapset.related_users, (newUser) ->
      if userIds.includes(newUser.id)
        users = _.reject users, id: newUser.id
        users.push(newUser)

    @cache.users = null
    @setState
      discussions: _.reverse(_.sortBy(discussions, (d) -> Date.parse(d.starting_post.created_at)))
      posts: _.reverse(_.sortBy(posts, (p) -> Date.parse(p.created_at)))
      users: users


  render: =>
    profileOrder = @state.profileOrder
    isBlocked = _.find(currentUser.blocks, target_id: @state.user.id)

    div
      className: 'osu-layout__no-scroll' if isBlocked && !@state.forceShow
      if isBlocked
        div className: 'osu-page',
          el NotificationBanner,
            type: 'warning'
            title: osu.trans('users.blocks.banner_text')
            message:
              div className: 'notification-banner__button-group',
                div className: 'notification-banner__button',
                  el BlockButton, userId: @props.user.id
                div className: 'notification-banner__button',
                  button
                    type: 'button'
                    className: 'textual-button'
                    onClick: =>
                      @setState forceShow: !@state.forceShow
                    span {},
                      i className: 'textual-button__icon fas fa-low-vision'
                      " "
                      if @state.forceShow
                        osu.trans('users.blocks.hide_profile')
                      else
                        osu.trans('users.blocks.show_profile')

      div className: "osu-layout osu-layout--full#{if isBlocked && !@state.forceShow then ' osu-layout--masked' else ''}",
        el Header,
          user: @state.user
          stats: @state.user.statistics
          rankHistory: @props.rankHistory
          userAchievements: @props.userAchievements

        div
          className: 'hidden-xs page-extra-tabs page-extra-tabs--profile-page js-switchable-mode-page--scrollspy-offset'
          div className: 'osu-page',
            div
              className: 'page-mode page-mode--profile-page-extra'
              ref: @tabs
              for m in profileOrder
                a
                  className: 'page-mode__item'
                  key: m
                  'data-page-id': m
                  onClick: @tabClick
                  href: "##{m}"
                  el ExtraTab,
                    page: m
                    currentPage: @state.currentPage
                    currentMode: @state.currentMode

        div
          className: 'osu-layout__section osu-layout__section--users-extra'
          div
            className: 'osu-layout__row'
            ref: @pages
            @extraPage name for name in profileOrder


  extraPage: (name) =>
    {extraClass, props, component} = @extraPageParams name
    classes = 'js-switchable-mode-page--scrollspy js-switchable-mode-page--page'
    classes += " #{extraClass}" if extraClass?
    props.name = name

    @extraPages ?= {}

    div
      key: name
      'data-page-id': name
      className: classes
      ref: (el) => @extraPages[name] = el
      el component, props


  extraPageParams: (name) =>
    switch name
      when 'discussions'
        props:
          discussions: @state.discussions
          user: @state.user
          users: @users()
        component: Discussions

      when 'events'
        props:
          events: @state.events
          user: @state.user
          users: @users()
        component: Events

      when 'kudosu'
        props:
          user: @state.user
          recentlyReceivedKudosu: @state.recentlyReceivedKudosu
          pagination: @state.showMorePagination
        component: Kudosu

      when 'posts'
        props:
          posts: @state.posts
          user: @state.user
          users: @users()
        component: Posts

      when 'votes'
        props:
          votes: @state.votes
          user: @state.user
          users: @users()
        component: Votes

  showMore: (e, {name, url, perPage = 50}) =>
    offset = @state[name].length

    paginationState = _.cloneDeep @state.showMorePagination
    paginationState[name] ?= {}
    paginationState[name].loading = true

    @setState showMorePagination: paginationState, ->
      $.get osu.updateQueryString(url, offset: offset, limit: perPage + 1), (data) =>
        state = _.cloneDeep(@state[name]).concat(data)
        hasMore = data.length > perPage

        state.pop() if hasMore

        paginationState = _.cloneDeep @state.showMorePagination
        paginationState[name].loading = false
        paginationState[name].hasMore = hasMore

        @setState
          "#{name}": state
          showMorePagination: paginationState


  pageJump: (_e, page) =>
    if page == 'main'
      @setCurrentPage null, page
      return

    target = $(@extraPages[page])

    # if invalid page is specified, scan current position
    if target.length == 0
      @pageScan()
      return

    # Don't bother scanning the current position.
    # The result will be wrong when target page is too short anyway.
    @scrolling = true
    Timeout.clear @modeScrollTimeout

    # count for the tabs height; assume pageJump always causes the header to be pinned
    # otherwise the calculation needs another phase and gets a bit messy.
    offsetTop = target.offset().top - pagesOffset[0].getBoundingClientRect().height

    $(window).stop().scrollTo window.stickyHeader.scrollOffset(offsetTop), 500,
      onAfter: =>
        # Manually set the mode to avoid confusion (wrong highlight).
        # Scrolling will obviously break it but that's unfortunate result
        # from having the scrollspy marker at middle of page.
        @setCurrentPage null, page, =>
          # Doesn't work:
          # - part of state (callback, part of mode setting)
          # - simple variable in callback
          # Both still change the switch too soon.
          @modeScrollTimeout = Timeout.set 100, => @scrolling = false


  pageScan: =>
    return if @modeScrollUrl != currentLocation()

    return if @scrolling
    return if pages.length == 0

    anchorHeight = pagesOffset[0].getBoundingClientRect().height

    if osu.bottomPage()
      @setCurrentPage null, _.last(pages).dataset.pageId
      return

    for page in pages
      pageDims = page.getBoundingClientRect()
      pageBottom = pageDims.bottom - Math.min(pageDims.height * 0.75, 200)
      continue unless pageBottom > anchorHeight

      @setCurrentPage null, page.dataset.pageId
      return

    @setCurrentPage null, page.dataset.pageId


  setCurrentPage: (_e, page, extraCallback) =>
    callback = =>
      extraCallback?()
      @setHash?()

    if @state.currentPage == page
      return callback()

    @setState currentPage: page, callback


  tabClick: (e) =>
    e.preventDefault()

    @pageJump null, e.currentTarget.dataset.pageId

  userUpdate: (_e, user) =>
    return @forceUpdate() if user?.id != @state.user.id

    # this component needs full user object but sometimes this event only sends part of it
    @setState user: _.assign({}, @state.user, user)

  users: =>
    if !@cache.users?
      @cache.users = _.keyBy @state.users, 'id'
      @cache.users[null] = @cache.users[undefined] =
        username: osu.trans 'users.deleted'

    @cache.users


  ujsDiscussionUpdate: (_e, data) =>
    # to allow ajax:complete to be run
    Timeout.set 0, => @discussionUpdate(null, beatmapset: data)
