<form class="form" role="form" method="POST"
      action="{{ URL('project') . "/" . $project->id . "/link/" . $checkListItem->id, $project->id }}">
    {!! csrf_field() !!}

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <legend><input type="checkbox" checked data-toggle="toggle" data-on="URL" data-off="Fichiers"
                           data-onstyle="info" data-offstyle="info" class="toggleDeliverable" id="toggleDeliverable"
                           data-width="100" @if(count($files)!=0) ></legend> @else disabled><p
                    class="text-danger msg-no-file"> Aucun fichier n'est disponible pour ce projet</p></legend> @endif
            <fieldset class="form-group" id="url">
                <div class="col-md-12 col-sm-12 urlform{{$checkListItem->id}}">
                    <input type="url" name="data" class="form-control url-input{{$checkListItem->id}}" id="url"
                           aria-describedby="url"
                           placeholder="Entrez l'URL ici" required>
                </div>
            </fieldset>
            <div class="col-md-12 col-sm-12 hidden fileform{{$checkListItem->id}}">
                @if(count($files)!=0)
                    <fieldset class="form-group" id="file">
                        @foreach($files as $file)
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="file-input{{$checkListItem->id}}" value="{{$file->id}}"
                                           name="data">
                                    {{$file->name}}
                                </label>
                            </div>
                        @endforeach
                    </fieldset>
                @else
                @endif
                <input type="hidden" class="valueType" name="type" value="url">
                <input type="hidden" name="check" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary to-link{{$checkListItem->id}}">
                <i class="fa fa-btn fa-plus"></i>Lier le lien
            </button>
        </div>
    </div>
</form>

@push('projectScripts')
<script> $(function () {
        $('#toggleDeliverable').bootstrapToggle();
    }) </script>
@endpush