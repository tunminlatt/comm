@extends('adminlte::page')

@section('title', 'Station List')

@section('content_header')
    Station List
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('station_list') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatable" class="table table-striped nowrap" width="100%;">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Title</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function () {
            let trash = `trash=`+getUrlParameter('trash')
            let url = "{{ route('admin.stations.index', ':trash') }}"
            url = url.replace(':trash', trash);

            let buttons = [
                {
                    text: '<i class="fas fa-plus"></i> Add New',
                    attr:  {
                        title: 'Add New',
                        id: 'addNew'
                    },
                    className: 'btn btn-success btn-sm',
                    action: function ( e, dt, node, config ) {
                        window.location.href = "{{ route('admin.stations.create') }}"
                    }
                },
                {
                    text: '<i class="fas fa-file-excel"></i> CSV Export',
                    className: 'btn btn-dark btn-sm',
                    action: function ( e, dt, node, config ) {
                        let rows = dt.ajax.json().data
                        let data = [
                            [
                                'No',
                                'Title',
                                'Phone',
                                'Email',
                                'Created Date',
                            ]
                        ]

                        rows.forEach(function(row) {
                            data.push([
                                escapeRow(row.DT_RowIndex),
                                escapeRow(row.title),
                                escapeRow(row.phone),
                                escapeRow(row.email),
                                escapeRow(row.created_at)
                            ])
                        })

                        exportExcel(data, 'Station List')
                    }
                },
            ]

            if(trash == 'trash=all'){
                buttons.push({
                    text: '<i class="fas fa-arrow-left"></i> <b>Back</b>',
                    attr:  {
                        title: 'Back',
                        id: 'back',
                    },
                    className: 'btn btn-primary btn-sm',
                    action: function ( e, dt, node, config ) {
                        window.location.href = '/stations';
                    }
                })
            }else{
                buttons.push({
                    text: '<i class="fas fa-trash"></i> <b>Trash</b>',
                    attr:  {
                        title: 'Trash',
                        id: 'trash',
                    },
                    className: 'btn btn-primary btn-sm',
                    action: function ( e, dt, node, config ) {
                        window.location.href = '/stations?trash=all';
                    }
                })
            }

            let table = $('#datatable').DataTable(
            {
                order: [[ 4, 'desc' ]],
                scrollX: true,
                scrollY: false,
                colReorder: true,
                fixedHeader: true,
                processing: true,
                serverSide: true,
                ajax: url,
                data: { 'trash' : trash},
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                dom: "B<'table_mt row'<'col-md-6'l><'col-md-6'f>>tr<'row'<'col-md-6'i><'col-md-6'p>>",
                buttons: buttons,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'title', name: 'title'},
                    {data: 'phone', name: 'phone'},
                    {data: 'email', name: 'email'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    { 'sort': false, "width": "5%", 'targets': 0 },
                    {  className: "ellipsis", targets: 1 }
                ],
            })
        })
        //** Q/A Automate Attribbute ID Set **/
        $('#datatable').on( 'draw.dt', function () {
            $( ".editQA" ).each(function( index ) {
                 $( this ).attr('id', 'edit_' + (index + 1)) ;
            });
            $( ".activateQA" ).each(function( index ) {
                 $( this ).attr('id', 'activate_' + (index + 1)) ;
            });
            $( ".deactivateQA" ).each(function( index ) {
                //  $( this ).attr('id', 'deactivate_' + (index + 1)) ;
            });
        } );
        //** End Q/A Automate Attribbute ID Set **/
    </script>
@endsection

@section('css')
@endsection
