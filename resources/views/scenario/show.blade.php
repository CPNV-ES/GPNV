@extends('layouts.app')

@section('content')
<div class="scenario container">
  <div class="row">
    <div class="col-xs-12">
      <a href="{{route('project.show', $projectId)}}" class="btn btn-primary btn-retour">
        <span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span>Retour au projet
      </a>
    </div>
  </div>
  <div class="row">
    <form method="POST" action="{{route('scenario.modify', array('projectId' => $projectId, 'scenarioId' => $scenario->id))}}" class="col-xs-12 col-md-12 ">
      {{ csrf_field() }}
      {{ method_field('PUT') }}
      <div class="form-group">
        <label for="name">Nom:</label>
        <input class="form-control" type="text" name="name" value="{{$scenario->name}}">
      </div>
      <div class="form-group">
        <label for="description">Description:</label>
        <textarea class="form-control" name="description" rows="8">{{$scenario->description}}</textarea>
      </div>
      <div class="form-group">
        <input @if($scenario->actif == 1) checked @endif name="actif" type="checkbox" data-toggle="toggle" data-onstyle="success" data-on="Validé" data-off="Non Validé">
        <input @if($scenario->test_validated == 1) checked @endif name="test_validated" type="checkbox" data-toggle="toggle" data-onstyle="success" data-on="Testé et Validé" data-off="Pas testé">
        <button class="btn btn-warning pull-right">Modifier</button>
      </div>
    </form>
  </div>
  <div class="row">
    <div class="elements col-xs-12 col-md-6">
      <h2>Etapes</h2>
      <div class="table">
        <div class="tableRow">
          <div class="cell">#</div>
          <div class="cell">Action</div>
          <div class="cell">Réponse</div>
          <div class="cell">Modif</div>
        </div>
        <?php $order=0;?>
        @foreach($scenario->steps as $step)
          <?php $order++?>
          <form method="post" class="tableRow" action="{{route('scenario.item.modify', array('projectId' => $projectId, 'scenarioId' => $scenario->id, 'itemId' => $step->id))}}">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <input type="hidden" name="id" value="{{$step->id}}">
            <input type="hidden" name="order" value="{{ $step->order }}">
            <input type="hidden" name="mockup" value="@if(isset($step->mockup)) {{$step->mockup->id}} @endif">
            <input type="hidden" name="mockupUrl" value="@if(isset($step->mockup)) {{ URL::asset('mockups/'.$projectId.'/'.$step->mockup->url)}} @endif">
            <input type="hidden" name="oldAction" value="{{ $step->action }}">
            <input type="hidden" name="oldReponse" value="{{ $step->result }}">
            <div class="cell" name="order">{{ $order }}</div>
            <div class="cell"><textarea onclick="resetStepColor(this)" onblur="updateStep(this.form, this)" name="action" class="form-control">{{ $step->action }}</textarea></div>
            <div class="cell"><textarea onclick="resetStepColor(this)" onblur="updateStep(this.form, this)" name="reponse" class="form-control">{{ $step->result }}</textarea></div>
            <div class="cell">
              <a href="{{route('scenario.del.item', array('projectId'=>$projectId, 'stepId'=>$step->id))}}" name="submit" class="btn btn-danger">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
              </a>
            </div>
          </form>
        @endforeach
        <form method="post" class="tableRow" action="{{route('scenario.create.item', array('projectId'=>$projectId, 'scenarioId'=>$scenario->id))}}">
          {{ csrf_field() }}
          {{ method_field('POST') }}
          <div class="cell"></div>
          <div class="cell"><textarea id="action" name="action" class="form-control"></textarea></div>
          <div class="cell"><textarea id="reponse" name="reponse" class="form-control"></textarea></div>
          <div class="cell"><button class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button></div>
        </form>
      </div>
    </div>
    <div class="maquette col-xs-12 col-md-6">
      <h2>Image</h2>
      <div ondrop="drop(event)" ondragover="allowDrop(event)">
        <a href="{{ URL::asset('mockups/thumbnail-default.jpg') }}" target="_blank">
          <img src="{{ URL::asset('mockups/thumbnail-default.jpg') }}"/>
        </a>
      </div>
    </div>
    <div class="col-xs-12 col-md-12">
      <h2>Images disponibles</h2>
      <div class="col-xs-12 maquettes">
        @foreach($mockups as $mockup)
        <div style="padding:2px;">
          <img src="{{ URL::asset('mockups/'.$projectId.'/'.$mockup->url)}}" id='{{$mockup->id}}' style="max-width:100%; max-height: 200px;" draggable="true" ondragstart="drag(event)">
        </div>
        @endforeach
      </div>
      <div class="col-xs-12">
        <h4>Ajouter une Image</h4>
        <form id="uploadMockup" enctype="multipart/form-data" action="{{route('scenario.uploadMaquete', array('projectId'=>$projectId, 'scenarioId'=>$scenario->id))}}" method="post">
          {{ csrf_field() }}
          {{ method_field('POST') }}
          <div class="form-group">
            <input type="file" name="maquette" class="form-control" required>
          </div>
          <div class="form-group">
            <button name="button" class="btn btn-warning">Ajouter une image</button>
            <div ondrop="delPicture(event)" ondragover="allowDrop(event)" class="btn btn-danger pull-right">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
 var update_image_route = "{{ route('scenario.changeMaquete', array('projectId'=>$projectId, 'scenarioId'=>$scenario->id)) }}";
 var del_image_route = "{{route('scenario.delMaquete', array('projectId'=>$projectId, 'scenarioId'=>$scenario->id))}}";
</script>
@endsection
