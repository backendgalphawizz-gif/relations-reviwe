<!-- BEGIN: Mobile Menu -->
<style>
    @media only screen and (max-width: 576px) {
        .side-nav .side-menu .side-menu__title{
            display: block;
        }
        .side-nav{
    width: 100%;
    padding-top: 55px;
    background-color: transparent;
    max-width: unset;
    height: auto;
        }
    }
</style>
<div class="mobile-menu md:hidden">

    <div class="mobile-menu-bar" style="background-color:#0fa3c8">

        <a href="" class="flex mr-auto">

            @php

                $logo = DB::table('systemflag')

                    ->where('name', 'AdminLogo')

                    ->select('value')

                    ->first();

            @endphp

            <img alt="Midone - HTML Admin Template" class="mainLogo" src="/{{ $logo->value }}">

        </a>

        <a href="javascript:;" class="mobile-menu-toggler">

            <i data-lucide="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i>

        </a>

    </div>

    <div class="scrollable">

        <a href="javascript:;" class="mobile-menu-toggler">

            <i data-lucide="x-circle" class="w-8 h-8 text-white transform -rotate-90"></i>

        </a>

        <!-- <ul class="scrollable__content py-2">

            @foreach ($side_menu as $menuKey => $menu)

                @if ($menu == 'devider')

                    <li class="menu__devider my-6"></li>

                @else

                    <li>

                        <a href="{{ isset($menu['route_name']) ? route($menu['route_name'], $menu['params']) : 'javascript:;' }}"

                            class="{{ $first_level_active_index == $menuKey ? 'menu menu--active' : 'menu' }}">

                            <div class="menu__icon">

                                <i data-lucide="{{ $menu['icon'] }}"></i>

                            </div>

                            <div class="menu__title">

                                {{ $menu['title'] }}

                                @if (isset($menu['sub_menu']))

                                    <i data-lucide="chevron-down"

                                        class="menu__sub-icon {{ $first_level_active_index == $menuKey ? 'transform rotate-180' : '' }}"></i>

                                @endif

                            </div>

                        </a>

                        @if (isset($menu['sub_menu']))

                            <ul class="{{ $first_level_active_index == $menuKey ? 'menu__sub-open' : '' }}">

                                @foreach ($menu['sub_menu'] as $subMenuKey => $subMenu)

                                    <li>

                                        <a href="{{ isset($subMenu['route_name']) ? route($subMenu['route_name'], $subMenu['params']) : 'javascript:;' }}"

                                            class="{{ $second_level_active_index == $subMenuKey ? 'menu menu--active' : 'menu' }}">

                                            <div class="menu__icon">

                                                <i data-lucide="activity"></i>

                                            </div>

                                            <div class="menu__title">

                                                {{ $subMenu['title'] }}

                                                @if (isset($subMenu['sub_menu']))

                                                    <i data-lucide="chevron-down"

                                                        class="menu__sub-icon {{ $second_level_active_index == $subMenuKey ? 'transform rotate-180' : '' }}"></i>

                                                @endif

                                            </div>

                                        </a>

                                        @if (isset($subMenu['sub_menu']))

                                            <ul

                                                class="{{ $second_level_active_index == $subMenuKey ? 'menu__sub-open' : '' }}">

                                                @foreach ($subMenu['sub_menu'] as $lastSubMenuKey => $lastSubMenu)

                                                    <li>

                                                        <a href="{{ isset($lastSubMenu['route_name']) ? route($lastSubMenu['route_name'], $lastSubMenu['params']) : 'javascript:;' }}"

                                                            class="{{ $third_level_active_index == $lastSubMenuKey ? 'menu menu--active' : 'menu' }}">

                                                            <div class="menu__icon">

                                                                <i data-lucide="zap"></i>

                                                            </div>

                                                            <div class="menu__title">{{ $lastSubMenu['title'] }}</div>

                                                        </a>

                                                    </li>

                                                @endforeach

                                            </ul>

                                        @endif

                                    </li>

                                @endforeach

                            </ul>

                        @endif

                    </li>

                @endif

            @endforeach

        </ul> -->
        <nav class="side-nav" style="    display: block;">
            <ul>
                @php
                    $side_menu = [];
                    $user = auth()->user();
                    $teamMember = DB::table('teammember')
                        ->where('userId', $user->id)
                        ->first();
                    $pages = [];
                    if ($teamMember) {
                        $rolePages = DB::table('rolepages')
                            ->join('adminpages', 'adminpages.id', 'rolepages.adminPageId')
                            ->where('teamRoleId', $teamMember->teamRoleId)
                            ->where('status', 1)
                            ->select('adminpages.*')
                            ->get();
                        $pageGroup = DB::table('adminpages')
                            ->whereNull('pageGroup')
                            ->where('status', 1)
                            ->get();
                        for ($i = 0; $i < count($pageGroup); $i++) {
                            $pages = DB::table('adminpages')
                                ->where('pageGroup', $pageGroup[$i]->id)
                                ->get();
                            $pageGroup[$i]->sub_menu = [];
                            if ($pages && count($pages) > 0) {
                                for ($j = 0; $j < count($rolePages); $j++) {
                                    $id = $rolePages[$j]->id;
                                    $result = array_filter(json_decode($pages), function ($event) use ($id) {
                                        return $event->id === $id;
                                    });
                                    if ($result && count($result) > 0) {
                                        array_push($pageGroup[$i]->sub_menu, $rolePages[$j]);
                                    }
                                }
                            }
                        }
                        for ($i = 0; $i < count($pageGroup); $i++) {
                            if ($pageGroup[$i]->sub_menu && count($pageGroup[$i]->sub_menu) > 0) {
                                array_push($side_menu, $pageGroup[$i]);
                            }
                        }
                        $parentPages = DB::table('rolepages')
                            ->join('adminpages', 'adminpages.id', 'rolepages.adminPageId')
                            ->where('teamRoleId', $teamMember->teamRoleId)
                            ->whereNull('adminpages.pageGroup')
                            ->select('adminpages.*')
                            ->get();
                        $side_menu = array_merge($side_menu, json_decode($parentPages));
                    } else {
                        $pageGroup = DB::table('adminpages')
                            ->whereNull('pageGroup')
                            ->where('status', 1)
                            ->get();
                        for ($i = 0; $i < count($pageGroup); $i++) {
                            $pages = DB::table('adminpages')
                                ->where('pageGroup', $pageGroup[$i]->id)
                                ->where('status', 1)
                                ->get();
                            $pageGroup[$i]->sub_menu = [];
                            if ($pages && count($pages) > 0) {
                                $pageGroup[$i]->sub_menu = $pages;
                            }
                        }
                        $side_menu = $pageGroup;
                    }
                    $side_menu = collect( $side_menu);
                    $side_menu =  $side_menu->sortBy('displayOrder');
                @endphp
                @foreach ($side_menu as $menuKey => $menu)
                    @if ($menu == 'devider')
                        <li class="side-nav__devider my-6"></li>
                    @else
                        <li>
                            <a href="{{ isset($menu->route) ? route($menu->route) : 'javascript:;' }}"
                                class="{{ $first_level_active_index == $menuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                <div class="side-menu__icon">
                                    <i data-lucide="{{ $menu->icon }}"></i>
                                </div>
                                <div class="side-menu__title">
                                    {{ $menu->pageName }}
                                    @if (isset($menu->sub_menu) && count($menu->sub_menu) > 0)
                                        <div
                                            class="side-menu__sub-icon {{ $first_level_active_index == $menuKey ? 'transform rotate-180' : '' }}">
                                            <i data-lucide="chevron-down"></i>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            @if (isset($menu->sub_menu))
                                <ul class="{{ $first_level_active_index == $menuKey ? 'side-menu__sub-open' : '' }}">
                                    @foreach ($menu->sub_menu as $subMenuKey => $subMenu)
                                        <li>
                                            <a href="{{ isset($subMenu->route) ? route($subMenu->route) : 'javascript:;' }}"
                                                class="{{ $second_level_active_index == $subMenuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                <div class="side-menu__icon">
                                                    <i data-lucide="activity"></i>
                                                </div>
                                                <div class="side-menu__title">
                                                    {{ $subMenu->pageName }}
                                                    @if (isset($subMenu->sub_menu))
                                                        <div
                                                            class="side-menu__sub-icon {{ $second_level_active_index == $subMenuKey ? 'transform rotate-180' : '' }}">
                                                            <i data-lucide="chevron-down"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                            @if (isset($subMenu->sub_menu))
                                                <ul
                                                    class="{{ $second_level_active_index == $subMenuKey ? 'side-menu__sub-open' : '' }}">
                                                    @foreach ($subMenu->sub_menu as $lastSubMenuKey => $lastSubMenu)
                                                        <li>
                                                            <a href="{{ isset($lastSubMenu->route) ? route($lastSubMenu->route) : 'javascript:;' }}"
                                                                class="{{ $third_level_active_index == $lastSubMenuKey ? 'side-menu side-menu--active' : 'side-menu' }}">
                                                                <div class="side-menu__icon">
                                                                    <i data-lucide="zap"></i>
                                                                </div>
                                                                <div class="side-menu__title">{{ $lastSubMenu->pageName }}
                                                                </div>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>

    </div>

</div>

<!-- END: Mobile Menu -->

