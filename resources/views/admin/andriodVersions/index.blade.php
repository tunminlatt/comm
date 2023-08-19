@extends('adminlte::page')

@section('title', 'Andriod Version')

@section('content_header')
    Andriod Version (Developer Only)
@stop

@section('breadcrumbs')
    
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <!-- Datatables -->
            <div class="table-responsive">
                <table id="datatable" class="table table-striped nowrap" width="100%;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Latest Version Code </th>
                            <th>Require Force Update</th>
                            <th>Min Version Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script>
        $(function () {

            // datatables
            let url = "{{ route('admin.andriodVersions.index') }}"
            let table = $('#datatable').DataTable(
            {
                order: [[ 4, 'desc' ]],
                scrollX: false,
                scrollY: false,
                colReorder: true,
                fixedHeader: true,
                processing: true,
                serverSide: true,
                ajax: url,
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'latest_version_code ', name: 'latest_version_code '},
                    {data: 'require_force_update', name: 'require_force_update'},
                    {data: 'min_version_code', name: 'min_version_code'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    { 'sort': false, "width": "5%", 'targets': 0 },
                    {  className: "ellipsis", targets: 1 }
                ],
            })
        })
    </script>
@endsection

@section('css')
@endsection