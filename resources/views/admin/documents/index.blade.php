@extends('adminlte::page')

@section('title', 'Document List')

@section('content_header')
    Document List
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('document_list') }}
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
                            <th>Volunteer Name</th>
                            <th>Station Title</th>
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
                allowClear: true,
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
                filterVolunteerInput.val(null).trigger('change')
                filterDateInput.val('')
                filterStationInput.val(null).trigger('change')

                table.draw()
            })

            //get volunteer by station
            filterStationInput.change(function () {
                let station_id = filterStationInput.val()
                // getVolunteersByStation
                $.ajax({
                    url: "{{ route('admin.volunteers.getVolunteersByStation') }}",
                    type: 'get',
                    data: {
                        'station_id': station_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        let volunteers = data.volunteerWithStation;
                        $("#filterVolunteer").empty();
                        $("#filterVolunteer").append('<option value="" >-- Please select a volunteer --</option>');
                        for(var i in volunteers){
                            $("#filterVolunteer").append('<option value="'+volunteers[i].id+'">'+volunteers[i].name+'</option>');
                        }
                    }
                })
            })

            // datatables
            let trash = `trash=`+getUrlParameter('trash')
            let url = "{{ route('admin.documents.index', ':trash') }}"
            url = url.replace(':trash', trash);
            let deleteLabel = 'Delete';

            let showFilterText = '<i class="fas fa-chevron-circle-down"></i> Show Filters'
            let hideFilterText = '<i class="fas fa-chevron-circle-up"></i> Hide Filters'

            if (trash == 'trash=all') {
                deleteLabel = 'Destory';
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
                        window.location.href = "{{ route('admin.documents.create') }}"
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
                                'Volunteer Name',
                                'Station Title',
                                'Uploaded Date'
                            ]
                        ]

                        rows.forEach(function(row) {
                            data.push([
                                escapeRow(row.DT_RowIndex),
                                escapeRow(row.title),
                                escapeRow(row.volunteer_name),
                                escapeRow(row.station_title),
                                escapeRow(row.created_at),
                            ])
                        })

                        exportExcel(data, 'document List')
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
                    titleAttr: 'Delete selected',
                    action: function ( e, dt, node, config ) {
                        var data= table.rows( { selected: true }).data();
                        var sData=[];
                        for (var i=0; i < data.length ;i++){
                            sData.push(data[i]['id']);
                        }
                        if(sData.length > 0){

                            bootbox.confirm({
                                message: 'This selected document(s) will be '+ deleteLabel +'! Are you sure?',
                                size: 'small',
                                backdrop: true,
                                closeButton: false,
                                callback: function (result) {
                                    if (result) {
                                        let is_trash = `trash=`+getUrlParameter('trash')
                                        let delete_selected_url = "{{ route('admin.documents.deleteSelected', ':trash') }}"
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
                        window.location.href = '/documents';
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
                        window.location.href = '/documents?trash=all';
                    }
                })
            }

            let table = $('#datatable').DataTable({
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
                        let volunteerID = filterVolunteerInput.val()
                        let uploadedDate = filterDateInput.val()
                        let stationID = filterStationInput.val()

                        return $.extend( {}, '', d, {title, volunteerID, uploadedDate, stationID})
                    }
                },
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                dom: "B<'table_mt row'<'col-md-6'l><'col-md-6'f>>tr<'row'<'col-md-6'i><'col-md-6'p>>",
                buttons: buttons,
                columns: [
                    {orderable: false, searchable: false},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'title', name: 'title'},
                    {data: 'volunteer_name', name: 'volunteer_name'},
                    {data: 'station_title', name: 'station_title'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                columnDefs: [
                    { 'sort': false, "width": "2%", className: 'select-checkbox', targets: 0 },
                    { 'sort': false, "width": "5%", targets: 1 },
                    { 'sort': false, "width": "5%", targets: 6 },
                    {  className: "ellipsis50", targets: 2 },
                    {  className: "ellipsis", targets: [2,3] }
                ],
            })
        })
        //** Q/A Automate Attribbute ID Set **/
        $('#datatable').on( 'draw.dt', function () {
            $( ".downloadQA" ).each(function( index ) {
                 $( this ).attr('id', 'download_' + (index + 1)) ;
            });
            $( ".shareQA" ).each(function( index ) {
                //  $( this ).attr('id', 'share_' + (index + 1)) ;
            });
            $( ".privateQA" ).each(function( index ) {
                //  $( this ).attr('id', 'private_' + (index + 1)) ;
            });
            $( ".detailQA" ).each(function( index ) {
                 $( this ).attr('id', 'detail_' + (index + 1)) ;
            });
            $( ".activateQA" ).each(function( index ) {
                 $( this ).attr('id', 'activate_' + (index + 1)) ;
            });
            $( ".deactivateQA" ).each(function( index ) {
                //  $( this ).attr('id', 'deactivate_' + (index + 1)) ;
            });
        } );
        //** End Q/A Automate Attribbute ID Set **/

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

</style>
@endsection