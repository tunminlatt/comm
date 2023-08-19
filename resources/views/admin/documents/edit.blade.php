@extends('adminlte::page')

@section('title', 'Document Edit')

@section('content_header')
    Document Edit
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('document_edit', $document->id) }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.documents.update', $document->id) }}" enctype="multipart/form-data">
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
                        <input type="text" maxlength="100" class="form-control" id="inputTitle" name="title" value="{{ old('title', $document->title) }}" placeholder="Title" autofocus>
                        {!! $errors->first('title', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                 <!-- Description -->
                 <div class="form-group row required">
                    <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea maxlength="1000" rows="5" class="form-control" id="inputDescription" name="description" placeholder="Description">{{ old('description', $document->description) }}</textarea>
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
                                    @if ($station->id == old('station_id', $document->station_id))
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
                <h3 class="card-title"><b>File</b></h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required">
                    <div class="col-sm-10">
                        <input type="file" accept="*" category="image" class="form-control-file" name="file"/>
                        {!! $errors->first('file', '<span class="text-danger">:message</span>') !!}

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                    <a href="{{ route('admin.documents.index') }}" class="btn btn-default" role="button">Back</a>
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
