<?php

namespace App\Main;

class SideMenu
{
    /**
     * List of side menu items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function menu()
    {
        return [

            'dashboard' => [
                'icon' => 'home',
                'route_name' => 'dashboard',
                'params' => "",
                'title' => 'Dashboard',
            ],

            'customer-list' => [
                'icon' => 'users',
                'route_name' => 'customers',
                'params' => "",
                'title' => 'Customers',
            ],
            'astrologer-list' => [
                'icon' => 'target',
                'route_name' => 'astrologers',
                'params' => "",
                'title' => 'Astrologers',
            ],
            'astroMall' => [
                'icon' => 'layers',
                'title' => 'AstroMall',
                'sub_menu' => [
                    'productCategory' => [
                        'icon' => '',
                        'route_name' => 'productCategories',
                        'params' => "",
                        'title' => 'Product Categories',
                    ],
                    'product' => [
                        'icon' => '',
                        'route_name' => 'products',
                        'params' => "",
                        'title' => 'Products',
                    ],
                    'order' => [
                        'icon' => '',
                        'route_name' => 'orders',
                        'params' => "",
                        'title' => 'Orders',
                    ],
                ],
            ],
            'daily-horoscope' => [
                'icon' => 'book-open',
                'title' => 'Horoscope Management',
                'sub_menu' => [
                    'dailyHoroScope' => [
                        'icon' => '',
                        'route_name' => 'dailyHoroscope',
                        'params' => "",
                        'title' => 'Daily HoroScope',
                    ],
                    'dailyHoroScopeInsight' => [
                        'icon' => '',
                        'route_name' => 'dailyHoroscopeInsight',
                        'params' => "",
                        'title' => 'Horoscope Insights',
                    ],
                    'horoscope' => [
                        'icon' => '',
                        'route_name' => 'horoscope',
                        'params' => "",
                        'title' => 'Weekly & Yearly Horoscope',
                    ],
                    'horoscopeFeedback' => [
                        'icon' => '',
                        'route_name' => 'horoscopeFeedback',
                        'params' => "",
                        'title' => 'Horoscope Feedback',
                    ],
                ]],
            'blog-list' => [
                'icon' => 'edit',
                'route_name' => 'blogs',
                'params' => "",
                'title' => 'Blogs',
            ],
            'news' => [
                'icon' => 'airplay',
                'route_name' => 'astroguruNews',
                'params' => "",
                'title' => 'Astroguru News',
            ],

            'adsVideo' => [
                'icon' => 'video',
                'route_name' => 'adsVideos',
                'params' => "",
                'title' => 'Videos Ads',
            ],

            'ticket' => [
                'icon' => 'twitch',
                'route_name' => 'tickets',
                'params' => "",
                'title' => 'Support Tickets',
            ],
            'earning' => [
                'icon' => 'dollar-sign',
                'title' => 'Earnings',
                'sub_menu' => [
                    'commission-list' => [
                        'icon' => 'dollar-sign',
                        'route_name' => 'commissions',
                        'params' => "",
                        'title' => 'Commissions',
                    ],
                    'withdrawl' => [
                        'icon' => 'dollar-sign',
                        'route_name' => 'withdrawalRequests',
                        'params' => "",
                        'title' => 'Withdrawal Requests',
                    ],
                ],
            ],
            'report' => [
                'icon' => 'file',
                'title' => 'Reports',
                'sub_menu' => [
                    'callHistory' => [
                        'icon' => 'phone',
                        'route_name' => 'callHistory',
                        'params' => "",
                        'title' => 'Call History',
                    ],
                    'chatHistory' => [
                        'icon' => 'chat',
                        'route_name' => 'chatHistory',
                        'params' => "",
                        'title' => 'Chat History',
                    ],
                    'partnerWiseEarning' => [
                        'icon' => 'doller',
                        'route_name' => 'partnerWiseEarning',
                        'params' => "",
                        'title' => 'PartnerWise Earning',
                    ],
                    'orderRequest' => [
                        'icon' => 'doller',
                        'route_name' => 'orderrequest',
                        'params' => "",
                        'title' => 'Order Request',
                    ],
                    'reportRequest' => [
                        'icon' => 'doller',
                        'route_name' => 'reportrequest',
                        'params' => "",
                        'title' => 'Report Request',
                    ],
                ],
            ],

            'reportBlock' => [
                'icon' => 'slash',
                'route_name' => 'astrologerReview',
                'params' => "",
                'title' => 'Astrologer Reviews',
            ],
            'blockAstrologer' => [
                'icon' => 'slash',
                'route_name' => 'blockAstrologer',
                'params' => "",
                'title' => 'Block Astrologer',
            ],
            'master-page' => [
                'icon' => 'book-open',
                'title' => 'Master Settings',
                'sub_menu' => [
                    'skill-list' => [
                        'icon' => '',
                        'route_name' => 'skills',
                        'params' => "",
                        'title' => 'Skills',
                    ],
                    'gift-list' => [
                        'icon' => '',
                        'route_name' => 'gifts',
                        'params' => "",
                        'title' => 'Gifts',
                    ],
                    'customer-profile' => [
                        'icon' => '',
                        'route_name' => 'customerProfile',
                        'params' => "",
                        'title' => 'Customer Profile',
                    ],
                    'astrologer-category-list' => [
                        'icon' => '',
                        'route_name' => 'astrologerCategories',
                        'params' => "",
                        'title' => 'Astrologer Categories',
                    ],
                    'horor-scope-sign-list' => [
                        'icon' => '',
                        'route_name' => 'horoscopeSigns',
                        'params' => "",
                        'title' => 'HoroScope Signs',
                    ],
                    'reports' => [
                        'icon' => 'folder',
                        'route_name' => 'reportTypes',
                        'params' => "",
                        'title' => 'Report Type',
                    ],
                    'banner-list' => [
                        'icon' => '',
                        'route_name' => 'banners',
                        'params' => "",
                        'title' => 'Banners',
                    ],

                    'notification-list' => [
                        'icon' => '',
                        'route_name' => 'notifications',
                        'params' => "",
                        'title' => 'Notifications',
                    ],
                    'help-support' => [
                        'icon' => '',
                        'route_name' => 'helpSupport',
                        'params' => "",
                        'title' => 'Help Support',
                    ],
                    'recharge-amount' => [
                        'icon' => '',
                        'route_name' => 'rechargeAmount',
                        'params' => "",
                        'title' => 'Recharge Amount',
                    ],
                ],
            ],
            'systemFlag' => [
                'icon' => 'settings',
                'route_name' => 'setting',
                'params' => "",
                'title' => 'General Settings',
            ],
            'teamManagement'=>[
                'icon' => 'user',
                'title' => 'Team Management',
                'sub_menu' => [
                    'team' => [
                        'icon' => '',
                        'route_name' => 'teamRole',
                        'params' => "",
                        'title' => 'Team Role',
                    ],
                    'teamList' => [
                        'icon' => '',
                        'route_name' => 'team-list',
                        'params' => "",
                        'title' => 'Team List',
                    ],
                ]
                ],
            'appFeedback' => [
                'icon' => 'message-square',
                'route_name' => 'feedback',
                'params' => "",
                'title' => 'Feedback',
            ],
        ];
    }
}
