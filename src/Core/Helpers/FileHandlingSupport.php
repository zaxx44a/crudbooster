<?php

namespace Crocodic\CrudBooster\Core\Helpers;

use Illuminate\Support\Facades\Storage;

trait FileHandlingSupport
{

    /**
     * @param $base64Data
     * @param null $overrideFileName
     * @return string
     * @throws \Exception
     */
    public static function uploadBase64($base64Data, $overrideFileName = null)
    {
        $fileSize = ((strlen($base64Data) * (3/4)) - 2) / 1024;
        if($fileSize > config('crudbooster.MAX_UPLOAD_SIZE')) {
            throw new \Exception("File size can't more than ".config("crudbooster.MAX_UPLOAD_SIZE")." kb");
        }

        $fileData = base64_decode($base64Data);
        $fileInit = finfo_open();
        $mime = finfo_buffer($fileInit, $fileData, FILEINFO_MIME_TYPE);
        if($mime) {
            $mimeType = explode('/', $mime);
            $mimeType = $mimeType[1];
            if ($mimeType) {
                $filePath = 'uploads/' . date('Y-m');
                Storage::makeDirectory($filePath);
                $newFilename = ($overrideFileName) ?: md5(str_random(5)).'.'.$mimeType;
                if (Storage::put($filePath.'/'.$newFilename, $fileData)) {
                    return $filePath.'/'.$newFilename;
                } else {
                    throw new \Exception("System can't write file at $filePath");
                }
            } else {
                throw new \Exception("System can't find mime type");
            }
        } else {
            throw new \Exception("System can't find mime type");
        }
    }

    /**
     * @param $inputName
     * @param null $overrideFileName
     * @param false $encrypt
     * @return string
     * @throws \Exception
     */
    public static function uploadFile($inputName, $overrideFileName = null, $encrypt = false)
    {
        $file = request()->file($inputName);
        $fileSize = $file->getSize() / 1024;
        if($fileSize > config('crudbooster.MAX_UPLOAD_SIZE')) {
            throw new \Exception("File size can't more than ".config("crudbooster.MAX_UPLOAD_SIZE"). " kb");
        }

        $ext = $file->getClientOriginalExtension();
        $filename = str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filePath = 'uploads/'.date('Y-m');

        //Create Directory Monthly
        Storage::makeDirectory($filePath);

        if($overrideFileName) {
            $filename = $overrideFileName;
        } else {
            if ($encrypt === true) {
                $filename = md5(str_random(5)).'.'.$ext;
            } else {
                $filename = str_slug($filename, '_').'.'.$ext;
            }
        }

        if (Storage::putFileAs($filePath, $file, $filename)) {
            return $filePath.'/'.$filename;
        } else {
            throw new \Exception("System can't write file to $filePath");
        }
    }
}