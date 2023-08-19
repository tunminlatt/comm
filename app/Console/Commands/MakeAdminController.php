<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Storage;

class MakeAdminController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admincontroller {name} {--s|show} {--l|localize} {--i|singleImage} {--I|multipleImage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin controller directory inside app';

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
        $displayShow = $this->option('show');
        $isLocalize = $this->option('localize');
        $displaySingleImage = $this->option('singleImage');
        $displayMultipleImage = $this->option('multipleImage');

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
use App\Repositories\\'. $singularFirstCapName .'Repository;
use App\Http\Requests\\'. $apiFolder .'\Store'. $singularFirstCapName .'Request;
use App\Http\Requests\\'. $apiFolder .'\Update'. $singularFirstCapName .'Request;
use App\Http\Resources\\'. $apiFolder .'\\'. $singularFirstCapName .'DatatableResource;
use Yajra\Datatables\Datatables;';

    $contents .= ($displaySingleImage || $displayMultipleImage) ? '
use App\Helpers\Image;' : '';

    $contents .= '
use DB;

class '.$baseName.' extends Controller
{
    public function __construct(
        '. $singularFirstCapName .'Repository $'. $singularSmallName .'Repository,
        Datatables $datatables';

        $contents .= ($displaySingleImage || $displayMultipleImage) ? ',
        Image $image' : '';

        $contents .= '
    ) {
        $this->'. $singularSmallName .'Repository = $'. $singularSmallName .'Repository;
        $this->datatables = $datatables;';

        $contents .= ($displaySingleImage || $displayMultipleImage) ? '
        $this->image = $image;' : '';

        $contents .= '
    }';

if ($isLocalize) {
    $contents .= '

    protected function packData($request) {
        $payload = $request->only(\'column\', \'column\');

        $payload[\'name\'] = [
            \'en\' => $request->name_en,
            \'mm\' => $request->name_mm,
        ];

        return $payload;
    }';
} else {
    $contents .= '

    protected function packData($request) {
        return $request->only(\'column\', \'column\');
    }';
}

$contents .= '

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $'. $pluralSmall .' = $this->'. $singularSmallName .'Repository->getAllForTable($request, [], true);
            $'. $singularSmallName .'DatatableResource = '. $singularFirstCapName .'DatatableResource::collection($'. $pluralSmall .');

            return $this->datatables->of($'. $singularSmallName .'DatatableResource)->addIndexColumn()->toJson();
        }

        return view(\'admin.'. $pluralSmall .'.index\');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view(\'admin.'. $pluralSmall .'.create\');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store'. $singularFirstCapName .'Request $request)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $request->only(\'name\', \'email\', \'password\');

            // create '. $singularSmallName .'
            ';

            $contents .= ($displaySingleImage || $displayMultipleImage) ? '$'. $singularSmallName .' = ' : '';

            $contents .= '$this->'. $singularSmallName .'Repository->create($payload);';

            if ($displaySingleImage || $displayMultipleImage) {
                $contents .= '

            // create image
            $this->image->add(\''. $pluralSmall .'/\'. $'. $singularSmallName .'->id, [$request->image]);';
            }

    $contents .= '
            DB::commit();

            return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'success\', \''. $singularFirstCapName .' Created Successfully!\');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'fail\', \''. $singularFirstCapName .' Creating Failed!\');
        }
    }';

    if ($displayShow) {
        $contents .= '

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $'. $singularSmallName .' = $this->'. $singularSmallName .'Repository->show(\'id\', $id, [], true);';

        if ($displaySingleImage || $displayMultipleImage) {
            $contents .= '
        $images = $this->image->get(\''. $pluralSmall .'/\'. $id);

        return view(\'admin.'. $pluralSmall .'.show\', [\''. $singularSmallName .'\' => $'. $singularSmallName .', \'images\' => $images]);';
        } else {
            $contents .= '

        return view(\'admin.'. $pluralSmall .'.show\', [\''. $singularSmallName .'\' => $'. $singularSmallName .']);';
        }

        $contents .= '
    }';
    }

    $contents .= '

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $'. $singularSmallName .' = $this->'. $singularSmallName .'Repository->show(\'id\', $id, [], true);';

        if ($displaySingleImage || $displayMultipleImage) {
            $contents .= '
        $images = $this->image->get(\''. $pluralSmall .'/\'. $id);

        return view(\'admin.'. $pluralSmall .'.edit\', [\''. $singularSmallName .'\' => $'. $singularSmallName .', \'images\' => $images]);';
        } else {
            $contents .= '

        return view(\'admin.'. $pluralSmall .'.edit\', [\''. $singularSmallName .'\' => $'. $singularSmallName .']);';
        }

        $contents .= '
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Update'. $singularFirstCapName .'Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // prepare variables
            $payload = $request->only(\'name\', \'email\');

            // update '. $singularSmallName .'
            $this->'. $singularSmallName .'Repository->update($id, $payload, true);';

            if ($displaySingleImage || $displayMultipleImage) {
                $contents .= '

            // remove old uploads
            $oldUploadsToDelete = json_decode($request->old_upload_to_delete[0]);
            if ($oldUploadsToDelete && count($oldUploadsToDelete) > 0) {
                $this->image->delete(\''. $pluralSmall .'/\'. $id, $oldUploadsToDelete);
            }

            // add new uploads
            if ($request->hasFile(\'image\')) {
                $this->image->add(\''. $pluralSmall .'/\'. $id, [$request->image]';

                $contents .= $displaySingleImage ? ', true' : '';
                $contents .= ');
            }';
            }

            $contents .= '
            DB::commit();

            return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'success\', \''. $singularFirstCapName .' Updated Successfully!\');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'fail\', \''. $singularFirstCapName .' Updating Failed!\');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // check '. $singularSmallName .' can destroy or not
            $canDestroy = $this->'. $singularSmallName .'Repository->canDestroy($id);

            if ($canDestroy) {
                $this->'. $singularSmallName .'Repository->destroy($id);
                DB::commit();

                return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'success\', \''. $singularFirstCapName .' Deactivated Successfully!\');
            } else {
                return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'fail\', \'Cannot deactivate as this '. $singularSmallName .' is associated with childrens!\');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'fail\', \''. $singularFirstCapName .' Deactivating Failed!\');
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();

        try {
            $this->'. $singularSmallName .'Repository->restore($id);
            DB::commit();

            return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'success\', \''. $singularFirstCapName .' Activated Successfully!\');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route(\'admin.'. $pluralSmall .'.index\')->with(\'fail\', \''. $singularFirstCapName .' Activating Failed!\');
        }
    }
}';

        // create parent folder
        $storage->makeDirectory($folderpath);

        // create file under folder
        if ($storage->exists($filePath)) {
           $this->line('<fg=white;bg=red>Admin Controller already exists!</>');
        } else {
            $storage->put($filePath, $contents);
            $this->line('<fg=green>Admin Controller created successfully.</>');
        }
    }
}