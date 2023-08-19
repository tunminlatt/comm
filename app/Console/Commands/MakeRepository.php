<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Storage;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name} {--t|table} {--m|many}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create repository directory inside app';

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
        $singularFirstCapName = str_replace('Repository', '', $name);
        $singularSmallName = strtolower($singularFirstCapName);
        $pluralSmall = Str::plural($singularSmallName);
        $displayTable = $this->option('table');
        $isManyToMany = $this->option('many');
        $extension = 'php';
        $folderpath = 'Repositories';
        $filePath = $folderpath .'/'. $name .'.'. $extension;

        $contents = '<?php

namespace App\Repositories;

use App\Models\\'. $singularFirstCapName .';

class '. $name .'
{
    public function __construct('. $singularFirstCapName .' $'. $singularSmallName .') {
        $this->'. $singularSmallName .' = $'. $singularSmallName .';
    }';

if ($displayTable) {
    $contents .= '

    public function getAllForTable($request, $eagerLoad = [], $withTrash = true) {
        return $this->'. $singularSmallName .'
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($eagerLoad, function ($query) use ($eagerLoad, $withTrash) {
                        if ($withTrash) {
                            return $query->withEagerTrashed($eagerLoad);
                        } else {
                            return $query->with($eagerLoad);
                        }
                    })
                    ->orderBy(\'deleted_at\', \'desc\')
                    ->orderBy(\'name\', \'asc\')
                    ->get();
    }';
}

    $contents .= '

    public function all($eagerLoad = [], $withTrash = true, $paginateCount = 0) {
        return $this->'. $singularSmallName .'
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($eagerLoad, function ($query) use ($eagerLoad, $withTrash) {
                        if ($withTrash) {
                            return $query->withEagerTrashed($eagerLoad);
                        } else {
                            return $query->with($eagerLoad);
                        }
                    })
                    ->when($paginateCount, function ($query, $role) use ($paginateCount) {
                        return $query->paginate($paginateCount);
                    }, function ($query) {
                        return $query->get();
                    });
    }

    public function show($column, $value, $eagerLoad = [], $withTrash = false, $returnMany = false) {
        return $this->'. $singularSmallName .'
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($eagerLoad, function ($query) use ($eagerLoad, $withTrash) {
                        if ($withTrash) {
                            return $query->withEagerTrashed($eagerLoad);
                        } else {
                            return $query->with($eagerLoad);
                        }
                    })
                    ->where($column, $value)
                    ->when($returnMany, function ($query, $role) {
                        return $query->get();
                    }, function ($query) {
                        return $query->first();
                    });
    }

    public function create($payload) {
        return $this->'. $singularSmallName .'->create($payload);
    }

    public function update($id, $payload, $withTrash = false) {
        return $this->'. $singularSmallName .'
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->find($id)->update($payload);
    }';

if ($isManyToMany) {
    $contents .= '
    public function createOrDestroyCourses($reward, $payload, $type = 1) { // 0-destroy, 1-create
        if ($type === 0) {
            return $reward->courses()->detach($payload);
        } else {
            return $reward->courses()->attach($payload);
        }
    }';
}

    $contents .= '

    public function canDestroy($id) {
        return $this->'. $singularSmallName .'->where(\'id\', $id)->doesntHave(\'childrens\')->exists();
    }

    public function destroy($id, $type = 1) { // 1 - normal, 2 - force
        if ($type == 1) {
            return $this->'. $singularSmallName .'->destroy($id);
        } else {
            return $this->'. $singularSmallName .'->withTrashed()->find($id)->forceDelete();
        }
    }

    public function restore($id) {
        return $this->'. $singularSmallName .'->withTrashed()->find($id)->restore();
    }
}';

        // create parent folder
        $storage->makeDirectory($folderpath);

        // create file under folder
        if ($storage->exists($filePath)) {
            $this->line('<fg=white;bg=red>Repository already exists!</>');
        } else {
            $storage->put($filePath, $contents);
            $this->line('<fg=green>Repository created successfully.</>');
        }
    }
}