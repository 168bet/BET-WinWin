@section('css')
    @include('Admin.layouts.datatables_css')
    <link rel="stylesheet" href="https://cdn.staticfile.org/select2-bootstrap-theme/0.1.0-beta.9/select2-bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
@endsection

<div class="col-md-12">
    {!! $dataTable->table(['width' => '100%','class' => 'table table-bordered table-hover dataTable','style' => 'text-align:center']) !!}
</div>

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.min.js"></script>
    @include('Admin.layouts.datatables_js')
    @include('Components.Ajax.WinwinAjax')
    {!! $dataTable->scripts() !!}
    <script src="https://unpkg.com/bootstrap-switch@3.3.4"></script>

@endsection