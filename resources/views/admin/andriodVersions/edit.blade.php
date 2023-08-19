@extends('adminlte::page')

@section('title', 'Andriod Version Edit')

@section('content_header')
    Andriod Version Edit
@stop

@section('breadcrumbs')
    
@endsection

@section('content')
    <div id="toast-container" class="toast-top-center">
        <div class="toast toast-warning" aria-live="assertive" style="display: block;">
            <div class="toast-message">
                We are allowed only developer role to edit this.
            </div>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.andriodVersions.update', $andriodVersion->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Latest Version Code -->
                <div class="form-group row required">
                    <label for="latest_version_code" class="col-sm-2 col-form-label">Latest Version Code</label>
                    <div class="col-sm-10">
                        <input type="number" maxlength="100" class="form-control" id="inputTitle" name="latest_version_code" value="{{ old('latest_version_code', $andriodVersion->latest_version_code) }}" placeholder="Latest Version Code" autofocus>
                        {!! $errors->first('latest_version_code', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                 <!-- Require Force Update -->
                 <div class="form-group row required">
                    <div class="col-sm-2">
                        <label for="require_force_update" class="col-form-label radio-label">Require Force Update</label>
                    </div>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input type="checkbox" id="require_force_update" name="require_force_update" class="form-check-input" {{ ($andriodVersion->require_force_update == 1 ? ' checked' : '') }} >
                        </div>
                    </div>
                </div>

                <!-- Min Version Code -->
                <div class="form-group row required">
                    <label for="min_version_code" class="col-sm-2 col-form-label">Min Version Code</label>
                    <div class="col-sm-10">
                        <input type="number" maxlength="100" class="form-control" id="inputTitle" name="min_version_code" value="{{ old('min_version_code', $andriodVersion->min_version_code) }}" placeholder="Min Version Code" autofocus>
                        {!! $errors->first('min_version_code', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Play Store Link -->
                <div class="form-group row required">
                    <label for="play_store_link" class="col-sm-2 col-form-label">Play Store Link</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputTitle" name="play_store_link" value="{{ old('Play Store Link', $andriodVersion->play_store_link) }}" placeholder="Play Store Link" autofocus>
                        {!! $errors->first('play_store_link', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>
            </div>

            <!-- Self Hosted APK -->
            <div class="card-header with-border">
                <h3 class="card-title">Self Hosted APK</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label for="inputApk" class="col-sm-2 col-form-label">
                        <span>Upload</span> <br/><kbd>APK Only</kbd>
                    </label>

                    <div class="col-sm-10">
                        <input type="file" accept=".apk" category="image" class="form-control-file" id="inputApk" name="self_hosted_link"/>
                        @if($apk)
                            <p style="margin-top: 5px;">
                                <a href="{{ url('storage/andriodVersion/yyat.apk') }}">yyat.apk</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                    <a href="{{ route('admin.andriodVersions.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script src="{{asset('js/jquery.form.js')}}"></script>
<script src="{{asset('js/formAjax.js')}}"></script>
@endsection

@section('css')
<style>
    .tox-notifications-container {display: none !important;}
    .tox-statusbar__branding {display: none !important;}
</style>
@endsection

@section('css')
@endsection
