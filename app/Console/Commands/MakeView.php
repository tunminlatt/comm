<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Storage;

class MakeView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {name} {--s|show} {--l|localize} {--i|singleImage} {--I|multipleImage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create view directory inside views';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function generate($contents, $title) {
        // create file output path
        $filePath = $this->folderpath .'/'. $this->name .'/'. $title .'.'. $this->extension;

        // create parent folder
        $this->storage->makeDirectory($this->folderpath);

        // create file under folder
        if ($this->storage->exists($filePath)) {
            $this->line('<fg=white;bg=red>'. ucfirst($title) .' blade already exists!</>');
        } else {
            $this->storage->put($filePath, $contents);
            $this->line('<fg=green>'. ucfirst($title) .' blade created successfully.</>');
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
     public function handle()
     {
        // prepare variables
        $this->storage = Storage::disk('resource');
        $this->name = $this->argument('name');
        $this->extension = 'blade.php';
        $this->folderpath = 'views';

        $splitedName = explode('/', $this->name);
        $pluralSmall = end($splitedName);
        $singularSmallName = Str::singular($pluralSmall);
        $singularFirstCapName = ucfirst($singularSmallName);

        $displayShow = $this->option('show');
        $isLocalize = $this->option('localize');
        $displaySingleImage = $this->option('singleImage');
        $displayMultipleImage = $this->option('multipleImage');

        // create index file
        $contents = '@extends(\'adminlte::page\')

@section(\'title\', \''. $singularFirstCapName .' List\')

@section(\'content_header\')
    '. $singularFirstCapName .' List
@stop

@section(\'breadcrumbs\')
    {{ Breadcrumbs::render(\''. $singularSmallName .'_list\') }}
@endsection

@section(\'content\')
    <div class="card">
        <div class="card-body">
            <table id="datatable" class="table table-striped nowrap">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Created Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@stop

@section(\'js\')
    <script>
        $(function () {
            let url = "{{ route(\'admin.'. $pluralSmall .'.index\') }}"
            let table = $(\'#datatable\').DataTable(
            {
                scrollX: true,
                scrollY: false,
                colReorder: true,
                processing: true,
                serverSide: true,
                ajax: url,
                lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
                dom: "B<\'table_mt row\'<\'col-md-6\'l><\'col-md-6\'f>>tr<\'row\'<\'col-md-6\'i><\'col-md-6\'p>>",
                buttons: [
                    {
                        text: \'Add New\',
                        action: function ( e, dt, node, config ) {
                            window.location.href = "{{ route(\'admin.'. $pluralSmall .'.create\') }}"
                        }
                    },
                    {
                        text: \'CSV Export\',
                        action: function ( e, dt, node, config ) {
                            let rows = dt.ajax.json().data
                            let data = [
                                [
                                    \'No\',
                                    \'Name\',
                                    \'Created Date\',
                                    \'Status\',
                                ]
                            ]

                            rows.forEach(function(row) {
                                data.push([
                                    escapeRow(row.DT_RowIndex),
                                    escapeRow(row.name),
                                    escapeRow(row.created_at),
                                    escapeRow(row.status)
                                ])
                            })

                            exportExcel(data, \''. $singularFirstCapName .' List\')
                        }
                    }
                ],
                columns: [
                    {data: \'DT_RowIndex\', name: \'DT_RowIndex\', orderable: false, searchable: false},
                    {data: \'name\', name: \'name\'},
                    {data: \'created_at\', name: \'created_at\'},
                    {data: \'status\', name: \'status\'},
                    {data: \'action\', name: \'action\', width: \'15%\', orderable: false, searchable: false}
                ]
            })
        })
    </script>
@endsection

@section(\'css\')
@endsection
';
        $this->generate($contents, 'index');

        // create action file
        $contents = '';
        if ($displayShow) {
            $contents .= '<!-- Show -->
<a class=\'btn btn-primary white_font\' href="{{ route(\'admin.'. $pluralSmall .'.show\', $'. $singularSmallName .'->id) }}">Detail</a>';
        }

        $contents .= '<!-- Edit -->
<a class=\'btn btn-primary white_font\' href="{{ route(\'admin.'. $pluralSmall .'.edit\', $'. $singularSmallName .'->id) }}">Edit</a>

<!-- Activate & Deactivate -->
@if ($'. $singularSmallName .'->deleted_at)
    <button type="submit" class="btn btn-warning" form="form_restore_{{$'. $singularSmallName .'->id}}">Activate</button>

    <form id="form_restore_{{$'. $singularSmallName .'->id}}" method="POST" action="{{ route(\'admin.'. $pluralSmall .'.restore\', $'. $singularSmallName .'->id) }}">
        @method(\'PUT\')
        @csrf
    </form>
@else
    <button id="deactivate" class="btn btn-danger" delete_id="{{$'. $singularSmallName .'->id}}">Deactivate</button>

    <form id="form_destroy_{{$'. $singularSmallName .'->id}}" method="POST" action="{{ route(\'admin.'. $pluralSmall .'.destroy\', $'. $singularSmallName .'->id) }}">
        @method(\'DELETE\')
        @csrf
    </form>
@endif
';
        $this->generate($contents, 'action');

        // create create file
        $contents = '@extends(\'adminlte::page\')

@section(\'title\', \''. $singularFirstCapName .' Add New\')

@section(\'content_header\')
    '. $singularFirstCapName .' Add New
@stop

@section(\'breadcrumbs\')
    {{ Breadcrumbs::render(\''. $singularSmallName .'_add_new\') }}
@endsection

@section(\'content\')
    <div class="card">
        <form method="POST" action="{{ route(\'admin.'. $pluralSmall .'.store\') }}"';
            $contents .= ($displaySingleImage || $displayMultipleImage) ? ' enctype="multipart/form-data"' : '';
            $contents .= '>
            @csrf
';

        if ($isLocalize) {
            $contents .= '
            <!-- Name -->
            <div class="card-header with-border">
                <h3 class="card-title">Name</h3>
            </div>
            <div class="card-body">
                <div class="form-group row required">
                    <label for="inputNameEN" class="col-sm-2 col-form-label">English</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" autofocus class="form-control" id="inputNameEN" name="name_en" value="{{ old(\'name_en\') }}" placeholder="Name in English">
                        {!! $errors->first(\'name_en\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="inputNameMM" class="col-sm-2 col-form-label">Myanmar</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputNameMM" name="name_mm" value="{{ old(\'name_mm\') }}" placeholder="Name in Myanmar">
                        {!! $errors->first(\'name_mm\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card-header with-border">
                <h3 class="card-title">Description</h3>
            </div>
            <div class="card-body">
                <div class="form-group row required">
                    <label for="inputDescriptionEN" class="col-sm-2 col-form-label">English</label>
                    <div class="col-sm-10">
                        <textarea maxlength="500" rows="5" class="form-control" id="inputDescriptionEN" name="description_en" placeholder="Description in English">{{ old(\'description_en\') }}</textarea>
                        {!! $errors->first(\'description_en\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="inputDescriptionMM" class="col-sm-2 col-form-label">Myanmar</label>
                    <div class="col-sm-10">
                        <textarea maxlength="500" rows="5" class="form-control" id="inputDescriptionMM" name="description_mm" placeholder="Description in Myanmar">{{ old(\'description_mm\') }}</textarea>
                        {!! $errors->first(\'description_mm\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>
            </div>';

            if ($displaySingleImage || $displayMultipleImage) {
                $contents .= '

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
                        <input type="file" accept="image/*" category="image" class="form-control-file" id="inputImage" ';

                        $contents .= $displaySingleImage ? 'name="image"' : 'name="image[] multiple"';

                        $contents .= '/>
                        {!! $errors->first(\'image\', \'<span class="text-danger">:message</span>\') !!}

                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>';
            }

            $contents .= '

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-submit">Create</button>
                    <a href="{{ route(\'admin.'. $pluralSmall .'.index\') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>';
        } else {
            $contents .= '
            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Name -->
                <div class="form-group row required">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputName" name="name" value="{{ old(\'name\') }}" placeholder="Name" autofocus>
                        {!! $errors->first(\'name\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row required">
                    <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea maxlength="500" rows="5" class="form-control" id="inputDescription" name="description" placeholder="Description">{{ old(\'description\') }}</textarea>
                        {!! $errors->first(\'description\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>

                <!-- Township -->
                <div class="form-group row required">
                    <label for="inputTownship" class="col-sm-2 col-form-label">Township</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="inputTownship" name="township_id">
                            <option value=\'\'>-- Please select a township --</option>
                            @foreach ($townships as $township)
                                @if ($township->id == old(\'township_id\'))
                                @if (in_array($township->id, old(\'township_id\', [])))
                                    <option value="{{ $township->id }}" selected>{{ $township->name_en }}</option>
                                @else
                                    <option value="{{ $township->id }}">{{ $township->name_en }}</option>
                                @endif
                            @endforeach
                        </select>
                        {!! $errors->first(\'township_id\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>';

                if ($displaySingleImage || $displayMultipleImage) {
                    $contents .= '

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
                            <input type="file" accept="image/*" category="image" class="form-control-file" id="inputImage" ';

                            $contents .= $displaySingleImage ? 'name="image"' : 'name="image[] multiple"';

                            $contents .= '/>
                            {!! $errors->first(\'image\', \'<span class="text-danger">:message</span>\') !!}

                            <div class="row push-top upload-preview"></div>
                        </div>
                    </div>
                </div>';
                }

                $contents .= '

                <!-- Submit -->
                <div class="card-footer">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-success" id="button-submit">Create</button>
                        <a href="{{ route(\'admin.'. $pluralSmall .'.index\') }}" class="btn btn-default" role="button">Back</a>
                    </div>
                </div>
            </div>';
        }

        $contents .= '
        </form>
    </div>
@endsection

@section(\'js\')
@endsection

@section(\'css\')
@endsection
';
        $this->generate($contents, 'create');

        // create show file
        $contents = '@extends(\'adminlte::page\')

@section(\'title\', \''. $singularFirstCapName .' Detail\')

@section(\'content_header\')
    '. $singularFirstCapName .' Detail
@stop

@section(\'breadcrumbs\')
    {{ Breadcrumbs::render(\''. $singularSmallName .'_detail\', $'. $singularSmallName .'->id) }}
@endsection

@section(\'content\')
    <div class="card">
        <form >';

            if ($isLocalize) {
                $contents .= '

            <!-- Name -->
            <div class="card-header with-border">
                <h3 class="card-title">Name</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $'. $singularSmallName .'->name }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">English</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $'. $singularSmallName .'->name[\'en\'] }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Myanmar</label>
                    <div class="col-sm-10">
                        <p class="form-control-plaintext">{{ $'. $singularSmallName .'->name[\'mm\'] }}</p>
                    </div>
                </div>
            </div>';

            if ($displaySingleImage || $displayMultipleImage) {
                $contents .= '

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
                                            <img src="{{ $image[\'data\'] }}" class="img-responsive">
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>';
            }

            $contents .= '

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <a href="{{ route(\'admin.'. $pluralSmall .'.index\') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>';
            } else {
                $contents .= '

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
                </div>';

                if ($displaySingleImage || $displayMultipleImage) {
                    $contents .= '

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
                                                <img src="{{ $image[\'url\'] }}" class="img-responsive">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>';
                }

                $contents .= '

                <!-- Submit -->
                <div class="card-footer">
                    <div class="offset-sm-2 col-sm-10">
                        <a href="{{ route(\'admin.'. $pluralSmall .'.index\') }}" class="btn btn-default" role="button">Back</a>
                    </div>
                </div>
            </div>';
            }

            $contents .= '
        </form>
    </div>
@endsection

@section(\'js\')
@endsection

@section(\'css\')
@endsection
';
        $this->generate($contents, 'show');

        // create edit file
        $contents = '@extends(\'adminlte::page\')

@section(\'title\', \''. $singularFirstCapName .' Edit\')

@section(\'content_header\')
    '. $singularFirstCapName .' Edit
@stop

@section(\'breadcrumbs\')
    {{ Breadcrumbs::render(\''. $singularSmallName .'_edit\', $'. $singularSmallName .'->id) }}
@endsection

@section(\'content\')
    <div class="card">
        <form method="POST" action="{{ route(\'admin.'. $pluralSmall .'.update\', $'. $singularSmallName .'->id) }}"';

            if ($displaySingleImage || $displayMultipleImage) {
                $contents .= ' enctype="multipart/form-data"';
            }

            $contents .= '>
            @method(\'PUT\')
            @csrf';

            if ($isLocalize) {
                $contents .= '

            <!-- Name -->
            <div class="card-header with-border">
                <h3 class="card-title">Name</h3>
            </div>
            <div class="card-body">
                <div class="form-group row required">
                    <label for="inputNameEN" class="col-sm-2 col-form-label">English</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" autofocus class="form-control" id="inputNameEN" name="name_en" value="{{ old(\'name_en\', $'. $singularSmallName .'->name[\'en\']) }}" placeholder="Name in English">
                        {!! $errors->first(\'name_en\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="inputNameMM" class="col-sm-2 col-form-label">Myanmar</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputNameMM" name="name_mm" value="{{ old(\'name_mm\', $'. $singularSmallName .'->name[\'mm\']) }}" placeholder="Name in Myanmar">
                        {!! $errors->first(\'name_mm\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card-header with-border">
                <h3 class="card-title">Description</h3>
            </div>
            <div class="card-body">
                <div class="form-group row required">
                    <label for="inputDescriptionEN" class="col-sm-2 col-form-label">English</label>
                    <div class="col-sm-10">
                        <textarea maxlength="500" rows="5" class="form-control" id="inputDescriptionEN" name="description_en" placeholder="Description in English">{{ old(\'description_en\', $'. $singularSmallName .'->description[\'en\']) }}</textarea>
                        {!! $errors->first(\'description_en\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>
                <div class="form-group row required">
                    <label for="inputDescriptionMM" class="col-sm-2 col-form-label">Myanmar</label>
                    <div class="col-sm-10">
                        <textarea maxlength="500" rows="5" class="form-control" id="inputDescriptionMM" name="description_mm" placeholder="Description in Myanmar">{{ old(\'description_mm\', $'. $singularSmallName .'->description[\'mm\']) }}</textarea>
                        {!! $errors->first(\'description_mm\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>
            </div>';

            if ($displaySingleImage || $displayMultipleImage) {
                $contents .= '

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
                        <input type="file" accept="image/*" category="image" class="form-control-file" id="inputImage" ';

                        $contents .= $displaySingleImage ? 'name="image"' : 'name="image[] multiple"';

                        $contents .= '/>
                        {!! $errors->first(\'image\', \'<span class="text-danger">:message</span>\') !!}

                        <input type="hidden" name="old_upload_to_delete[]" />
                        <input type="hidden" name="old_upload_count[]" />

                        <div class="row push-top upload-preview"></div>
                    </div>
                </div>
            </div>';
            }

            $contents .= '

            <!-- Submit -->
            <div class="card-footer">
                <div class="offset-sm-2 col-sm-10">
                    <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                    <a href="{{ route(\'admin.'. $pluralSmall .'.index\') }}" class="btn btn-default" role="button">Back</a>
                </div>
            </div>';
            } else {
                $contents .= '

            <div class="card-header with-border">
                <h3 class="card-title">Basic Info</h3>
            </div>
            <div class="card-body">
                <!-- Name -->
                <div class="form-group row required">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" maxlength="100" class="form-control" id="inputName" name="name" value="{{ old(\'name\', $'. $singularSmallName .'->name) }}" placeholder="Name" autofocus>
                        {!! $errors->first(\'name\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group row required">
                    <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea maxlength="500" rows="5" class="form-control" id="inputDescription" name="description" placeholder="Description">{{ old(\'description\', $'. $singularSmallName .'->description) }}</textarea>
                        {!! $errors->first(\'description\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>

                <!-- Township -->
                <div class="form-group row required">
                    <label for="inputTownship" class="col-sm-2 col-form-label">Township</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="inputTownship" name="township_id">
                            <option value=\'\'>-- Please select a township --</option>
                                @foreach ($townships as $township)
                                    @if ($township->id == old(\'township_id\', $'. $singularSmallName .'->township_id))
                                    @if (in_array($township->id, old(\'township_id\', convertIDArray($'. $singularSmallName .'->townships))))
                                        <option selected value="{{ $township->id }}">{{ $township->name_en }}</option>
                                    @else
                                        <option value="{{ $township->id }}">{{ $township->name_en }}</option>
                                    @endif
                                @endforeach
                        </select>
                        {!! $errors->first(\'township_id\', \'<span class="text-danger">:message</span>\') !!}
                    </div>
                </div>';

                if ($displaySingleImage || $displayMultipleImage) {
                    $contents .= '

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
                            <input type="file" accept="image/*" category="image" class="form-control-file" id="inputImage" ';

                            $contents .= $displaySingleImage ? 'name="image"' : 'name="image[] multiple"';

                            $contents .= '/>
                            {!! $errors->first(\'image\', \'<span class="text-danger">:message</span>\') !!}

                            <input type="hidden" name="old_upload_to_delete[]" />
                            <input type="hidden" name="old_upload_count[]" />

                            <div class="row push-top upload-preview"></div>
                        </div>
                    </div>
                </div>';
                }

                $contents .= '

                <!-- Submit -->
                <div class="card-footer">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-success" id="button-edit-submit">Update</button>
                        <a href="{{ route(\'admin.'. $pluralSmall .'.index\') }}" class="btn btn-default" role="button">Back</a>
                    </div>
                </div>
            </div>';
            }

            $contents .= '
        </form>
    </div>
@endsection

@section(\'js\')
    <script>
        let images = @json($images);

        $(function () {
            // show uploaded files
            addOldUpload(images)
        })
    </script>
@endsection

@section(\'css\')
@endsection
';
        $this->generate($contents, 'edit');
    }
}