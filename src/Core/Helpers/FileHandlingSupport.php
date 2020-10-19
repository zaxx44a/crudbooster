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
                    static::resizeImage($filePath.'/'.$newFilename);
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

    public static function uploadFile($inputName, $overrideFileName = null, $encrypt = false)
    {
        $file = request()->file($inputName);
        $ext = $file->getClientOriginalExtension();
        $filename = str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $fileSize = $file->getSize() / 1024;
        $filePath = 'uploads/'.date('Y-m');

        //Create Directory Monthly
        Storage::makeDirectory($filePath);

        if ($encrypt === true) {
            $filename = md5(str_random(5)).'.'.$ext;
        } else {
            $filename = str_slug($filename, '_').'.'.$ext;
        }

        if (Storage::putFileAs($filePath, $file, $filename)) {
            self::resizeImage($file_path.'/'.$filename, $resize_width, $resize_height);

            return $file_path.'/'.$filename;
        } else {
            return null;
        }
    }

    private static function resizeImage($fullFilePath, $resize_width = null, $resize_height = null, $qty = 100, $thumbQty = 75)
    {
        $images_ext = config('crudbooster.IMAGE_EXTENSIONS', 'jpg,png,gif,bmp');
        $images_ext = explode(',', $images_ext);

        $filename = basename($fullFilePath);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $file_path = trim(str_replace($filename, '', $fullFilePath), '/');

        $file_path_thumbnail = 'uploads_thumbnail/'.date('Y-m');
        Storage::makeDirectory($file_path_thumbnail);

        if (in_array(strtolower($ext), $images_ext)) {

            if ($resize_width && $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->fit($resize_width, $resize_height);
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } elseif ($resize_width && ! $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->resize($resize_width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } elseif (! $resize_width && $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->resize(null, $resize_height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } else {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                if ($img->width() > 1300) {
                    $img->resize(1300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            }

            $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
            $img->fit(350, 350);
            $img->save(storage_path('app/'.$file_path_thumbnail.'/'.$filename), $thumbQty);
        }
    }

}