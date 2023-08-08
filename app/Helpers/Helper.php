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
    public static function decryptFile($filename)
    {
        if (Storage::exists($filename)) {
            //FileVault::decryptCopy($filename . '.enc', Str::replaceLast('.enc', '', $filename));
            return storage_path('app/'. $filename) ;
        }
       abort('404', 'File Not Found.');
    }
}