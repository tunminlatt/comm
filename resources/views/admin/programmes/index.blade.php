@extends('adminlte::page')

@section('title', 'Programme List')

@section('title', 'YYAT List')
@section('content_header')
YYAT List
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('programme_list') }}
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
                        <label for="filterTitle">Title</label>
                        <input type="text" class="form-control" id="filterTitle" placeholder="Title">
                    </div>

                    <div class="col mb-3">
                        <label for="filterDate">Uploaded Date</label>
                        <input type="text" class="form-control" id="filterDate" placeholder="Uploaded Date">
                    </div>

                    @if($userTypeID == 1) <!-- admin can filter stations -->
                        <div class="col mb-3">
                            <label for="filterStation">Station</label>
                            <select class="form-control" id="filterStation">
                                <option value=''>-- Please select a station --</option>
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}">{{ $station->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="col text-center">
                    <button id="btnSearchTable" class="btn btn-success" type="button">Search</button>
                    <button id="btnResetTable" class="btn btn-dark" type="reset">Reset</button>
                </div>

                <hr/>
            </form>
            <!-- Datatables -->
            <div class="table-responsive">
                <table id="datatable" class="table table-striped nowrap" width="100%;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Duration</th>
                            <th>Station Title</th>
                            <th>Created Date</th>
                            <th>Released Date</th>
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
            let url = "{{ route('admin.programmes.index', ':trash') }}"
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
                    text: '<i class="fas fa-plus"></i> Add New',
                    attr:  {
                        title: 'Add New',
                        id: 'addNew'
                    },
                    className: 'btn btn-success btn-sm',
                    action: function ( e, dt, node, config ) {
                        window.location.href = "{{ route('admin.programmes.create') }}"
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
                                'Duration',
                                'Station Title',
                                'Created Date',
                                'Released Date'
                            ]
                        ]

                        rows.forEach(function(row) {
                            data.push([
                                escapeRow(row.DT_RowIndex),
                                escapeRow(row.title),
                                escapeRow(row.duration),
                                escapeRow(row.station_title),
                                escapeRow(row.created_at),
                                escapeRow(row.released_at)
                            ])
                        })

                        exportExcel(data, 'Programme List')
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
                                message: 'This selected programme(s) will be '+ deleteLabel +'! Are you sure?',
                                size: 'small',
                                backdrop: true,
                                closeButton: false,
                                callback: function (result) {
                                    if (result) {
                                        let is_trash = `trash=`+getUrlParameter('trash')
                                        let delete_selected_url = "{{ route('admin.programmes.deleteSelected', ':trash') }}"
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
                        window.location.href = '/programmes';
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
                        window.location.href = '/programmes?trash=all';
                    }
                })
            }

            let table = $('#datatable').DataTable(
            {
                order: [[ 5, 'desc' ]],
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

                        return $.extend( {}, d, {title, uploadedDate, stationID})
                    }
                },
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                dom: "B<'table_mt row'<'col-md-6'l><'col-md-6'f>>tr<'row'<'col-md-6'i><'col-md-6'p>>",
                buttons: buttons,
                columns: [
                    {orderable: false, searchable: false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'title', name: 'title'},
                    {data: 'duration', name: 'duration'},
                    {data: 'station_title', name: 'station_title'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'released_at', name: 'released_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    { 'sort': false, "width": "2%", className: 'select-checkbox', targets: 0 },
                    { 'sort': false, "width": "5%", 'targets': 0 },
                    {  className:"ellipsis50", targets: 1 },
                    {  className:"ellipsis", targets: 3 }
                ],
            })
        })
        //** Q/A Automate Attribbute ID Set **/
        $('#datatable').on( 'draw.dt', function () {
            $( ".approveQA" ).each(function( index ) {
                 $( this ).attr('id', 'approve_' + (index + 1)) ;
            });
            $( ".rejectQA" ).each(function( index ) {
                 $( this ).attr('id', 'reject_' + (index + 1)) ;
            });
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

        //** Approve with schedule **/
        function form_approve (id ,schedule_lable){
            if(schedule_lable == null){
                var button_text = 'Approve'
                var schedule_value = moment(new Date ).format('DD MMMM YYYY HH:mm')
                var scheduleType = 0
            }else{
                var button_text = 'Update'
                var schedule_value = schedule_lable
                var scheduleType = 1
            }

            swal.fire({
                title: "Choose Schedule Time...",
                html:`
                    <br>
                    <div class="row">
                        <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                            <input size="16" id="schedule_time" type="text" readonly class="form-control form_datetime" placeholder="Choose Schedule Time...">
                            </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <br>
                    `,
                type: "warning",
                onOpen: function() {
                    $(".form_datetime").datetimepicker({
                        format: "dd MM yyyy hh:ii",
                        autoclose: true,
                        todayBtn: true,
                    });
                    $('#schedule_time').val(schedule_value)
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: button_text
            }).then((result) => {
                if (result.value) {
                    let schedule_time = $('#schedule_time').val()
                    if(schedule_time){
                        $('#schedule_'+id).val(schedule_time);
                        $('#scheduleType_'+id).val(scheduleType);
                        $('#form_approve_'+id).submit();
                    }else{
                        $('#schedule_'+id).val(schedule_value);
                        $('#scheduleType_'+id).val(scheduleType);
                        $('#form_approve_'+id).submit();
                    }
                }
            })
        }
        //** End Approve with schedule **/

    </script>
@endsection

@section('css')
@endsection
