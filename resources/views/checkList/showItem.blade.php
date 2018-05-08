<div class="objectif">
  <form method="post" action="id/{{$item->id}}">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="form-group">
      <label for="name">Nom</label>
      <input id="name" name="title" type="text" class="form-control" value="{{$item->title}}">
    </div>
    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" class="form-control">{{$item->description}}</textarea>
    </div>
    <button type="submit" class="btn btn-default">Sauvegarder</button>
  </form>
  <br>
  <br>
  @if(isset($scenarios))
  <form method="post" action="{{$projectId}}/scenario">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
    <div class="form-group">
      <label>Scénarios</label>
      @if(count($scenarios)>0)
      <div class="container">
        @foreach($scenarios as $scenario)
        <div class="row">
          <div class="col-xs-10">
            <label>{{$scenario->name}}</label>
          </div>
          <div class="col-xs-1">
            <a href="{{route('scenario.show', array('projectId'=>$projectId, 'stepId'=>$scenario->id))}}" class="btn btn-primary pull-rigth"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
          </div>
          <div class="col-xs-1">
            <a href="{{route('scenario.delete', array('projectId'=>$projectId, 'stepId'=>$scenario->id))}}" class="btn btn-danger pull-rigth"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <p>Aucun scénario disponible</p>
      @endif
    </div>
    <a data-id="{{$item->id}}" data-projectid="{{$projectId}}" data-URL="{{ URL('project') }}" class="btn btn-default addScenario">Créer nouveau scénario</a>
  </form>

  @endif
</div>
