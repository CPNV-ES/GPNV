<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\File as Filestorage;
use Auth;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Http\Requests;

class FileController extends Controller
{

    /**
    * Show file
    */
    public function index($project){
        $project = Project::find($project);
        return view('file/show',['project' => $project]);
    }

    /**
    * Save file
    * @param $project The project item
    * @param $id The file item id
    * @param $request Define the request data send by POST
    * @return view project
    */
    public function store(Project $project, Request $request, $id){
        $file = Input::file('file');

        $destinationPath = 'files/'.$id.'/';

        $fileArray = array('files' => $file);

        $rules = array(
            'files' => 'required|max:1000000'
        );

        $validator = Validator::make($fileArray, $rules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->getMessages()], 400);
        } else {
            $extension = $file->getClientOriginalExtension();
            $hash = $file->hashName();
            $fileName = $file->getClientOriginalName();
            $file->move($destinationPath, $hash);
            $store = new File;
            $store->name = $file->getClientOriginalName();
            $store->description = $request->input('description');
            $store->mime = $extension;
            $store->size = $file->getClientSize();
            $store->url = $hash;
            $store->project_id = $id;
            $store->save();
        };

        return redirect()->route("project.files.index", ['id'=>$id]);

    }

    /**
    * Remove file
    * @param $id The project id
    * @param $file The file item
    */
    public function destroy($id,File $file){
        if(Storage::disk('local')->exists('public/files/'.$id.'/'.$file->url)){
            Storage::delete('public/files/'.$id.'/'.$file->url);
        }else{
            echo "File not exist";
        }

        File::where('id','=',$file->id)->delete();
    }
}
