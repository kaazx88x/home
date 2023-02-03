<?php

namespace App\Helpers;
use App\Repositories\S3ClientRepo;
use Image;
use Carbon\Carbon;
use Excel;
use Crypt;
use Jenssegers\Agent\Agent;

class Helper
{
    public static function upload_image($file, $main_image, $mer_id)
    {
        try
        {
            $upload_file = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $file_detail = explode('.', $upload_file);
            $image_name = date('Ymd').'_'.str_random(4).'.'.$extension;
            $temp_path = public_path().'/export/';
            $file->move($temp_path, $image_name);

            $compressed_image = $temp_path.$image_name;

            $info = getimagesize($compressed_image);

            switch ($info['mime']) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($compressed_image);
                    imagejpeg($image, $compressed_image, 50);
                    break;

                case 'image/png':
                    $image = imagecreatefrompng($compressed_image);
                    imagesavealpha($image, true);
                    imagepng($image, $compressed_image, 5);
                    break;
            }

            $path = 'product/'.$mer_id;
            if(@file_get_contents($compressed_image) && !S3ClientRepo::IsExisted($path, $image_name))
                S3ClientRepo::Upload($path, $compressed_image, $image_name);

            if($main_image == 1) {
                $thumbnail_name = 'thumbnail_'.$image_name;
                //create temp thumbnail image
                $thumbnail_image = Image::make($compressed_image);
                $thumbnail_image->resize(null, 250, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $thumbnail_image->save($temp_path.$thumbnail_name);
                $thumbnail_image = $temp_path.$thumbnail_name;

                if(!S3ClientRepo::IsExisted($path, $thumbnail_name))
                    S3ClientRepo::Upload($path, $thumbnail_image, $thumbnail_name);

                @unlink($thumbnail_image); //remove thumbnail file
            }

            @unlink($compressed_image); //remove compressed file

            return $image_name;

        } catch (\Exception $e) {
            return false;
        }
    }

    public static function set_product_thumbnail_image($new_thumbnail_name, $old_thumbnail_name, $pro_id, $mer_id)
    {
        $path = 'product/'.$mer_id;
        if(S3ClientRepo::IsExisted($path, $old_thumbnail_name))
            S3ClientRepo::Delete($path, $old_thumbnail_name);

        $temp_path = public_path().'/export/'.$new_thumbnail_name;
        $thumbnail_image = Image::make(S3ClientRepo::Get($path, str_replace('thumbnail_','', $new_thumbnail_name)));
        $thumbnail_image->resize(null, 250, function ($constraint) {
            $constraint->aspectRatio();
        });
        $thumbnail_image->save($temp_path);
        $thumbnail_image = $temp_path;

        if(!S3ClientRepo::IsExisted($path, $new_thumbnail_name))
            $upload = S3ClientRepo::Upload($path, $thumbnail_image, $new_thumbnail_name);

        @unlink($thumbnail_image); //remove thumbnail_image from export folder
    }

    public static function UTCtoTZ($datetime, $format = 'd F Y h:i A')
    {
        $tz = 'Asia/Kuala_Lumpur';
        if (\Cookie::get('timezone') != null)
            $tz = \Cookie::get('timezone');
        elseif (\Session::has('timezone'))
            $tz = \Session::get('timezone');

        return Carbon::createFromTimestamp(strtotime($datetime))->timezone($tz)->format($format);
    }

    public static function TZtoUTC($datetime)
    {
        $tz = 'Asia/Kuala_Lumpur';
        if (\Cookie::get('timezone') != null)
            $tz = \Cookie::get('timezone');
        elseif (\Session::has('timezone'))
            $tz = \Session::get('timezone');

        return Carbon::createFromFormat('Y-m-d H:i:s', $datetime, $tz)->setTimezone('UTC')->toDateTimeString();
    }

    public static function encrypt($id) {
        return Crypt::encrypt($id);
    }

    public static function decrypt($id)
    {
        try {
            return Crypt::decrypt($id);
        } catch (\Exception $e) {

            //can insert into error log later
            // $ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
            // \Request::fullUrl();
            // $e->getMessage();

            return 0;
        }
    }

    public static function export($filename, $extension = 'csv', $contents)
    {
        $time = time();
        foreach ($contents as $file_no => $content) {
            $file = 'File-'.($file_no+1).'_'.$filename.'-'.$time;
            $files[] = $file.'.'.$extension;
            // dd($content);
            Excel::create($file, function($excel) use($content) {
                $excel->sheet('Sheetname', function($sheet) use($content) {
                    $sheet->fromArray($content, null, 'A1', false, false);
                });

            })->store($extension, public_path().'/export');
        }


        $zip = new \ZipArchive();
        $zip_name = public_path().'/export/'.$filename.'-'.date('Ymd_His').".zip"; // Zip name

        $zip->open($zip_name, \ZipArchive::CREATE);
        foreach ($files as $file) {
            $path = public_path().'/export/'.$file;
            if(file_exists($path)){
                $zip->addFromString(basename($path), file_get_contents($path));
                unlink($path);
            }
        }
        $zip->close();

        return $zip_name;
    }

    public static function securePhone($phone)
    {
        return substr_replace($phone, str_repeat("*", strlen($phone)-3), 0, -3);
    }

    public static function secureEmail($email)
    {
        if ($email) {
            $mail_segments = explode("@", $email);
            $count_name = strlen($mail_segments[0]);

            if ($count_name >= 6) {
                $mail_segments[0] = substr_replace($mail_segments[0], str_repeat("*", $count_name-3), 3);
            } else {
                $mail_segments[0] = substr_replace($mail_segments[0], str_repeat("*", $count_name-2), 2);
            }


            return implode("@", $mail_segments);
        }

        return null;
    }

    public static function agent($type = 'mobile')
    {
        $agent = new Agent();
        switch ($type) {
            case 'mobile':
                if($agent->isMobile() || $agent->isTablet()) {
                    return true;
                }
                break;

            case 'android':
                if($agent->isAndroidOS()) {
                    return true;
                }
                break;

            case 'desktop':
                if($agent->isDesktop()) {
                    return true;
                }
                break;

            default:
                return false;
                break;
        }

        return false;
    }

}