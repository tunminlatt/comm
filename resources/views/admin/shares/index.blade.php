@extends('adminlte::page')

@section('title', 'Share List')

@section('content_header')
    Share List
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('share_list') }}
@endsection

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
                        <label for="filterVolunteer">Volunteer</label>
                        <select class="form-control" id="filterVolunteer">
                            <option value=''>-- Please select a volunteer --</option>
                            @foreach ($volunteers as $volunteer)
                                <option value="{{ $volunteer->id }}">{{ $volunteer->name }}</option>
                            @endforeach
                        </select>
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
                <table id="datatable" class="table table-striped nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Duration</th>
                            <th>Volunteer Name</th>
                            @if($userTypeID == 1) <!-- admin can see -->
                            <th>Station Title</th>
                            @endif
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
    <script>
        $(function () {
            // prepare variables
            var userTypeID = {!! $userTypeID !!}
            let comboTree, modalBox
            let filterTileInput = $('#filterTitle')
            let filterVolunteerInput = $('#filterVolunteer')
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

            filterVolunteerInput.select2({
                placeholder: 'Please select a volunteer',
                width: '100%',
                allowClear: true
            })

            filterStationInput.select2({
                placeholder: 'Please select a station',
                width: '100%',
                allowClear: true
            })

            // datatables
            let url = "{{ route('admin.shares.index') }}"
            let showFilterText = '<i class="fas fa-chevron-circle-down"></i> Show Filters'
            let hideFilterText = '<i class="fas fa-chevron-circle-up"></i> Hide Filters'

            if (userTypeID == 1) {
                let table = $('#datatable').DataTable({
                    order: [[ 5, 'desc' ]],
                    scrollX: true,
                    scrollY: false,
                    colReorder: true,
                    fixedHeader: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url,
                        data: function ( d ) {
                            // prepare variables
                            let title = $('#filterTitle').val()
                            let volunteerID = filterVolunteerInput.val()
                            let uploadedDate = filterDateInput.val()
                            let stationID = filterStationInput.val()

                            return $.extend( {}, d, {title, volunteerID, uploadedDate, stationID})
                        }
                    },
                    lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                    dom: "B<'table_mt row'<'col-md-6'l><'col-md-6'f>>tr<'row'<'col-md-6'i><'col-md-6'p>>",
                    buttons: [
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
                                        'Duration',
                                        'Volunteer Name',
                                        'Station Title',
                                        'Uploaded Date',
                                    ]
                                ]

                                rows.forEach(function(row) {
                                    data.push([
                                        escapeRow(row.DT_RowIndex),
                                        escapeRow(row.title),
                                        escapeRow(row.duration),
                                        escapeRow(row.volunteer_name),
                                        escapeRow(row.station_title),
                                        escapeRow(row.created_at),
                                    ])
                                })

                                exportExcel(data, 'Share List')
                            }
                        }
                    ],
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'title', name: 'title'},
                        {data: 'duration', name: 'duration'},
                        {data: 'volunteer_name', name: 'volunteer_name'},
                        {data: 'station_title', name: 'station_title'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    columnDefs: [
                        { 'sort': false, "width": "5%", 'targets': 0 },
                        {  className: "ellipsis50", targets: 1 },
                        {  className: "ellipsis", targets: [3,4] }
                    ],
                })
            } else {
                let table = $('#datatable').DataTable({
                    order: [[ 4, 'desc' ]],
                    scrollX: true,
                    scrollY: false,
                    colReorder: true,
                    fixedHeader: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url,
                        data: function ( d ) {
                            // prepare variables
                            let title = $('#filterTitle').val()
                            let volunteerID = filterVolunteerInput.val()
                            let uploadedDate = filterDateInput.val()
                            let stationID = filterStationInput.val()

                            return $.extend( {}, d, {title, volunteerID, uploadedDate, stationID})
                        }
                    },
                    lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                    dom: "B<'table_mt row'<'col-md-6'l><'col-md-6'f>>tr<'row'<'col-md-6'i><'col-md-6'p>>",
                    buttons: [
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
                                        'Duration',
                                        'Volunteer Name',
                                        'Uploaded Date',
                                    ]
                                ]

                                rows.forEach(function(row) {
                                    data.push([
                                        escapeRow(row.DT_RowIndex),
                                        escapeRow(row.title),
                                        escapeRow(row.duration),
                                        escapeRow(row.volunteer_name),
                                        escapeRow(row.created_at)
                                    ])
                                })

                                exportExcel(data, 'Share List')
                            }
                        }
                    ],
                    columns: [

                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'title', name: 'title'},
                        {data: 'duration', name: 'duration'},
                        {data: 'volunteer_name', name: 'volunteer_name'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    columnDefs: [
                        { 'sort': false, "width": "5%", 'targets': 0 },
                        {  className:"ellipsis50", targets: 1 },
                    ],
                })
            }
        })

        //** Q/A Automate Attribbute ID Set **/
        $('#datatable').on( 'draw.dt', function () {
            $( ".downloadQA" ).each(function( index ) {
                 $( this ).attr('id', 'download_' + (index + 1)) ;
            });
            $( ".privateQA" ).each(function( index ) {
                //  $( this ).attr('id', 'private_' + (index + 1)) ;
            });
            $( ".detailQA" ).each(function( index ) {
                 $( this ).attr('id', 'detail_' + (index + 1)) ;
            });
        } );
        //** End Q/A Automate Attribbute ID Set **/

        $('#btnSearchTable').click(function () {
            $('#datatable').DataTable().draw()
        })

        $('#btnResetTable').click(function () {
            filterTileInput.val('')
            filterVolunteerInput.val(null).trigger('change')
            filterDateInput.val('')
            filterStationInput.val(null).trigger('change')

            $('#datatable').DataTable().draw()
        })

        function noteDownload(filename, text) {
            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);
        }

        function onClickNote(id){
            // Generate download of hello.txt file with some content
            var text = document.getElementById("note-val"+id).value;
            var title = document.getElementById("title-val"+id).value;

            var filename = title+" Note.txt";

            noteDownload(filename, text);
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
</style>
@endsection