@extends('adminlte::page')

@section('title', 'Station Add New')

@section('content_header')
    Station Add New
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('station_add_new') }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.stations.store') }}" enctype="multipart/form-data" class="ajax">
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Title -->
                <div class="form-group row required">
                    <label for="inputTitle" class="col-sm-2 col-form-label">Title</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputTitle" name="title" value="{{ old('title') }}" placeholder="Title" autofocus>
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

                <!-- Email -->
                <div class="form-group row required">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" maxlength="100" class="form-control" id="inputEmail" name="email" value="{{ old('email') }}" placeholder="Email">
                        {!! $errors->first('email', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Phone -->
                <div class="form-group row">
                    <label for="inputPhone" class="col-sm-2 col-form-label">Phone</label>
                    <div class="col-sm-10">
                        <input type="number" minlength="11" maxlength="15" class="form-control" id="inputPhone" name="phone" value="{{ old('phone') }}" placeholder="Phone (09xxxxxxxxx)" autofocus>
                        {!! $errors->first('phone', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Signal -->
                <div class="form-group row">
                    <label for="inputSignal" class="col-sm-2 col-form-label">Signal</label>
                    <div class="col-sm-10">
                        <input type="tel" minlength="7" maxlength="20" class="form-control" id="inputSignal" name="signal" value="{{ old('signal') }}" placeholder="Phone (09xxxxxxxxx)" autofocus>
                        {!! $errors->first('signal', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Viber -->
                <div class="form-group row">
                    <label for="inputViber" class="col-sm-2 col-form-label">Viber</label>
                    <div class="col-sm-10">
                        <input type="tel" minlength="7" maxlength="20" class="form-control" id="inputViber" name="viber" value="{{ old('viber') }}" placeholder="Phone (09xxxxxxxxx)" autofocus>
                        {!! $errors->first('viber', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- WhatsApp -->
                <div class="form-group row">
                    <label for="inputWhatsApp" class="col-sm-2 col-form-label">WhatsApp</label>
                    <div class="col-sm-10">
                        <input type="tel" minlength="7" maxlength="20" class="form-control" id="inputWhatsApp" name="whats_app" value="{{ old('whats_app') }}" placeholder="Phone (09xxxxxxxxx)" autofocus>
                        {!! $errors->first('whats_app', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Facebook Link -->
                <div class="form-group row">
                    <label for="inputFacebookLink" class="col-sm-2 col-form-label">Facebook Link</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputFacebookLink" name="facebook_link" value="{{ old('facebook_link') }}" placeholder="Facebook Link" autofocus>
                        {!! $errors->first('facebook_link', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Messenger Link -->
                <div class="form-group row">
                    <label for="inputMessengerLink" class="col-sm-2 col-form-label">Messenger Link</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputMessengerLink" name="messenger_link" value="{{ old('messenger_link') }}" placeholder="Messenger Link" autofocus>
                        {!! $errors->first('messenger_link', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Public Key -->
                <div class="form-group row">
                    <label for="inputPublicKey" class="col-sm-2 col-form-label">Public Key</label>
                    <div class="col-sm-10">
                        <textarea placeholder="...." class="form-control" name="public_key" id="inputPublicKey" cols="30" rows="10">{{ old('public_key') }}</textarea>
                        {!! $errors->first('public_key', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Private Key -->
                <div class="form-group row">
                    <label for="inputPrivateKey" class="col-sm-2 col-form-label">Private Key</label>
                    <div class="col-sm-10">
                        <textarea placeholder="...." class="form-control" name="private_key" id="inputPrivateKey" cols="30" rows="10">{{ old('private_key') }}</textarea>
                        {!! $errors->first('private_key', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Is Public Flag -->
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="is_public" name="is_public" checked>
                    <label class="form-check-label" for="is_public">Public</label>
                </div>
            </div>

            <!-- Image -->
            <div class="card-header with-border">
                <h3 class="card-title">Image</h3>
            </div>
            <div class="card-body">
                <div class="form-group row file-required">
                    <label for="inputImage" class="col-sm-2 col-form-label">
                        <span>Upload</span> <br/><kbd>2MB, Image Only</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="image/*" category="image" class="form-control-file" id="inputImage" name="image"/>
                        {!! $errors->first('image', '<span class="text-danger">:message</span>') !!}

                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            <!-- Profile Image -->
            <div class="card-header with-border">
                <h3 class="card-title">Profile Image</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label for="inputProfileImage" class="col-sm-2 col-form-label">
                        <span>Upload</span> <br/><kbd>2MB, Image Only, Dimension - 1:1</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="image/*" category="image" class="form-control-file" id="inputProfileImage" name="profile_image"/>
                        {!! $errors->first('profile_image', '<span class="text-danger">:message</span>') !!}

                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-submit">Create</button>
                    <a href="{{ route('admin.stations.index') }}" class="btn btn-default" role="button">Back</a>
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
