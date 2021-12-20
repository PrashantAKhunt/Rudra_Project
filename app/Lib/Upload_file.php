<?php

namespace App\Lib;
use Illuminate\Support\Facades\Storage;
class Upload_file {

    private static $s3_link = "https://rtplhrms.sfo2.digitaloceanspaces.com/";
    
    public static function upload_chat_file($request) {  //this
        $file = $request->file('file');
        $extension=$file->getClientOriginalExtension();
        $new_file_name=time().rand(10000,99999).'.'.$extension;
        $response=Storage::disk('s3')->put('chatWappnet/'.$new_file_name,file_get_contents($file),'public');
        
        //$file_path = $video_file->move('video_file',$new_file_name,'public');
        
        if ($response) {
            return ['status'=>true,'storage_path'=>$new_file_name];
        }
        else{
            return ['status'=>false];
        }
    }
    

    public static function get_file_path($file_type,$file_name) {  //this
        
        switch ($file_type) {
            case 'chat':
                //$full_path=asset("audio_file/".$file_name);
                
                $full_path= self::$s3_link."chatWappnet/".$file_name;
                break;
            
            default:
                $full_path="";
                break;
        }
        return $full_path;
    }

    //-------------------------------------- aws s3-------------------------

    public function upload_s3_file($file, $storageFolder)
    {
        $extension=$file->getClientOriginalExtension();
        $new_file_name=time().rand(10000,99999).'.'.$extension;
        $response=Storage::disk('s3')->put($storageFolder.$new_file_name,file_get_contents($file),'public');
        
        if ($response) {
            return $new_file_name;
        }
        else{
            return '';
        }
    }

    public function get_s3_file_path($folder, $file_name)
    {
        $full_path= self::$s3_link.$folder.$file_name;
        return $full_path;
    }



}
