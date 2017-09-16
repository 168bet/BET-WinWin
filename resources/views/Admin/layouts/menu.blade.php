{{--<li class="treeview {{ Route::is('carriers.*') ? 'active' : '' }}">--}}
    {{--<a href="#">--}}
        {{--<i class="fa fa-dashboard"></i> <span>运营商管理</span>--}}
        {{--<span class="pull-right-container">--}}
              {{--<i class="fa fa-angle-left pull-right"></i>--}}
            {{--</span>--}}
    {{--</a>--}}
    {{--<ul class="treeview-menu">--}}
        {{--<li class="{{ Route::is('carriers.*') ? 'active' : '' }}">--}}
            {{--<a href="{!! route('carriers.index') !!}"><i class="fa fa-edit"></i><span>运营商列表</span></a>--}}
        {{--</li>--}}
    {{--</ul>--}}
{{--</li>--}}

<li class="treeview {{ Route::is('carriers.*') ? 'active' : '' }}">
    <a href="{!! route('carriers.index') !!}"><i class="fa fa-list"></i><span>运营商列表</span></a>
</li>
<li class="treeview {{ Request::is('games*') ? 'active' : '' }}">
    <a href="{!! route('games.index') !!}"><i class="fa fa-gamepad"></i><span>游戏管理</span></a>
</li>

