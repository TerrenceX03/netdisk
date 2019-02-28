<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use App\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use DB;

class fileController extends Controller{
    public function upload(Request $request){
        if($request->hasFile('file')){
            $file = $request->file('file');
            if(!$file->isValid()){
                return 'file upload error';
            }
            //set file directory
            $destPath = realpath(base_path('public/upload'));
            if(!file_exists($destPath)){
                mkdir($destPath, 0777, true);
            }
            //create file model attributes
            $filename = $file->getClientOriginalName();
            $fileType = $file->getClientOriginalExtension();
            $fileSize = filesize($file);

            if(!file_exists($destPath.'/'.$filename)){
                if(!$path = $file->move($destPath, $filename)){
                    return 'file saving error';
                }
                $fileAttributes = ['name'=>$filename, 'directory'=>$destPath, 'type'=>$fileType, 'size'=>$fileSize];
                $tableFile = File::create($fileAttributes);
                // echo $filename.'<br>';
                // echo $path.'<br>';
                // echo 'file saved<br>';
                return response()->json($tableFile, 201);
            }else{
                return 'file duplicated';
            }
        }
        return 'uploaded';
    }

    public function download(Request $request){
        $id = $request->get('id');
        if($id != NULL){
            $file = DB::table('files')->select('name')->where('id', '=', $id)->value('name');
            $directory = DB::table('files')->select('directory')->where('id', '=', $id)->value('directory');
            $path = $directory.'/'.$file;
            if($file != NULL && $directory != NULL){
                if(!file_exists($path)){
                    File::findOrFail($id)->delete();
                    return 'no such file';
                }
            }else{
                return 'no such file';
            }
        }
        return response()->download($path, $filename);
    }

    public function delete(Request $request){
        $id = $request->get('id');
        if($id != NULL){
            $file = DB::table('files')->select('name')->where('id', '=', $id)->value('name');
            $directory = DB::table('files')->select('directory')->where('id', '=', $id)->value('directory');
            $path = $directory.'/'.$file;
            if($file != NULL && $directory != NULL){
                if(!file_exists($path)){
                    File::findOrFail($id)->delete();
                    return 'no such file';
                }
            }else{
                return 'no such file';
            }
        }
        unlink($path);
        return $file.' deleted';
    }

    public function list(){
        return response()->json(File::all());
    }
    
    public function update($id){
        $file = File::findOrFail($id);
        $file->update($request->all());
        return response()->json(File::all());
    }

    public function setPath($filename){
        return realpath(base_path('public/upload')).'/'.$filename;
    }

}

