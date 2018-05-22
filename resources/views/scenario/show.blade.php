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


    <!--        NAME / DESCRIPTION        -->
    <div class="row">
      <form method="POST" action="{{route('scenario.modify', array('projectId' => $projectId, 'scenarioId' => $scenario->id))}}" class="col-xs-12 col-md-12 ">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <div class="form-group">
          <label for="name">Nom:</label>
          <input id="projectNameInput" class="form-control hidden" type="text" name="name" value="{{$scenario->name}}">
          <p id="projectNameP">{{ $scenario->name }}</p>
        </div>
        <div class="form-group">
          <label for="description">Description:</label>
          <textarea id="projectDescriptionInput" class="form-control hidden" name="description" rows="8">{{$scenario->description}}</textarea>
          <p id="projectDescriptionP">{{ $scenario->description }}</p>
        </div>
        <div class="form-group">
          <input id="slideValidated" disabled @if($scenario->actif == 1) checked @endif name="actif" type="checkbox" data-toggle="toggle" data-onstyle="success" data-on="Validé" data-off="Non Validé">
          <input id="slideTested" disabled @if($scenario->test_validated == 1) checked @endif name="test_validated" type="checkbox" data-toggle="toggle" data-onstyle="success" data-on="Testé et Validé" data-off="Pas testé">
          <button id="saveDescription" class="btn btn-success pull-right hidden" onclick="saveDescription">Enregistrer</button>
          <button id="modifyDescription" class="btn btn-warning pull-right" type="button">Modifier</button>
          <button id="cancelDescription" class="btn btn-danger pull-right hidden" type="button" onclick="cancelDescription">Annuler</button>
        </div>
      </form>
    </div>


    <!--        STEP      -->
    <div class="row">
      <div class="elements col-xs-12 col-md-6">
        <h2>Etapes</h2>
        <div class="table">
          <div class="tableRow">
            <div class="cell">#</div>
            <div class="cell">Action</div>
            <div class="cell">Réponse</div>
            <div class="cell">Modif</div>
            <div id="delCellStep" class="cell hidden">Delete</div>
          </div>
            <?php $order=0;?>
          @foreach($scenario->steps as $step)
                <?php $order++?>
            <form id="formStep" method="post" class="tableRow" action="{{route('scenario.item.modify', array('projectId' => $projectId, 'scenarioId' => $scenario->id, 'itemId' => $step->id))}}">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              <input type="hidden" name="id" value="{{$step->id}}">
              <input type="hidden" name="order" value="{{ $step->order }}">
              <input type="hidden" name="mockup" value="@if(isset($step->mockup)) {{$step->mockup->id}} @endif">
              <input type="hidden" name="mockupUrl" value="@if(isset($step->mockup)) {{ URL::asset('mockups/'.$projectId.'/'.$step->mockup->url)}} @endif">
              <input type="hidden" name="oldAction" value="{{ $step->action }}">
              <input type="hidden" name="oldReponse" value="{{ $step->result }}">
              <div class="cell" name="order">{{ $order }}</div>

              <div class="cell">
                <textarea id ="stepAction_{{$step->id}}" onclick="resetStepColor(this)" onblur="updateStep(this.form, this)" name="action" class="form-control hidden">{{ $step->action }}</textarea>
                <p id ="stepAction1_{{$step->id}}"> {{ $step->action }}</p>
              </div>
              <div class="cell">
                <p id ="stepResult1_{{$step->id}}"> {{ $step->result }}</p>
                <textarea id ="stepResult_{{$step->id}}" onclick="resetStepColor(this)" onblur="updateStep(this.form, this)" name="reponse" class="form-control hidden">{{ $step->result }}</textarea>
              </div>

              <!-- Modify Step Cell -->
              <div class="cell">
                <button id="validateStep_{{$step->id}}" class="addStep hidden btn btn-success pull-right glyphicon glyphicon-ok" type="button"></button>

                <button id="modifyStep_{{$step->id}}" class="modifyStep btn btn-warning pull-right glyphicon glyphicon-edit" type="button"></button>
              </div>

              <!-- Delete Step cell-->
              <div>
                <a href="{{route('scenario.del.item', array('projectId'=>$projectId, 'stepId'=>$step->id))}}" id="delStep_{{$step->id}}" name="submit" class="btn btn-danger hidden" type="button">
                  <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </a>
              </div>
            </form>
          @endforeach

          <h2>Nouvelle etape</h2>
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


      <!--        IMAGES      -->
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
            <div class="col-md-4 col-xs-12 col-lg-3" style="padding:2px;">
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
