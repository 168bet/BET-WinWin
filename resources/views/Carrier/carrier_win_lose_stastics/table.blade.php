@section('css')
    @include('Carrier.layouts.datatables_css')
@endsection

<div class="col-md-12">
    {!! $dataTable->table(['width' => '100%','class' => 'table table-bordered table-hover dataTable']) !!}
</div>

@section('scripts')
    @include('Carrier.layouts.datatables_js')
    {!! $dataTable->scripts() !!}
@endsection