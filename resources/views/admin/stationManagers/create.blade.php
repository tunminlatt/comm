@extends('adminlte::page')

@section('title', 'Station Manager Add New')

@section('content_header')
    Station Manager Add New
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('station_manager_add_new') }}
@endsection

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.stationManagers.store') }}">
            @csrf

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Name -->
                <div class="form-group row required">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputName" name="name" value="{{ old('name') }}" placeholder="Name" autofocus>
                        {!! $errors->first('name', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group row required">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputEmail" name="email" value="{{ old('email') }}" placeholder="Email">
                        {!! $errors->first('email', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group row required">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" minlength="8" maxlength="100" class="form-control" id="inputPassword" name="password" placeholder="Password">
                        {!! $errors->first('password', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="inputPasswordConfirm" class="col-sm-2 col-form-label">Confirm Password</label>
                    <div class="col-sm-10">
                        <input type="password" minlength="8" maxlength="100" class="form-control" id="inputPasswordConfirm" name="password_confirmation" placeholder="Confirm Password">
                        {!! $errors->first('password_confirmation', '<span class="text-danger">:message</span>') !!}
                    </div>
                </div>
            </div>

            <!-- Station -->
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
            </div>

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-submit">Create</button>
                    <a href="{{ route('admin.stationManagers.index') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
<script>
let inputStationInput = $('#inputStation')

inputStationInput.select2({
    placeholder: 'Please select a station',
    width: '100%',
    allowClear: true
})
</script>
@endsection

@section('css')
@endsection