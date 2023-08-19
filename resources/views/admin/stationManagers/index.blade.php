@extends('adminlte::page')

@section('title', 'Station Manager List')

@section('content_header')
    Station Manager List
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('station_manager_list') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatable" class="table table-striped nowrap" width="100%;">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Station Title</th>
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
            let url = "{{ route('admin.stationManagers.index', ':trash') }}"
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
                        window.location.href = "{{ route('admin.stationManagers.create') }}"
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
                                'Name',
                                'Email',
                                'Station Title',
                                'Created Date',
                            ]
                        ]

                        rows.forEach(function(row) {
                            data.push([
                                escapeRow(row.DT_RowIndex),
                                escapeRow(row.name),
                                escapeRow(row.email),
                                escapeRow(row.station_title),
                                escapeRow(row.created_at),
                            ])
                        })

                        exportExcel(data, 'Station Manager List')
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
                        window.location.href = '/stationManagers';
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
                        window.location.href = '/stationManagers?trash=all';
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
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                dom: "B<'table_mt row'<'col-md-6'l><'col-md-6'f>>tr<'row'<'col-md-6'i><'col-md-6'p>>",
                buttons: buttons,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'station_title', name: 'station_title'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    { 'sort': false, "width": "5%", 'targets': 0 },
                    {  className: "ellipsis", targets: [1,3] }
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