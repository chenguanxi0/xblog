<?php
/**
 * Created by PhpStorm.
 * User: lufficc
 * Date: 2016/9/17
 * Time: 17:10
 */

namespace Lufficc;

use Storage;

class FileUploadManager
{
    public function uploadFile($key, $filePath)
    {
        $disk = Storage::disk('qiniu');
        if ($disk->put($key, file_get_contents($filePath))) {
            return true;
        } else {
            return false;
        }
    }

    public function url($key)
    {
        $disk = Storage::disk('qiniu');
        return $disk->url($key);
    }

    public function deleteFile($key)
    {
        $disk = Storage::disk('qiniu');
        if ($disk->delete($key)) {
            return true;
        } else {
            return false;
        }
    }
}