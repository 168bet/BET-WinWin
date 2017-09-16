@section('css')
    @include('Carrier.layouts.datatables_css')
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-switch@3.3.4/dist/css/bootstrap3/bootstrap-switch.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/select2-bootstrap-theme/0.1.0-beta.9/select2-bootstrap.min.css">
@endsection


<div class="col-md-12">
    {!! $dataTable->table(['width' => '100%','class' => 'table table-bordered table-hover dataTable']) !!}
</div>

@section('scripts')
    @parent
    <script src="https://unpkg.com/bootstrap-switch@3.3.4"></script>
    @include('Carrier.layouts.datatables_js')
    {!! $dataTable->scripts() !!}
    @include('vendor.datatable.datatables_template')
    <script>

        $(function(){

            $(document).on('click','.group_edit_link',function(e){
                e.preventDefault();
                var id = $(this).attr('data-id');
                console.log(id);
                {!! TableScript::addOrEditModalShowEventScript(route('carrierGamePlats.edit',59)) !!}
            });
        })



    </script>
@endsection

@section('footer')
    @include('vendor.alert.default')
@endsection
