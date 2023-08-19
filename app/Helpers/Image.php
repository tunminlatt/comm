<?php

namespace App\Helpers;

use Illuminate\Filesystem\Filesystem;
use Str;
use Storage;
use File;

class Image
{
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    protected function getName($file, $showExtension = true)
    {
        if ($showExtension) {
            return $this->fileSystem->basename($file);
        } else {
            return $this->fileSystem->name($file);
        }
    }

    public function getExtension($file, $isUploadedFile = true, $isExternal)
    {
        if ($isUploadedFile) {
            return $file->extension();
        } else {
            if ($isExternal) {
                return $this->fileSystem->extension($file);
            } else {
                preg_match("/^data:image\/(.*);base64/i", $file, $match);
                return $match[1];
            }
        }
    }

    protected function getURL($file)
    {
        $visibility = Storage::getVisibility($file);

        if ($visibility == 'public') {
            return Storage::url($file);
        } else {
            return Storage::url($file);
            // return Storage::temporaryUrl($file, now()->addMinutes(10080));
        }
    }

    protected function getBlob($file)
    {
        $content = Storage::get($file);
        $blob = base64_encode($content);
        $extension = $this->getExtension($file, false, true);

        return "data:image/$extension;base64,". $blob;
    }

    protected function generateUniqueName()
    {
        return (string) Str::uuid();
    }

    public function get($path, $filter = [], $loopSubFolder = false, $outputURL = true)
    {
        // loop all images
        if ($loopSubFolder) {
            $files = Storage::allFiles($path);
        } else {
            $files = Storage::files($path);
        }

        // get meta from image
        $images = [];
        foreach ($files as $file) {
            // prepare variables
            $name = $this->getName($file);
            $extension = $this->getExtension($file, false, true);

            // skip mac system files & do filter
            if ($extension == 'DS_Store' || ($filter && !in_array($name, $filter))) {
                continue;
            }

            // output
            $images[] = [
                'name' => $name,
                'data' => $outputURL ? $this->getURL($file) : $this->getBlob($file)
            ];
        }

        return $images;
    }

    public function add($path, $files, $wipeExisting = false, $isExternal = false)
    {
        if ($wipeExisting) {
            Storage::deleteDirectory($path);
        }

        $paths = [];
        foreach ($files as $file) {
            $isUploadedFile = $this->fileSystem->isFile($file);
            if ($isUploadedFile) {
                $paths[] = Storage::putFile($path, $file);
            } else {
                $name = $this->generateUniqueName();
                $extension = $this->getExtension($file, $isUploadedFile, $isExternal);
                $path = $path .'/'. $name .'.'. $extension;

                if ($isExternal) {
                    $file = file_get_contents($file);
                } else {
                    $file = base64_decode(explode(',', $file)[1]);
                }

                Storage::put($path, $file, 'public');
                $paths[] = $path;
            }
        }

        return $paths;
    }

    public function copyDirectory($oldPath, $newPath)
    {
        // get source files
        $files = Storage::files($oldPath);

        // add to new directory
        foreach ($files as $file) {
            $fileContent = Storage::get($file);
            $name = $this->generateUniqueName();
            $extension = $this->getExtension($file, false, true);
            $newPath = $newPath .'/'. $name .'.'. $extension;

            Storage::put($newPath, $fileContent, 'public');
        }
    }

    public function delete($path, $names = [])
    {
        if ($names) {
            $files = Storage::files($path);

            foreach ($files as $file) {
                $name = $this->getName($file);

                if (in_array($name, $names)) {
                    Storage::delete($file);
                }
            }
        } else {
            Storage::deleteDirectory($path);
        }
    }

    public function getByApi($path, $filter = [], $loopSubFolder = false, $outputURL = true)
    {
        // loop all images
        if ($loopSubFolder) {
            $files = Storage::allFiles($path);
        } else {
            $files = Storage::files($path);
        }

        // get meta from image
        $images = [];
        foreach ($files as $file) {
            // prepare variables
            $name = $this->getName($file);
            $extension = $this->getExtension($file, false, true);

            // skip mac system files & do filter
            if ($extension == 'DS_Store' || ($filter && !in_array($name, $filter))) {
                continue;
            }

            // output
            $images = $outputURL ? $this->getURL($file) : $this->getBlob($file);
        }

        return $images == [] ? null : $images;
    }

    public function getByApiMultiple($path, $filter = [], $loopSubFolder = false, $outputURL = true)
    {
        // loop all images
        if ($loopSubFolder) {
            $files = Storage::allFiles($path);
        } else {
            $files = Storage::files($path);
        }

        // get meta from image
        $images = [];
        foreach ($files as $file) {
            // prepare variables
            $name = $this->getName($file);
            $extension = $this->getExtension($file, false, true);

            // skip mac system files & do filter
            if ($extension == 'DS_Store' || ($filter && !in_array($name, $filter))) {
                continue;
            }

            // output
            $images[] = $outputURL ? $this->getURL($file) : $this->getBlob($file);
        }

        return $images;
    }

