@extends('adminlte::page')

@section('title', 'YYAT Edit')

@section('content_header')
YYAT Edit
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('programme_edit', $programme->id) }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.programmes.update', $programme->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Title -->
                <div class="form-group row required">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Title</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputTitle" name="title" value="{{ old('title', $programme->title) }}" placeholder="Title" autofocus>
                        {!! $errors->first('title', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                 <!-- Description -->
                 <div class="form-group row required">
                    <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea maxlength="5000" rows="5" class="form-control" id="inputDescription" name="description" placeholder="Description">{{ old('description', $programme->description) }}</textarea>
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
                                    @if ($station->id == old('station_id', $programme->station_id))
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
                <div class="form-group row">
                    <label for="inputType" class="col-sm-2 col-form-label">Content Type</label>
                    <div class="col-sm-10">
                        <p class="form-control">{{ ucwords($programme->type) }}</p>
                    </div>
                </div>

                <!-- Thumbnail -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Thumbnail</label>
                    <div class="col-sm-10">
                        @if ($thumbNail)
                        <img src="{{ $thumbNail[0]['data'] }}" height="100px" class="custom-content-display">
                        @else
                        <b>N/A</b>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Uploaded Contents</label>
                    <div class="col-sm-10">
                        @if(!$contents)
                        <b>N/A</b>
                        @endif
                        <!-- Uploaded Photos -->
                        @if ($programme->type == 'photo')
                            @foreach ($contents as $photo)
                            <img src="{{ $photo['data'] }}" height="100px" class="custom-content-display">
                            @endforeach
                        @endif

                        <!-- Uploaded Audio -->
                        @if ($programme->type == 'audio')
                            <audio controls>
                                <source src="{{ $contents[0]['data'] }}">
                            </audio>
                        @endif

                        <!-- Uploaded Video -->
                        @if ($programme->type == 'video')
                            <video width="320" height="240" controls>
                                <source src="{{ $contents[0]['data'] }}">
                                Your browser does not support the video tag.
                            </video>
                        @endif

                        <!-- Uploaded File -->
                        @if ($programme->type == 'file')
                            <embed src="{{ $contents[0]['data'] }}" width="800px" height="1000px" />
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="card-header with-border">
                <h3 class="card-title"><b>Update Content<span class="text-danger">*</span></b></h3>
            </div>
            <div class="card-body">
                <!-- Type -->
                <div class="form-group row">
                    <label for="inputTypeNew" class="col-sm-2 col-form-label">Content Type</label>
                    <div class="col-sm-10">
                        <select name="new_type" class="form-control" id="inputTypeNew">
                            <option value=""> --- Select Content Type --- </option>
                            <option value="audio">Audio</option>
                            <option value="video">Video</option>
                            <option value="photo">Photo</option>
                            <option value="file">File</option>
                        </select>
                        {!! $errors->first('new_type', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Content -->
                <div class="form-group row file-required">
                    <label for="inputBanner" class="col-sm-2 col-form-label">
                        <kbd>Audio, Video, Photo or File (1GB only)</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="" class="form-control-file" id="content" name="content[]"/>
                        {!! $errors->first('content', '<span class="text-danger">:message</span>') !!}
                        <input type="hidden" name="duration" value="{{ $programme->duration }}"/>

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>

                <!-- Thumb Nail -->
                <div class="card-header with-border">
                    <h3 class="card-title"><b>Thumb Nail</b></h3>
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
            </div>

            <!-- Banner -->
            {{-- <div class="card-header with-border">
                <h3 class="card-title"><b>Thumbnail <span class="text-danger">*</span></b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required required">
                    <label for="inputBanner" class="col-sm-2 col-form-label">
                        <kbd>Image Or Video (1GB only)</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" category="{{$bannerType}}" class="form-control-file" id="inputImage" name="banner"/>
                        {!! $errors->first('banner', '<span class="text-danger">:message</span>') !!}

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div> --}}

            <!-- Recording -->
            {{-- <div class="card-header with-border">
                 <h3 class="card-title"><b>Audio <span class="text-danger">*</span></b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required required">
                    <label for="inputRecording" class="col-sm-2 col-form-label">
                        <kbd>1GB, Audio Only</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="audio/*" category="audio" class="form-control-file" id="inputRecording" name="recording"/>
                        {!! $errors->first('recording', '<span class="text-danger">:message</span>') !!}

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />
                        <input type="hidden" name="duration" value="{{ $programme->duration }}"/>
                        <audio id="audio"></audio>
                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div> --}}

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                    <a href="{{ route('admin.programmes.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
<!-- include tinymce js -->
<script src="{{asset('js/tinymce.min.js')}}"></script>
<script src="{{asset('js/jquery.tinymce.min.js')}}"></script>
<script>
    let contents = @json($contents);

    $(function () {
        $('#inputStation').select2({
            placeholder: 'Please select a station',
            width: '100%'
        })
    })

    $(function () {
        $('#inputTypeNew').change(function(e) {
            let type = $('#inputTypeNew').val()

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
        addOldUpload(contents)

        $('form').submit(function(e) {
            var audio = document.getElementById("audio");
            if(audio.readyState > 0)
            {
                var minutes = parseInt(audio.duration / 60, 10);
                var seconds = parseInt(audio.duration % 60);
                if(seconds < 10){
                    seconds = '0'+seconds
                }
                $('[name=duration]').val(minutes+":"+seconds);
            }

        })
    })

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
<script src="{{asset('js/jquery.form.js')}}"></script>
<script src="{{asset('js/formAjax.js')}}"></script>
@endsection

@section('css')
<style>
    .tox-notifications-container {display: none !important;}
    .tox-statusbar__branding {display: none !important;}
    .custom-content-display {
        border: 1px solid #000;
        margin: 5px
    }
</style>
@endsection
