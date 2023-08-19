<?php

namespace App\Helpers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use App\Helpers\Image;
use Carbon\Carbon;
use Hash;
use Storage;

class Seed
{
    public static function generateCreatedDate () {
        return Carbon::now()->subDays(rand(5, 9));
    }

    public static function generateUpdatedDate () {
        return Carbon::now()->subDays(rand(1, 5));
    }

    public static function generateRememberToken () {
        return Str::random(10);
    }

    public static function generateCurrentDate () {
        return Carbon::now();
    }

    public static function getSeedLimit () {
        return 10;
    }

    public static function getDefaultPassword () {
        return Hash::make('password');
    }

    public static function localize ($value) {
        $valueEN = $value .' (en)';
        $valueMM = $value .' (mm)';

        return [$valueEN, $valueMM];
    }

    public static function insertData ($model, $rows, $timeStamps = TRUE) {
        $model::truncate();

        $ids = [];
        foreach ($rows as $row) {
            if ($timeStamps) {
                $row['created_at'] = Self::generateCreatedDate();
                $row['updated_at'] = Self::generateUpdatedDate();
            }

            $newRow = $model::create($row);
            $ids[] = $newRow->id;
        }

        return $ids;
    }

    public static function insertImage ($seedFolder, $destFolder, $filter = false) {
        // prepare variables
        $fileSystem = new Filesystem();
        $image = new Image($fileSystem);
        $seedPath = storage_path("seed/$seedFolder");
        $seedFiles = glob($seedPath .'/*.*');
        $files = [];

        foreach ($seedFiles as $seedFile) {
            $name = $fileSystem->name($seedFile);

            // only take specific photo
            if ($filter !== false) {
                if ($name != $filter) {
                    continue;
                }
            }

            $files[] = new File($seedFile);
        }

        $image->add($destFolder, $files);
    }
}