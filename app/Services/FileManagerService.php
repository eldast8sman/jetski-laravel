<?php

namespace App\Services;

use App\Models\FileManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerService
{
    public static function fetch_file($file)
    {
        $file = FileManager::find($file);
        if(empty($file)){
            return false;
        }

        return $file;
    }

    public static function findByUrl($url){
        $file = FileManager::where('url', $url)->first();
        if(empty($file)){
            return false;
        }
        return $file;
    }

    public static function upload_file(UploadedFile $file, $disk='public')
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::random(20).time().'.'.$extension;
        if(($extension == 'jpg') || ($extension == 'jpeg') || ($extension == 'png') || ($extension == 'gif') || ($extension == 'svg') || ($extension == 'webp') || ($extension == 'jfif')){
            $path = 'images';
        } elseif(($extension == 'pdf') || ($extension == 'docx') || ($extension == 'csv') || ($extension == 'xlsx') || ($extension == 'doc')){
            $path = 'documents';
        } elseif(($extension == 'mp3') || ($extension == 'mpeg3') || ($extension == 'wav') || ($extension == 'aac')){
            $path = 'audios';
        } elseif(($extension == 'mp4') || ($extension == 'mpeg4') || ($extension == 'avi') || ($extension == 'mov') || ($extension == 'mkv')){
            $path = 'videos';
        } else {
            $path = 'others';
        }
        $upload = Storage::disk($disk)->putFileAs($path, $file, $filename);
        if(!$upload){
            return false;
        }

        $manager = FileManager::create([
            'disk' => $disk,
            'path' => $upload,
            'url' => Storage::disk($disk)->url($upload),
            'size' => 2000, //Storage::disk($disk)->size($upload),
            'extension' => $extension,
            'filename' => $filename
        ]);

        return $manager;
    }

    public static function delete($file) : bool
    {
        $file = FileManager::find($file);
        if(empty($file)){
            return false;
        }
        // if(!Storage::disk($file->disk)->exists($file->path)){
        //     return false;
        // }

        Storage::disk($file->disk)->delete($file->path);

        $file->delete();

        return true;
    }
}