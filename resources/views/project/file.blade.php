<div class="col-xs-12 col-lg-6">
  <div class="panel panel-default">
      <!-- Display all project informations like the members, a description and so on -->
      <div class="panel-heading showPanel" data-toggle="collapse" data-target="#filesInfo">
        <h1>Fichiers <span class="glyphicon glyphicon-chevron-down pull-right"></h1>
      </div>
      <div id="filesInfo" class="panel-body filesInfo collapse" data-projectid="{{$project->id}}">
          <div class="panel panel-default" id="files">
              @if(Auth::user()->projects()->find($project->id))
                  <div class="panel-heading">Ajoutez des fichiers</div>
                  <div class="panel-body">
                      <div class="container">
                      <form enctype="multipart/form-data" action="{{route('project.files.store', $project->id)}}" method="post" id="sendFile">
                          {!! csrf_field() !!}

                          <label class="col-md-4 control-label">Description du fichier</label>

                          <div class="col-md-6">
                              <input type="texte" class="form-control" name="description" value="" required>
                          </div>

                          <label class="col-md-4 control-label">Le fichier</label>

                          <div class="col-md-6">
                              <input type="file" name="file">
                          </div>

                          <div class="col-md-6">
                              <input type="submit" value="Envoyer">
                          </div>

                          </form>
                      </div>
                  </div>
              @endif

              <div class="panel-heading">Fichiers du projet</div>
              <div class="panel-body">
                  <div class="container files">
                      @foreach($project->files as $file)
                          <div class="file">
                              <a href="{{asset('files/'.$project->id.'/'.$file->url)}}" download="{{$file->name}}">
                              <img class="" src="{{asset('images/icon/'.$file->mime.'.png')}}">
                              <p>{{$file->name}}</p>
                              <p>{{$file->description}}</p>
                              <p>{{round($file->size / (1024*1024), 2)}} MB</p>
                              </a>
                              <button class="right btn filedestroy" data-project="{{$project->id}}"
                              data-id="{{$file->id}}">
                              <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                          </div>
                      @endforeach
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
