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
    'support' => [
        'convinced' => [
            'title' => '可以可以，买买买！',
            'support' => '支持 osu!',
            'gift' => '或者以礼物方式赠送给其它玩家',
            'instructions' => '点击爱心前往 osu! 商店',
        ],
        'why-support' => [
            'title' => '为什么我应该支持 osu!？钱将用往何处？',

            'team' => [
                'title' => '支持开发团队',
                'description' => '一个小团队开发并维护着 osu!，你的支持可以帮助他们继续下去。',
            ],
            'infra' => [
                'title' => '维护服务器',
                'description' => '',
            ],
            'featured-artists' => [
                'title' => '精选艺术家',
                'description' => '在你的支持下，我们可以与更多艺术家合作为 osu! 带来更多的绝佳音乐。',
                'link_text' => '查看当前列表 &raquo;',
            ],
            'ads' => [
                'title' => '',
                'description' => '你的帮助可以让游戏保持独立并远离广告，不受外部赞助商的控制。',
            ],
            'tournaments' => [
                'title' => '官方比赛',
                'description' => '为运营 osu! 世界杯筹集资金（及奖励）。',
                'link_text' => '探索比赛 &raquo;',
            ],
            'bounty-program' => [
                'title' => '开源赏金计划',
                'description' => '支持那些花费时间与精力来帮助 osu! 变得更好的社区贡献者。',
                'link_text' => '了解更多 &raquo;',
            ],
        ],
        'perks' => [
            'title' => '我能得到什么？',
            'osu_direct' => [
                'title' => 'osu!direct',
                'description' => '在游戏客户端内搜索和下载谱面。',
            ],

            'friend_ranking' => [
                'title' => '好友排名',
                'description' => "",
            ],

            'country_ranking' => [
                'title' => '',
                'description' => '',
            ],

            'mod_filtering' => [
                'title' => '',
                'description' => '',
            ],

            'auto_downloads' => [
                'title' => '自动下载',
                'description' => '本地没有需要的谱面时，osu! 会自动下载！',
            ],

            'upload_more' => [
                'title' => '上传更多谱面',
                'description' => '谱面集中 Pending 谱面上限增加到 10 张。',
            ],

            'early_access' => [
                'title' => '抢先体验',
                'description' => '抢先体验正在测试中的新特性！',
            ],

            'customisation' => [
                'title' => '个性化',
                'description' => "自定义个人资料页。",
            ],

            'beatmap_filters' => [
                'title' => '筛选铺面',
                'description' => '可在搜索谱面时以更多角度筛选。',
            ],

            'yellow_fellow' => [
                'title' => '用户名高亮',
                'description' => '聊天时，用户名会变成亮黄色。',
            ],

            'speedy_downloads' => [
                'title' => '高速下载',
                'description' => '更快的下载速度。使用 osu!direct 的话甚至会更快。',
            ],

            'change_username' => [
                'title' => '修改用户名',
                'description' => '你能得到一次免费修改用户名的机会。',
            ],

            'skinnables' => [
                'title' => '更多的皮肤',
                'description' => '自定义更多的游戏界面元素，例如主菜单的背景。',
            ],

            'feature_votes' => [
                'title' => '新特性投票',
                'description' => '为新功能投票（每月 2 票）。',
            ],

            'sort_options' => [
                'title' => '详细的排名',
                'description' => '查看排名时可按 国家/好友/所选MOD 进行排名。',
            ],

            'more_favourites' => [
                'title' => '',
                'description' => '',
            ],
            'more_friends' => [
                'title' => '',
                'description' => '',
            ],
            'more_beatmaps' => [
                'title' => '',
                'description' => '',
            ],
            'friend_filtering' => [
                'title' => '',
                'description' => '',
            ],

        ],
        'supporter_status' => [
            'contribution' => '感谢你一直以来的支持！你已经捐赠了 :dollars 并购买了 :tags 次支持者标签！',
            'gifted' => "你已经捐赠了 :giftedTags 次支持者标签（花费了 :giftedDollars ），真慷慨啊！",
            'not_yet' => "你还没有支持者标签 :(",
            'valid_until' => '你的支持者标签将在 :date 到期',
            'was_valid_until' => '你的支持者标签已于 :date 到期',
        ],
    ],
];
