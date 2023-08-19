@extends('adminlte::page')

@section('title', 'Document Add New')

@section('content_header')
    Document Add New
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('document_add_new') }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Title -->
                <div class="form-group row required">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Title</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputTitle" name="title" value="{{ old('title')}}" placeholder="Title" autofocus>
                        {!! $errors->first('title', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row required">
                    <label for="inputNote" class="col-sm-2 col-form-label">Note</label>
                    <div class="col-sm-10">
                        <textarea maxlength="1000" rows="5" class="form-control" id="inputNote" name="note" placeholder="Add Note....">{{ old('note') }}</textarea>
                        {!! $errors->first('note', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Station -->
                @if($userTypeID == 1) <!-- admin can select stations -->
                    <div class="form-group row required">
                        <label for="inputStation" class="col-sm-2 col-form-label">Station</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="inputStation" name="station_id">
                                <option value=''>-- Please select a station --</option>
                                @foreach ($stations as $station)
                                    @if ($station->id == old('station_id'))
                                        <option value="{{ $station->id }}" selected>{{ $station->title }}</option>
                                    @else
                                        <option value="{{ $station->id }}">{{ $station->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                            {!! $errors->first('station_id', '<span class="text-danger">:message</span>') !!}
                        </div>
                    </div>
                @else
                    <input type="hidden" name="station_id" value="{{ $userStationID }}" />
                @endif
                <!-- Volunteer -->
                <div class="form-group row required" id="divVolunteer" style="display:none;">
                    <label for="inputVolunteer" class="col-sm-2 col-form-label">Volunteer</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="inputVolunteer" name="uploaded_by">
                            <option value=''>-- Please select a volunteer --</option>
                            @foreach ($volunteers as $volunteer)
                                @if ($volunteer->id == old('uploaded_by'))
                                    <option value="{{ $volunteer->id }}" selected>{{ $volunteer->title }}</option>
                                @else
                                    <option value="{{ $volunteer->id }}">{{ $volunteer->title }}</option>
                                @endif
                            @endforeach
                        </select>
                        {!! $errors->first('uploaded_by', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>
            </div>

            <!-- Banner -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>File</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required">

                    <div class="col-sm-10">
                        <input type="file" accept="*" category="image" class="form-control-file" name="file"/>
                        {!! $errors->first('file', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-submit">Create</button>
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
    let inputStationInput = $('#inputStation')
    let inputVolunteerInput = $('#inputVolunteer')
    let divVolunteerInput = $('#divVolunteer')

    inputStationInput.select2({
        placeholder: 'Please select a station',
        width: '100%',
        allowClear: true
    })

    inputVolunteerInput.select2({
        placeholder: 'Please select a volunteer',
        width: '100%',
        allowClear: true
    })

    //get volunteer by station
    inputStationInput.change(function () {
        let station_id = inputStationInput.val()
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
                inputVolunteerInput.empty();
                inputVolunteerInput.append('<option value="" >-- Please select a volunteer --</option>');
                for(var i in volunteers){
                    inputVolunteerInput.append('<option value="'+volunteers[i].id+'">'+volunteers[i].name+'</option>');
                }
                divVolunteerInput.show();
            }
        })
    })
</script>
@endsection

@section('css')
@endsection
