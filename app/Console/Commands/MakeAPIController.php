<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Storage;

class MakeAPIController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:apicontroller {name} {--a|all} {--s|show} {--c|create} {--u|update} {--d|delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create api controller directory inside app';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
     public function handle()
     {
        // prepare variables
        $storage = Storage::disk('app');
        $name = $this->argument('name');
        $displayAll = $this->option('all');
        $displayShow = $this->option('show');
        $displayCreate = $this->option('create');
        $displayUpdate = $this->option('update');
        $displayDelete = $this->option('delete');

        $splitedName = explode('/', $name);
        $splitedNameCopy = $splitedName;
        array_pop($splitedNameCopy);
        $apiFolder = implode('\\', $splitedNameCopy);

        $baseName = end($splitedName);
        $singularFirstCapName = str_replace('Controller', '', $baseName);
        $singularSmallName = strtolower($singularFirstCapName);
        $pluralSmall = Str::plural($singularSmallName);

        $extension = 'php';
        $folderpath = 'Http/Controllers';
        $filePath = $folderpath .'/'. $name .'.'. $extension;

        $contents = '<?php

namespace App\Http\Controllers\\'. $apiFolder .';

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\\'. $singularFirstCapName .'Repository;';

$contents .= $displayAll ? '
use App\Http\Requests\\'. $apiFolder .'\\'. $singularFirstCapName .'Request;' : '';

$contents .= ($displayShow || $displayDelete) ? '
use App\Http\Requests\\'. $apiFolder .'\\'. $singularFirstCapName .'DetailRequest;' : '';

$contents .= $displayCreate ? '
use App\Http\Requests\\'. $apiFolder .'\\Store'. $singularFirstCapName .'Request;' : '';

$contents .= $displayUpdate ? '
use App\Http\Requests\\'. $apiFolder .'\\Update'. $singularFirstCapName .'Request;' : '';

$contents .= $displayAll ? '
use App\Http\Resources\\'. $apiFolder .'\\'. $singularFirstCapName .'Resource;' : '';

$contents .= $displayShow ? '
use App\Http\Resources\\'. $apiFolder .'\\'. $singularFirstCapName .'DetailResource;' : '';

$contents .= '
use App\Helpers\Responder;
use DB;

class '. $baseName .' extends Controller
{
    public function __construct(
        '. $singularFirstCapName .'Repository $'. $singularSmallName .'Repository,
        Responder $responder
    ) {
        $this->'. $singularSmallName .'Repository = $'. $singularSmallName .'Repository;
        $this->responder = $responder;
    }';

    if ($displayAll) {

    $contents .= '

    public function getAll('. $singularFirstCapName .'Request $request) {
        DB::beginTransaction();

        try {
            $'. $pluralSmall .' = $this->'. $singularSmallName .'Repository->all([], false);
            DB::commit();

            if ($'. $pluralSmall .'->count()) {
                return '. $singularFirstCapName .'Resource::collection($'. $pluralSmall .');
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, \'Something wrong!\');
        }
    }';
    }

    if ($displayShow) {

    $contents .= '

    public function getDetail('. $singularFirstCapName .'DetailRequest $request) {
        DB::beginTransaction();

        try {
            // prepare variables
            $id = $request->id;

            $'. $singularSmallName .' = $this->'. $singularSmallName .'Repository->show(\'id\', $id, []);
            DB::commit();

            if ($'. $singularSmallName .'->exists()) {
                return new '. $singularFirstCapName .'DetailResource($'. $singularSmallName .');
            } else {
                return $this->responder->noDataResponse();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->customResponse(500, \'Something wrong!\');
        }
    }';
    }

    if ($displayCreate) {

    $contents .= '

	public function create(Store'. $singularFirstCapName .'Request $request) {
        DB::beginTransaction();

        try {
            // prepare variables
            $id = $request->user()->id;
            $payload = $request->only([\'course_unit_lesson_id\', \'title\', \'description\']);

            $this->'. $singularSmallName .'Repository->create($payload);
            DB::commit();

            return $this->responder->createResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->createResponse(false);
        }
    }';
    }

    if ($displayUpdate) {

    $contents .= '

    public function update(Update'. $singularFirstCapName .'Request $request) {
        DB::beginTransaction();

        try {
            // prepare data to update
            $id = $request->user()->id;
            $payload = $request->only([\'field\']);

            $this->'. $singularSmallName .'Repository->update($id, $payload);
            DB::commit();

            return $this->responder->updateResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->updateResponse(false);
        }
    }';
    }

    if ($displayDelete) {

    $contents .= '

    public function delete('. $singularFirstCapName .'DetailRequest $request) {
        DB::beginTransaction();

        try {
            // prepare data to delete
            $id = $request->field;

            $this->'. $singularSmallName .'Repository->destroy($id);
            DB::commit();

            return $this->responder->deleteResponse();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responder->deleteResponse(false);
        }
    }';
    }

    $contents .= '
}';

        // create parent folder
        $storage->makeDirectory($folderpath);

        // create file under folder
        if ($storage->exists($filePath)) {
           $this->line('<fg=white;bg=red>API Controller already exists!</>');
        } else {
            $storage->put($filePath, $contents);
            $this->line('<fg=green>API Controller created successfully.</>');
        }
    }
}