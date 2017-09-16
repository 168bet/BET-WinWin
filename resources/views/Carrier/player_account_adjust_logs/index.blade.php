@extends('Carrier.layouts.app')
@section('css')
    @parent
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
@endsection
@section('content')
    <section class="content-header">
        <div class="left">
        </div>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary color-palette-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-tag"></i> 会员资金调整记录</h3>
                <div class="box-tools">
                    <ul class="pull-right pagination-sm pagination">
                    </ul>
                </div>
            </div>
            <div class="box-body">
                @include('Carrier.player_account_adjust_logs.table')
                <h5 class="pull-left">
                    <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" onclick="{!! TableScript::addOrEditModalShowEventScript(route('playerAccountAdjustLogs.passPlayerAccountEdit')) !!}">批量资金调整</a>
                </h5>
            </div>
            <div class="overlay" id="overlay" style="display: none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>

    @include('Components.player_edit_modal')


@endsection
