@extends('adminlte::page')

@section('title', 'Volunteer List')

@section('content_header')
    Volunteer List
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('volunteer_list') }}
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="datatable" class="table table-striped nowrap" width="100%;">
                <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Phone</th>
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
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script>
        $(function () {
            let trash = `trash=`+getUrlParameter('trash')
            let url = "{{ route('admin.volunteers.index', ':trash') }}"
            url = url.replace(':trash', trash);
            let deleteLabel = 'Delete';

            if (trash == 'trash=all') {
                deleteLabel = 'Destory';
            }

            let buttons = [
                {
                    text: '<i class="fas fa-plus"></i> Add New',
                    attr:  {
                        title: 'Add New',
                        id: 'addNew'
                    },
                    className: 'btn btn-success btn-sm',
                    action: function ( e, dt, node, config ) {
                        window.location.href = "{{ route('admin.volunteers.create') }}"
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
                                'Phone',
                                'Email',
                                'Station Title',
                                'Created Date',
                            ]
                        ]

                        rows.forEach(function(row) {
                            data.push([
                                escapeRow(row.DT_RowIndex),
                                escapeRow(row.name),
                                escapeRow(row.phone),
                                escapeRow(row.email),
                                escapeRow(row.station_title),
                                escapeRow(row.created_at),
                            ])
                        })

                        exportExcel(data, 'Volunteer List')
                    }
                },
                {
                    text: '<i class="far fa-check-square"></i> Select All',
                    className: 'btn btn-warning btn-sm',
                    titleAttr: 'select all',
                    action: function() {
                        table.rows({
                        page: 'current'
                        }).select();
                    }
                },
                {
                    text: '<i class="far fa-square"></i> Deselect All',
                    className: 'btn btn-sm buttons-select-none',
                    titleAttr: 'unselect all',
                    action: function() {
                        table.rows({
                        page: 'current'
                        }).deselect();
                    }
                },
                {
                    text: '<i class="fa fa-info"></i> '+ deleteLabel +' Selected',
                    className: 'btn btn-danger btn-sm',
                    titleAttr: 'delete selected',
                    action: function ( e, dt, node, config ) {
                        var data= table.rows( { selected: true }).data();
                        var sData=[];
                        for (var i=0; i < data.length ;i++){
                            sData.push(data[i]['id']);
                        }
                        if(sData.length > 0){
                            bootbox.confirm({
                                message: 'This selected volunteer(s) and it related audio(s) will be '+ deleteLabel +'! Are you sure?',
                                size: 'small',
                                backdrop: true,
                                closeButton: false,
                                callback: function (result) {
                                    if (result) {
                                        let is_trash = `trash=`+getUrlParameter('trash')
                                        let delete_selected_url = "{{ route('admin.volunteers.deleteSelected', ':trash') }}"
                                        delete_selected_url = delete_selected_url.replace(':trash', is_trash);
                                        $.post( delete_selected_url, { '_token': '{{ csrf_token() }}', 'ids' : sData })
                                        .done(function( data ) {
                                            loadingModal()
                                            if(data == 'success'){
                                                location.reload();
                                            }else{
                                                location.reload();
                                            }
                                        });
                                    }
                                }
                            })

                        }else{
                            bootbox.alert('No Selected row(s) !');
                        }
                    }
                }
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
                        window.location.href = '/volunteers';
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
                    action: function (e, dt, node, config) {
                        window.location.href = '/volunteers?trash=all';
                    }
                })
            }

            let table = $('#datatable').DataTable(
            {
                order: [[ 5, 'desc' ]],
                scrollX: true,
                scrollY: false,
                colReorder: true,
                fixedHeader: true,
                processing: true,
                serverSide: true,
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                ajax: url,
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                dom: "B<'table_mt row'<'col-md-6'l><'col-md-6'f>>tr<'row'<'col-md-6'i><'col-md-6'p>>",
                buttons: buttons,
                columns: [
                    {orderable: false, searchable: false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'phone', name: 'phone'},
                    {data: 'email', name: 'email'},
                    {data: 'station_title', name: 'station_title'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    { 'sort': false, "width": "2%", className: 'select-checkbox', targets: 0 },
                    { 'sort': false, "width": "5%", 'targets': 0 },
                    {  className: "ellipsis", targets: [1, 4] }
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