    public function getType($file)
    {
        $mime_map = [
            'video/3gpp2'                                                               => '3g2',
            'video/3gp'                                                                 => '3gp',
            'video/3gpp'                                                                => '3gp',
            'audio/x-acc'                                                               => 'aac',
            'audio/ac3'                                                                 => 'ac3',
            'audio/x-aiff'                                                              => 'aif',
            'audio/aiff'                                                                => 'aif',
            'audio/x-au'                                                                => 'au',
            'video/x-msvideo'                                                           => 'avi',
            'video/msvideo'                                                             => 'avi',
            'video/avi'                                                                 => 'avi',
            'image/bmp'                                                                 => 'bmp',
            'image/x-bmp'                                                               => 'bmp',
            'image/x-bitmap'                                                            => 'bmp',
            'image/x-xbitmap'                                                           => 'bmp',
            'image/x-win-bitmap'                                                        => 'bmp',
            'image/x-windows-bmp'                                                       => 'bmp',
            'image/ms-bmp'                                                              => 'bmp',
            'image/x-ms-bmp'                                                            => 'bmp',
            'image/cdr'                                                                 => 'cdr',
            'image/x-cdr'                                                               => 'cdr',
            'video/x-f4v'                                                               => 'f4v',
            'audio/x-flac'                                                              => 'flac',
            'video/x-flv'                                                               => 'flv',
            'image/gif'                                                                 => 'gif',
            'image/x-icon'                                                              => 'ico',
            'image/x-ico'                                                               => 'ico',
            'image/vnd.microsoft.icon'                                                  => 'ico',
            'image/jp2'                                                                 => 'jp2',
            'video/mj2'                                                                 => 'jp2',
            'image/jpx'                                                                 => 'jp2',
            'image/jpm'                                                                 => 'jp2',
            'image/jpeg'                                                                => 'jpeg',
            'image/pjpeg'                                                               => 'jpeg',
            'audio/x-m4a'                                                               => 'm4a',
            'audio/midi'                                                                => 'mid',
            'video/quicktime'                                                           => 'mov',
            'video/x-sgi-movie'                                                         => 'movie',
            'audio/mpeg'                                                                => 'mp3',
            'audio/mpg'                                                                 => 'mp3',
            'audio/mpeg3'                                                               => 'mp3',
            'audio/mp3'                                                                 => 'mp3',
            'video/mp4'                                                                 => 'mp4',
            'video/mp4'                                                                 => 'm4v',
            'video/mpeg'                                                                => 'mpeg',
            'audio/ogg'                                                                 => 'ogg',
            'video/ogg'                                                                 => 'ogg',
            'image/png'                                                                 => 'aaa',
            'image/x-png'                                                               => 'png',
            'image/vnd.adobe.photoshop'                                                 => 'psd',
            'audio/x-realaudio'                                                         => 'ra',
            'audio/x-pn-realaudio'                                                      => 'ram',
            'audio/x-pn-realaudio-plugin'                                               => 'rpm',
            'video/vnd.rn-realvideo'                                                    => 'rv',
            'image/svg+xml'                                                             => 'svg',
            'image/tiff'                                                                => 'tiff',
            'audio/x-wav'                                                               => 'wav',
            'audio/wave'                                                                => 'wav',
            'audio/wav'                                                                 => 'wav',
            'video/webm'                                                                => 'webm',
            'audio/x-ms-wma'                                                            => 'wma',
            'video/x-ms-wmv'                                                            => 'wmv',
            'video/x-ms-asf'                                                            => 'wmv',
            'application/vnd.android.package-archive'                                   => 'apk',

        ];
        $file = $this->getExtension($file, false, true);
        $variable = explode('?', $file);
        $extension = $variable[0];
        if ($extension == 'mp4') {
            $extension = 'm4v';
        }
        return (explode("/", array_search($extension, $mime_map)))[0];
    }

    public function generateThumbnail($file, $id)
    {
        //ffmpeg.exe run command
        $ffmpeg = config('ffmpeg.path');

        //video dir
        $video = '/app/storage/app/public/' . $file[0];

        //where to save the image
        $image_path = public_path('thumbnails/'.$id.'.jpeg');
        $path = public_path('thumbnails/');
        if (!File::exists($path)) {
            File::makeDirectory($path);
        }
        if (file_exists($image_path)) {
            @unlink($image_path);
        }
        //time to take screenshot at
        $interval = 5;

        //screenshot size
        $size = '500x300';

        //ffmpeg command
        $cmd = "ffmpeg -i $video -deinterlace -an -ss $interval -f mjpeg -t 1 -r 1 -y -s $size $image_path 2>&1";
        shell_exec($cmd);
    }

    public function getDuration($file){
        //video dir
        $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 -sexagesimal $file 2>&1";
        $duration = shell_exec($cmd);//ffprobe if fail response will be ""
        return $duration;
    }
}
