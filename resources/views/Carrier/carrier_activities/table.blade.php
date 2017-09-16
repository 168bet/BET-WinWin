@section('css')
    @include('Components.Editor.style')
    @include('Carrier.layouts.datatables_css')
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-switch@3.3.4/dist/css/bootstrap3/bootstrap-switch.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/select2-bootstrap-theme/0.1.0-beta.9/select2-bootstrap.min.css">
@endsection



<div class="col-md-12">
</div>

<div class="col-md-12">
    {!! $dataTable->table(['width' => '100%','class' => 'table table-bordered table-hover dataTable']) !!}
</div>

@section('scripts')

    <script src="{{asset('js/vue.min.js')}}"></script>
    @include('Carrier.layouts.datatables_js')
    @include('Components.Editor.scripts')
    <script src="https://unpkg.com/bootstrap-switch@3.3.4"></script>
    {!! $dataTable->scripts() !!}
    @include('vendor.datatable.datatables_template')
@endsection

@section('footer')
    @include('vendor.alert.default')
@endsection