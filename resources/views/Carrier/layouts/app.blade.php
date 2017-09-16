<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>双赢</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('css/font-awesome/font-awesome.min.css')}}">
    {{--<link rel="stylesheet" href="http://libs.cdnjs.net/font-awesome/4.5.0/css/font-awesome.min.css">--}}
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">--}}

    <link rel="stylesheet" href="{!! asset('select2/4.0.2/css/select2.min.css') !!}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/css/AdminLTE.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/css/skins/_all-skins.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/toastr.js/latest/css/toastr.min.css">
    @yield('css')
    <style>
        thead th,table.table,tr th{
            text-align: center;
        }
        .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td{
            vertical-align: middle;
        }
        div.dataTables_wrapper div.dataTables_info{
            float: left;
        }
        div.dataTables_length{
            margin-bottom: 10px;
        }
        div.dataTables_wrapper div.dataTables_processing{
            position: fixed;
        }
        .modal {
            overflow-x: hidden;
            overflow-y: scroll;
        }
    </style>
    <script src="https://cdn.staticfile.org/jquery/1.12.0/jquery.min.js"></script>

</head>

<body class="hold-transition skin-blue fixed sidebar-mini">

    <div class="wrapper">
        <!-- Main Header -->
        <header class="main-header">

            <!-- Logo -->
            <a href="#" class="logo">
                <b>双赢</b>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">当前额度: {!! WinwinAuth::currentWebCarrier()->remain_quota !!}</span>
                            </a>
                        </li>

                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning" id="notificationBell" style="display: none"></span>
                            </a>
                            <ul class="dropdown-menu" id="notificationMain" style="width: 370px">
                                <li class="header" id="notificationHeader"></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu" id="notificationContent">
                                    </ul>
                                </li>
                                <li class="footer"><a href="#" id="notificationFooterLink">全部已读</a></li>
                            </ul>
                        </li>
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <img src="http://infyom.com/images/logo/blue_logo_150x150.jpg"
                                     class="user-image" alt="User Image"/>
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">{!! WinwinAuth::carrierUser()->username !!}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    <img src="http://infyom.com/images/logo/blue_logo_150x150.jpg"
                                         class="img-circle" alt="User Image"/>
                                    <p>
                                        {!! WinwinAuth::carrierUser()->username !!}
                                        <small>注册于 {!! WinwinAuth::carrierUser()->created_at->toDateString() !!}</small>
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="text-center">
                                        <a href="{!! url('carrier/logout') !!}" class="btn btn-default btn-flat"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            退出登录
                                        </a>
                                        <form id="logout-form" action="{{ url('carrier/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left side column. contains the logo and sidebar -->
        @include('Carrier.layouts.sidebar')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Main Footer -->
        {{--<footer class="main-footer" style="max-height: 100px;text-align: center">--}}
            {{--<strong>Copyright © 2017 <a href="#">ShuangYing</a>.</strong> All rights reserved.--}}
        {{--</footer>--}}

    </div>

    @yield('footer')

    <!-- jQuery 2.1.4 -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="{!! asset('select2/4.0.2/js/select2.min.js') !!}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
    <script src="https://cdn.staticfile.org/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>

    <!-- AdminLTE App -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/js/app.min.js"></script>
    <!--Toast-->
    <script src="https://cdn.staticfile.org/toastr.js/latest/js/toastr.min.js"></script>
    @include('Components.Ajax.WinwinAjax')
    @yield('scripts')
    <script>
        $(function(){
            var bell = $('#notificationBell'),
                notificationHeader  = $('#notificationHeader'),
                notificationContent = $('#notificationContent'),
                notificationFooterLink = $('#notificationFooterLink'),
                notificationMain = $('#notificationMain'),
                    notificationIds = [];

            function markAsReadNotification(notificationIds,redirectRoute) {
                $.ajax({
                    url:'{!! route('markAsReadNotifications') !!}',
                    type:'POST',
                    data: { notificationIds :notificationIds  },
                    success:function(){
                        $.fn.fetchNotification();
                        if(redirectRoute && notificationIds.length  == 1){
                            if('{!! URL::current() !!}' != redirectRoute){
                                window.location.href = redirectRoute;
                            }
                        }
                    }
                });
            }

            notificationFooterLink.on('click',function () {
                if(notificationIds){
                    markAsReadNotification(notificationIds);
                }
            });

            $.fn.fetchNotification = function() {
                $.fn.winwinAjax.sendFetchAjax('{!! route('notifications') !!}',null,function(response){
                    if(response.data){
                        var data = response.data;
                        data.length > 0 ? bell.show() : bell.hide();
                        bell.text(data.length);
                        notificationHeader.text('您有'+data.length+'条未读消息');
                        notificationContent.html('');
                        notificationIds = [];
                        $.each(data,function (index,element) {
                            var aLink = document.createElement('a');
                            aLink.style = 'cursor:pointer';
                            var i = document.createElement('i');
                            $(i).addClass(element.textIconClass);
                            $(aLink).html(i.outerHTML + element.messageContent);
                            aLink.addEventListener('click',function(){
                                markAsReadNotification([element.notificationId],element.redirectRoute);
                            });
                            notificationIds.push(element.notificationId);
                            var li = document.createElement('li');
                            $(li).html(aLink);
                            notificationContent.append(li);
                        })
                    }
                })
            };
            @if(App::environment() != 'local')
                setInterval($.fn.fetchNotification,5000);
            @endif
            $.fn.fetchNotification();
        })
    </script>
</body>
</html>