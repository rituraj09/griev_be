<?php
namespace App\Helpers; 
use DB, Auth, Storage, Str;
class Helper
{
    public static function storeFile($file, $filePath = null)
    {
        if (!$file){
            return null;
        }
        $filename = Storage::putFile(config('app.file_storage_path') . $filePath, $file); 
        return $filename;
    }
}