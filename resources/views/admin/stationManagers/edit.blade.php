@extends('adminlte::page')

@section('title', 'Station Manager Edit')

@section('content_header')
    Station Manager Edit
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('station_manager_edit', $user->id) }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header with-border">
            <h3 class="card-title">Basic Info</h3>
        </div>
        <form method="POST" action="{{ route('admin.stationManagers.update', $user->id) }}">
            @method('PUT')
            @csrf

            <div class="card-body">
                <!-- Name -->
                <div class="form-group row required">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputName" name="name" value="{{ old('name', $user->name) }}" placeholder="Name" autofocus>
                        {!! $errors->first('name', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group row required">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputEmail" name="email" value="{{ old('email', $user->email) }}" placeholder="Email">
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

                    @if($userTypeID == 2) <!-- force change for admin -->
                        <div class="form-group row required">
                            <label for="inputOldPassword" class="col-sm-2 col-form-label">Old Password</label>
                            <div class="col-sm-10">
                                <input type="password" minlength="8" maxlength="100" class="form-control" id="inputOldPassword" name="old_password" placeholder="Old Password">
                                {!! $errors->first('old_password', '<span class="text-danger">:message</span>') !!}
                            </div>
                        </div>
                    @endif
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
            </div>

            <!-- Station -->
            @if($userTypeID == 1) <!-- only accessible by admin -->
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
                                    @if ($station->id == old('station_id', $user->station_id))
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
            @endif

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                    <a href="{{ route('admin.stationManagers.index') }}" class="btn btn-default" role="button">Back</a>
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

        $(function () {
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
@endsection

@section('css')
@endsection