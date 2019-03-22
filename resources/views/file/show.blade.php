@extends('layouts.project')

@section('projectContent')
  <div class="panel panel-default">
      <!-- Display all project informations like the members, a description and so on -->
      <div class="panel-heading">
        <h1>Fichiers</h1>
      </div>
      <div id="filesInfo" class="panel-body filesInfo" data-projectid="{{$project->id}}">
          <div class="panel panel-default" id="files">
              @if(Auth::user()->projects()->find($project->id))
                  <div class="panel-heading">Ajoutez des fichiers</div>
                  <div class="panel-body">
                      <div class="container">
                      <form enctype="multipart/form-data" action="{{route('project.files.store', $project->id)}}" method="post" id="sendFile">
                          {{ csrf_field() }}

                          <label class="col-md-4 control-label">Description du fichier</label>

                          <div class="col-md-6">
                              <input type="texte" class="form-control" name="description" value="" required>
                          </div>

                          <label class="col-md-4 control-label">Le fichier</label>

                          <div class="col-md-6">
                              <input type="file" name="file" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" multiple="">
                              <label for="file-1"><span class="fileselector">Choose File</span> </label>
                          </div>

                          <div class="col-md-6">
                              <button type="submit" class="btn btn-primary" value="Envoyer" aria-hidden="true">Upload</button>
                          </div>

                          @if ($errors->has('file'))
                              <div class="alert alert-danger col-md-3" role="alert">
                                  {{ $errors->first('file') }}
                              </div>
                          @endif

                          </form>
                      </div>
                  </div>
              @endif

              <div class="panel-heading">Fichiers du projet</div>
              <div class="panel-body">

                  <div class="container files dynamic-layout">
                      @foreach($project->files as $file)
                          <div class="file">
                              <a href="{{asset('files/'.$project->id.'/'.$file->url)}}" download="{{$file->name}}">
                              @if(\Illuminate\Support\Str::startsWith($file->mime, 'image/'))
                                  <img class="" src="{{asset('files/'.$project->id.'/'.$file->url)}}">
                              @endif
                              <p>{{$file->name}}</p>
                              <p>{{$file->description}}</p>
                              <p>{{round($file->size / (1024*1024), 2)}} MB</p>
                              </a>
                              {{ method_field('DELETE') }}
                              {{ csrf_field() }}
                              {{ Form::open([
                                  'method' => 'DELETE',
                                  'route' => ['project.files.destroy', $project->id , $file->id]
                              ]) }}
                              {{ Form::submit('X', ['class' => 'btn btn-danger'])}}
                              {{ Form::close() }}
                          </div>
                      @endforeach
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection