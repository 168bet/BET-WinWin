@section('css')
    @include('Carrier.layouts.datatables_css')
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-switch@3.3.4/dist/css/bootstrap3/bootstrap-switch.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/select2-bootstrap-theme/0.1.0-beta.9/select2-bootstrap.min.css">
    <link rel="stylesheet" href="{!! asset('daterangepicker/daterangepicker.css') !!}">
    <link rel="stylesheet" href="{!! asset('datepicker/datepicker3.css') !!}">
@endsection

<div class="col-md-12">
    <form action="" id="searchForm">
        <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-addon">
                    <span>代理账号</span>
                </div>
                <input class="form-control" style="width: 100%;" name="username" id="username" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-addon">
                    <span>姓名</span>
                </div>
                <input class="form-control" style="width: 100%;" name="realname" id="realname" value="">
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-addon">
                    <span>是否启用</span>
                </div>
                <select name="status" class="form-control select2" style="width: 100%;">
                    <option value="">不限</option>
                    <option value="1">正常</option>
                    <option value="0">已关闭</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="input-group input-group-sm">
                <button class="btn btn-primary btn-sm" type="submit">搜索</button>
            </div>
        </div>
    </form>
</div>

<div class="col-md-12">
    {!! $dataTable->table(['width' => '100%','class' => 'table table-bordered table-hover dataTable']) !!}
</div>

@section('scripts')
    @parent
    @include('Carrier.layouts.datatables_js')
    <script src="{{asset('js/vue.min.js')}}"></script>
    <script src="https://unpkg.com/bootstrap-switch@3.3.4"></script>
    {!! $dataTable->scripts() !!}
    @include('vendor.datatable.datatables_template')
    <script src="{!! asset('daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('daterangepicker/daterangepicker.js') !!}"></script>
    <script src="{!! asset('datepicker/bootstrap-datepicker.js') !!}"></script>
@endsection

@section('footer')
    @include('vendor.alert.default')
@endsection