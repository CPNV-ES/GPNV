<form class="form" role="form" method="POST" action="{{ route('deliveries.link', $project->id) }}">
  {!! csrf_field() !!}

  <script>
      $(document).ready(function () {
        $('button.change').click(function () {
          if($(this).text()=="Choisir un fichier"){
            $(this).text("Choisir un lien");
            $('fieldset#file').prop("disabled", false);
            $('fieldset#url').prop("disabled", true);
            $('input.valueType').val("url");
          }
          else{
            $(this).text("Choisir un fichier");
            $('fieldset#url').prop("disabled", false);
            $('fieldset#file').prop("disabled", true);
            $('input.valueType').val("file");
          }
        });
      });
  </script>

  @if(count($files)!=0)
    <fieldset class="form-group" id="file" disabled>
    <legend>Fichiers</legend>
      @foreach($files as $file)
        <div class="form-check">
          <label class="form-check-label">
            <input type="radio" class="form-check-input" value="{{$file->id}}" name="data">
            {{$file->name}}
          </label>
        </div>
      @endforeach
    </fieldset>
  @else
    <div>Aucun fichier n'est disponible pour ce projet</div>
  @endif

  <input type="hidden" class="valueType" name="type" value="url">
  <input type="hidden" name="check" value="{{$checkID}}">

  <fieldset class="form-group" id="url">
  <legend>Lien</legend>
    <label for="url">URL: </label>
    <input type="url" name="data" class="form-control" id="url" aria-describedby="url" placeholder="Entrez l'URL ici">
  </fieldset>

  <br>
  <div class="form-group">
     <div class="col-md-6 col-md-offset-4">
        @if(count($files)!=0)
          <button type="button" class="btn btn-secondary change">Choisir un fichier</button>
        @endif
        <button type="submit" class="btn btn-primary pull-right">
          <i class="fa fa-btn fa-plus"></i>Lier le lien
        </button>
     </div>
  </div>
</form>
