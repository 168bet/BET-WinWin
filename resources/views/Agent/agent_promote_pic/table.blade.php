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
                    <span>广告名</span>
                </div>
                <input type="text" name="search[value]" class="form-control pull-right">
                <input type="hidden" name="search[regex]" value="false">
            </div>
        </div>
        <div class="col-md-2">
            <div class="input-group input-group-sm">
                <button class="btn btn-primary btn-md" type="submit">搜索</button>
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
@endsection

@section('footer')
    @include('vendor.alert.default')
@endsection