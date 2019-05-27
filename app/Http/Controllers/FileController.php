<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\File;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\File as Filestorage;
use Auth;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Http\Requests;
use App\Http\Requests\UploadImageRequest;

class FileController extends Controller
{
    /**
    * Show file
    */
    public function index(Project $project){
        return view('file/show',['project' => $project]);
    }

    /**
    * Save file
    * @param $project The project item
    * @param $id The file item id
    * @param $request Define the request data send by POST
    * @return view project
    */
    public function store(UploadImageRequest $request, Project $project){

        $file = Input::file('file');

        $destinationPath = 'files/'.$project->id.'/';

        $hash = $file->hashName();
        $store = new File;
        $store->name = $file->getClientOriginalName();
        $store->description = $request->input('description');
        $store->mime = $file->getMimeType();
        $store->size = $file->getClientSize();
        $store->url = $hash;
        $store->project_id = $project->id;
        $file->move($destinationPath, $hash);
        $store->save();

        return view('file/show',['project' => $project]);

    }

    /**
    * Remove file
    * @param $id The project id
    * @param $file The file item
    public function destroy($id, File $file){
        if(Storage::disk('local')->exists('public/files/'.$id.'/'.$file->url)){
            Storage::delete('public/files/'.$id.'/'.$file->url);
        }else{
            echo "File not exist";
        }

        File::where('id','=',$file->id)->delete();
    }
     **/

    public function destroy($project, $file){

        File::findOrFail($file)->delete();

        Session::flash('flash_message', 'Image successfully deleted');

        return redirect()->route('project.files.index', $project);
    }
}
