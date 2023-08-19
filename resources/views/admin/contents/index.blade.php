@extends('adminlte::page')

@section('title', 'Content List')

@section('content_header')
    Content List
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('content_list') }}
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <!-- Filters -->
            <form id="datatable-filters" class="collapse">
                <div class="form-row">
                    <div class="col mb-3">
                        <label for="filterTitle">Type</label>
                        <input type="text" class="form-control" id="filterTitle" placeholder="Title">
                    </div>

                @if($userTypeID == 1) <!-- admin can filter stations -->
                    <div class="col mb-3">
                        <label for="filterStation">Media</label>
                        <select class="form-control" id="filterStation">
                            <option value=''>-- Please select a station --</option>
                            @foreach ($stations as $station)
                                <option value="{{ $station->id }}">{{ $station->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col mb-3">
                        <label for="filterDate">Uploaded Date</label>
                        <input type="text" class="form-control" id="filterDate" placeholder="Uploaded Date">
                    </div>

                </div>

                <div class="col text-center">
                    <button id="btnSearchTable" class="btn btn-success" type="button">Search</button>
                    <button id="btnResetTable" class="btn btn-dark" type="reset">Reset</button>
                </div>

                <hr/>
            </form>
            <!-- Datatables -->
            <div class="table-responsive">
                <table id="datatable" class="table table-striped" width="100%;">
                    <thead>
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th rowspan="10px" style="width:30%">Description</th>
                        <th>Journalist</th>
                        <th>Type</th>
                        <th>Media</th>
                        <th>Uploaded Date</th>
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
            // prepare variables
            let comboTree, modalBox
            let filterTileInput = $('#filterTitle')
            let filterDateInput = $('#filterDate')
            let filterStationInput = $('#filterStation')
            let dateFormat = 'YYYY-MM-DD'

            // filters
            filterDateInput.daterangepicker({
                showDropdowns: true,
                autoApply: true,
                autoUpdateInput: false,
                linkedCalendars: false,
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format(dateFormat) + ' to ' + picker.endDate.format(dateFormat));
            })

            filterStationInput.select2({
                placeholder: 'Please select a station',
                width: '100%',
                allowClear: true
            })

            $('#btnSearchTable').click(function () {
                table.draw()
            })

            $('#btnResetTable').click(function () {
                filterTileInput.val('')
                filterDateInput.val('')
                filterStationInput.val(null).trigger('change')

                table.draw()
            })

            // datatables
            let trash = `trash=`+getUrlParameter('trash')
            let url = "{{ route('admin.contents.index', ':trash') }}"
            url = url.replace(':trash', trash);
            let deleteLabel = 'delete';

            let showFilterText = '<i class="fas fa-chevron-circle-down"></i> Show Filters'
            let hideFilterText = '<i class="fas fa-chevron-circle-up"></i> Hide Filters'

            if (trash == 'trash=all') {
                deleteLabel = 'destory';
            }

            let buttons = [
                {
                    text: showFilterText,
                    className: 'btn btn-default btn-sm',
                    action: function ( e, dt, node, config ) {
                        $('#datatable-filters').slideToggle( "slow" )
                        let newLabel = (config.text == showFilterText) ? hideFilterText : showFilterText
                        table.button(0).text(newLabel)
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
                                'Station Title',
                                'Description',
                                'Uploaded Date'
                            ]
                        ]

                        rows.forEach(function(row) {
                            data.push([
                                escapeRow(row.DT_RowIndex),
                                escapeRow(row.title),
                                escapeRow(row.station_title),
                                escapeRow(row.description),
                                escapeRow(row.created_at),
                            ])
                        })

                        exportExcel(data, 'Content List')
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
                                message: 'This selected content(s) will be '+ deleteLabel +'! Are you sure?',
                                size: 'small',
                                backdrop: true,
                                closeButton: false,
                                callback: function (result) {
                                    if (result) {
                                        let is_trash = `trash=`+getUrlParameter('trash')
                                        let delete_selected_url = "{{ route('admin.contents.deleteSelected', ':trash') }}"
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
                        window.location.href = '/contents';
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
                        window.location.href = '/contents?trash=all';
                    }
                })
            }

            let table = $('#datatable').DataTable({
                order: [[ 6, 'desc' ]],
                scrollX: true,
                scrollY: false,
                colReorder: true,
                processing: true,
                serverSide: true,
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                ajax: {
                    url,
                    data: function ( d ) {
                        // prepare variables
                        let title = $('#filterTitle').val()
                        let uploadedDate = filterDateInput.val()
                        let stationID = filterStationInput.val()

                        return $.extend( {}, '', d, {title, uploadedDate, stationID})
                    }
                },
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                dom: "B<'table_mt row'<'col-md-6'l><'col-md-6'f>>tr<'row'<'col-md-6'i><'col-md-6'p>>",
                buttons: buttons,
                columns: [
                    {orderable: false, searchable: false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'description', name: 'description'},
                    {data: 'journalist', name: 'journalist'},
                    {data: 'title', name: 'title'},
                    {data: 'station_title', name: 'station_title'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    { 'sort': false, "width": "2%", className: 'select-checkbox', targets: 0 },
                    { 'sort': false, "width": "5%", targets: 1 }
                ],
            })
        })

        function downloadFile(urlToSend, title) {
            $.ajax({
                url: urlToSend,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (data) {
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(data);
                    a.href = url;
                    a.download = title;
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                }
            });
        }
    </script>
@endsection

@section('css')
    <style>
        .dropdown>.dropdown-menu {
            top: 200%;
            transition: 0.3s all ease-in-out;
        }

        .dropdown:hover>.dropdown-menu {
            display: block;
            top: 100%;
        }

        .dropdown>.dropdown-toggle:active {
            /*Without this, clicking will make it sticky*/
            pointer-events: none;
        }
        .selected{
            background-color: #9e9e9e !important;
        }
        .buttons-select-all{
            background-color: #ff1818 !important;
        }

        table#datatable tbody td{
            height:28px;
            overflow:hidden;
            word-wrap:break-word;
        }
    </style>
@endsection
