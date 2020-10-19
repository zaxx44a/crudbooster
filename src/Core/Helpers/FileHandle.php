<?php

namespace Crocodic\CrudBooster\Core\Helpers;

trait FileHandle
{
    public static function uploadBase64($value, $id = null)
    {
        if (! self::myId()) {
            $userID = 0;
        } else {
            $userID = self::myId();
        }

        if ($id) {
            $userID = $id;
        }

        $filedata = base64_decode($value);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $filedata, FILEINFO_MIME_TYPE);
        @$mime_type = explode('/', $mime_type);
        @$mime_type = $mime_type[1];
        if ($mime_type) {
            $filePath = 'uploads/'.$userID.'/'.date('Y-m');
            Storage::makeDirectory($filePath);
            $filename = md5(str_random(5)).'.'.$mime_type;
            if (Storage::put($filePath.'/'.$filename, $filedata)) {
                self::resizeImage($filePath.'/'.$filename);

                return $filePath.'/'.$filename;
            }
        }
    }

    public static function uploadFile($name, $encrypt = false, $resize_width = null, $resize_height = null, $id = null)
    {
        if (Request::hasFile($name)) {
            if (! self::myId()) {
                $userID = 0;
            } else {
                $userID = self::myId();
            }

            if ($id) {
                $userID = $id;
            }

            $file = Request::file($name);
            $ext = $file->getClientOriginalExtension();
            $filename = str_slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filesize = $file->getClientSize() / 1024;
            $file_path = 'uploads/'.$userID.'/'.date('Y-m');

            //Create Directory Monthly
            Storage::makeDirectory($file_path);

            if ($encrypt == true) {
                $filename = md5(str_random(5)).'.'.$ext;
            } else {
                $filename = str_slug($filename, '_').'.'.$ext;
            }

            if (Storage::putFileAs($file_path, $file, $filename)) {
                self::resizeImage($file_path.'/'.$filename, $resize_width, $resize_height);

                return $file_path.'/'.$filename;
            } else {
                return null;
            }
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