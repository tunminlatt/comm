@extends('adminlte::page')

@section('title', 'Volunteer Edit')

@section('content_header')
    Volunteer Edit
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('volunteer_edit', $volunteer->id) }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.volunteers.update', $volunteer->id) }}" enctype="multipart/form-data" class="ajax">
            @method('PUT')
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Name -->
                <div class="form-group row required">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputName" name="name" value="{{ old('name', $volunteer->name) }}" placeholder="Name" autofocus>
                        {!! $errors->first('name', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Phone -->
                <div class="form-group row required">
                    <label for="inputPhone" class="col-sm-2 col-form-label">Phone</label>
                    <div class="col-sm-10">
                        <input type="number" minlength="11" maxlength="15" class="form-control" id="inputPhone" name="phone" value="{{ old('phone', $volunteer->phone) }}" placeholder="Phone (09xxxxxxxxx)" autofocus>
                        {!! $errors->first('phone', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group row required">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail" name="email" value="{{ old('email', $volunteer->email) }}" placeholder="example@gmail.com" autofocus>
                        {!! $errors->first('email', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group row required">
                    <div class="col-sm-2">
                        <label for="inputPasswordToggle" class="col-form-label radio-label">Change Password</label>
                    </div>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input type="checkbox" id="inputPasswordToggle" name="password_toggle" class="form-check-input" >
                        </div>
                    </div>
                </div>
                <div id="passwordDisplay" class="d-none">
                    <div class="form-group row required">
                        <label for="inputNewPassword" class="col-sm-2 col-form-label">New Password</label>
                        <div class="col-sm-10">
                            <input type="password" minlength="8" maxlength="100" class="form-control" id="inputNewPassword" name="new_password" placeholder="New Password">
                            {!! $errors->first('new_password', '<span class="text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="inputNewPasswordConfirm" class="col-sm-2 col-form-label">Confirm Password</label>
                        <div class="col-sm-10">
                            <input type="password" minlength="8" maxlength="100" class="form-control" id="inputNewPasswordConfirm" name="new_password_confirmation" placeholder="Confirm Password">
                            {!! $errors->first('new_password_confirmation', '<span class="text-danger">:message</span>') !!}
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group row">
                    <label for="inputAddress" class="col-sm-2 col-form-label">Address</label>
                    <div class="col-sm-10">
                        <textarea maxlength="1000" rows="5" class="form-control" id="inputAddress" name="address" placeholder="Address">{{ old('address', $volunteer->address) }}</textarea>
                        {!! $errors->first('address', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>
            </div>

            <!-- Station -->
            @if($userTypeID == 1) <!-- admin can select stations -->
                <div class="card-header with-border">
                    <h3 class="card-title">Station</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row required">
                        <label for="inputStation" class="col-sm-2 col-form-label">Choose One</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="inputStation" name="station_id">
                                <option value=''>-- Please select a station --</option>
                                @foreach ($stations as $station)
                                    @if ($station->id == old('station_id', $volunteer->station_id))
                                        <option value="{{ $station->id }}" selected>{{ $station->title }}</option>
                                    @else
                                        <option value="{{ $station->id }}">{{ $station->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                            {!! $errors->first('station_id', '<span class="text-danger">:message</span>') !!}
                        </div>
                    </div>
                </div>
            @else
                <input type="hidden" name="station_id" value="{{ $userStationID }}" />
            @endif

            <!-- Image -->
            <div class="card-header with-border">
                <h3 class="card-title">Image</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label for="inputImage" class="col-sm-2 col-form-label">
                        <span>Upload</span> <br/><kbd>2MB, Image Only</kbd>
                    </label>
                    <div class="col-sm-10">
                        <input type="file" accept="image/*" category="image" class="form-control-file" id="inputImage" name="image"/>
                        {!! $errors->first('image', '<span class="text-danger">:message</span>') !!}

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />

                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                    <a href="{{ route('admin.volunteers.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(function () {
            $('#inputStation').select2({
                placeholder: 'Please select a station',
                width: '100%'
            })
        })

        let images = @json($images);

        $(function () {
            // show uploaded files
            addOldUpload(images)

            // toggle password change checkbox
            $('[name=password_toggle]').change(function() {
                $('div#passwordDisplay').toggleClass('d-none')
            })

            // check checkbox need to on if validation fail
            let passwordToggle = "{{ old('password_toggle') }}"
            if (Boolean(passwordToggle)) {
                $('#inputPasswordToggle').trigger('click')
            }
        })
    </script>
        <script src="{{asset('js/jquery.form.js')}}"></script>
        <script src="{{asset('js/formAjax.js')}}"></script>
@endsection

@section('css')
@endsection
