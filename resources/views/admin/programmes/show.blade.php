@extends('adminlte::page')

@section('title', 'YYAT Detail')

@section('content_header')
YYAT Detail
@stop

@section('breadcrumbs')
    {{ Breadcrumbs::render('programme_detail', $programme->id) }}
@endsection

@section('content')
    <div class="card">
        <form >

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Name -->
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $student->name }}</p>
                    </div>
                </div>

                <!-- Image -->
                <div class="card-header with-border">
                    <h3 class="card-title">Image</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Upload</label>
                        <div class="col-sm-10">
                            <div class="row push-top">
                                @foreach ($images as $image)
                                    <div class="col-md-3">
                                        <div class="thumbnail">
                                            <a href="#">
                                                <img src="{{ $image['url'] }}" class="img-responsive">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="card-footer">
                    <div class="offset-sm-2 col-sm-10">
                        <a href="{{ route('admin.programmes.index') }}" class="btn btn-default" role="button">Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
@endsection

@section('css')
@endsection
