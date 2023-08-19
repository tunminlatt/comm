@extends('adminlte::page')

@section('title', 'YYAT Add New')

@section('content_header')
YYAT Add New
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('programme_add_new') }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.programmes.store', ['audio_id' => $audioID]) }}" enctype="multipart/form-data">
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Title -->
                <div class="form-group row required">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Title</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputTitle" name="title" value="{{ old('title', returnIfExist($audio, 'title')) }}" placeholder="Title" autofocus>
                        {!! $errors->first('title', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row required">
                    <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea maxlength="5000" rows="5" class="form-control" id="inputDescription" name="description" placeholder="Description">{{ old('description') }}</textarea>
                        {!! $errors->first('description', '<span class="text-danger">:message</span>') !!}
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
                                    @if ($station->id == old('station_id', returnIfExist($audio, 'station_id')))
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

                <!-- Type -->
                <div class="form-group row required">
                    <label for="inputType" class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-10">
                        {{-- <textarea maxlength="5000" rows="5" class="form-control" id="inputType" name="description" placeholder="Description">{{ old('description') }}</textarea> --}}
                        <select name="type" class="form-control" id="inputType">
                            <option value=""> --- Select Content Type --- </option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                            <option value="photo">Photo</option>
                            <option value="file">File</option>
                        </select>
                        {!! $errors->first('type', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>Content<span class="text-danger">*</span></b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required">
                    <label for="inputBanner" class="col-sm-2 col-form-label">
                        <kbd>Audio, Video, Photo or File (1GB only)</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="" class="form-control-file" id="content" name="content[]"/>
                        {!! $errors->first('content', '<span class="text-danger">:message</span>') !!}
                        <input type="hidden" name="duration" value="{{ returnIfExist($audio, 'duration') }}"/>
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            <!-- Thumb Nail -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>Thumbnail</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label for="thumbNail" class="col-sm-2 col-form-label">
                        <kbd>Photo (2MB only)</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="image/*" class="form-control-file" id="thumbNail" name="thumbNail"/>
                        {!! $errors->first('thumbNail', '<span class="text-danger">:message</span>') !!}
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            {{-- <!-- Recording -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>Audio <span class="text-danger">*</span></b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required">
                    <label for="inputRecording" class="col-sm-2 col-form-label">
                        <kbd>1GB, Audio Only</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="audio/*" category="audio" class="form-control-file" id="inputRecording" name="recording"/>
                        {!! $errors->first('recording', '<span class="text-danger">:message</span>') !!}

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />
                        <input type="hidden" name="duration" value="{{ returnIfExist($audio, 'duration') }}"/>
                        <audio id="audio"></audio>
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div> --}}

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-submit">Create</button>
                    <a href="{{ route('admin.programmes.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
    let banners = @json($banners);
    let recordings = @json($recordings);
    let inputStationInput = $('#inputStation')

    inputStationInput.select2({
        placeholder: 'Please select a station',
        width: '100%',
        allowClear: true
    })

    $(function () {
        $('#inputType').change(function(e) {
            let type = $('#inputType').val()

            if(type === 'photo') {
                $('input#content').attr('accept', 'image/*')
                $('input#content').attr('multiple', '')
                $('input#content').attr('category', 'image')
            } else if(type === 'video') {
                $('input#content').attr('accept', 'video/*')
                $('input#content').removeAttr('multiple')
                $('input#content').attr('category', 'video')
            } else if(type === 'audio') {
                $('input#content').attr('accept', 'audio/*')
                $('input#content').removeAttr('multiple')
                $('input#content').attr('category', 'audio')
            } else if(type === 'file') {
                $('input#content').attr('accept', 'application/pdf')
                $('input#content').removeAttr('multiple')
                $('input#content').attr('category', 'pdf')
            } else {
                $('input#content').removeAttr('accept')
                $('input#content').removeAttr('multiple')
            }

        })

        // show uploaded files
        addOldUpload(banners)
        addOldUpload(recordings, 1)

        // $('form').submit(function(e) {
        //     var audio = document.getElementById("audio");
        //     if(audio.readyState > 0)
        //     {
        //         var minutes = parseInt(audio.duration / 60, 10);
        //         var seconds = parseInt(audio.duration % 60);
        //         if(seconds < 10){
        //             seconds = '0'+seconds
        //         }
        //         $('[name=duration]').val(minutes+":"+seconds);
        //     }

        // })
    })

</script>
<script src="{{asset('js/jquery.form.js')}}"></script>
<script src="{{asset('js/formAjax.js?v=1.6')}}"></script>
<!-- include tinymce js -->
<script src="{{asset('js/tinymce.min.js')}}"></script>
<script src="{{asset('js/jquery.tinymce.min.js')}}"></script>
<script>
    $('textarea#inputDescription').tinymce({
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
