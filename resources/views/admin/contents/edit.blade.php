@extends('adminlte::page')

@section('title', 'Content Edit')

@section('content_header')
    Content Edit
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('content_edit', $audio->id) }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.audios.update', $audio->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Title -->
                <div class="form-group row required">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Type</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputTitle" name="title" value="{{ old('title', $audio->title) }}" placeholder="Title" autofocus>
                        {!! $errors->first('title', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                 <!-- Description -->
                 <div class="form-group row required">
                    <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea maxlength="1000" rows="5" class="form-control" id="inputDescription" name="description" placeholder="Description">{{ old('description', $audio->description) }}</textarea>
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
                                    @if ($station->id == old('station_id', $audio->station_id))
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

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />
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

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />
                        <input type="hidden" name="duration" value="{{ $audio->duration}}"/>

                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                    <a href="{{ route('admin.audios.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
    let banners = @json($banners);
    let recordings = @json($recordings);

    $(function () {
        $('#inputStation').select2({
            placeholder: 'Please select a station',
            width: '100%'
        })
    })

    $(function () {
        // show uploaded files
        addOldUpload(banners)
        addOldUpload(recordings, 1)
    })
</script>
@endsection

@section('css')
@endsection
