@if(Auth()->user())

@extends('../layout/main')

@section('head')
    @yield('subhead')
@endsection

@section('content')
    @include('../layout/components/mobile-menu')
    @include('../layout/components/top-bar')
    <div class="flex overflow-hidden">
        <nav class="side-nav">
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
        <div class="content">
            @yield('subcontent')
        </div>
    </div>
@endsection
@endif
