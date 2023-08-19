@extends('adminlte::page')

@section('title', 'Admin Edit')

@section('content_header')
    Admin Edit
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin_edit', $user->id) }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header with-border">
            <h3 class="card-title">Basic Info</h3>
        </div>
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
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
                    <div class="form-group row required">
                        <label for="inputOldPassword" class="col-sm-2 col-form-label">Old Password</label>
                        <div class="col-sm-10">
                            <input type="password" minlength="8" maxlength="100" class="form-control" id="inputOldPassword" name="old_password" placeholder="Old Password">
                            {!! $errors->first('old_password', '<span class="text-danger">:message</span>') !!}
                        </div>
                    </div>
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

                <!-- Submit -->
                <div class="card-footer">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default" role="button">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
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