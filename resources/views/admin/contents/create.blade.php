@extends('adminlte::page')

@section('title', 'Content Add New')

@section('content_header')
    Content Add New
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('content_add_new') }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.audios.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Title -->
                <div class="form-group row required">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputTitle" name="title" value="{{ old('title')}}" placeholder="Title" autofocus>
                        {!! $errors->first('title', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row required">
                    <label for="inputNote" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea maxlength="5000" rows="5" class="form-control" id="inputNote" name="note" placeholder="Add Note....">{{ old('note') }}</textarea>
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
                <h3 class="card-title"><b>Image</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required">
                    <label for="inputBanner" class="col-sm-2 col-form-label">
                        <kbd>2MB, Image Only</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="image/*" category="image" class="form-control-file" id="inputImage" name="banner"/>
                        {!! $errors->first('banner', '<span class="text-danger">:message</span>') !!}
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            <!-- Recording -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>Audio</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required">
                    <label for="inputRecording" class="col-sm-2 col-form-label">
                        <kbd>1GB, Audio Only</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="audio/*" category="audio" class="form-control-file" id="inputRecording" name="recording"/>
                        {!! $errors->first('recording', '<span class="text-danger">:message</span>') !!}

                        <input type="hidden" name="duration" value=""/>
                        <audio id="audio"></audio>
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            <!-- Video -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>Video</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required">
                    <label for="inputVideo" class="col-sm-2 col-form-label">
                        <kbd>1GB, Video Only</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="video/*" category="video" class="form-control-file" id="inputVideo" name="video"/>
                        {!! $errors->first('video', '<span class="text-danger">:message</span>') !!}

                        {{-- <input type="hidden" name="video_duration" value=""/>
                        <audio id="video"></audio> --}}
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-submit">Create</button>
                    <a href="{{ route('admin.audios.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script src="{{asset('js/jquery.form.js')}}"></script>
<script src="{{asset('js/formAjax.js')}}"></script>
<!-- include tinymce js -->
<script src="{{asset('js/tinymce.min.js')}}"></script>
<script src="{{asset('js/jquery.tinymce.min.js')}}"></script>

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

    $('textarea#inputNote').tinymce({
        height: 200,
        menubar: false,
        plugins: [
            'advlist lists link charmap',
            'help wordcount'
        ],
        toolbar: 'undo redo | formatselect | link bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });
</script>
@endsection

@section('css')
<style>
    .tox-notifications-container {display: none !important;}
    .tox-statusbar__branding {display: none !important;}
</style>
@endsection
