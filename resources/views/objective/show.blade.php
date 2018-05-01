@extends('layouts.project')

@section('projectContent')
<div class="panel panel-default">
  <!-- Display all project informations like the members, a description and so on -->
  <div class="panel-heading showPanel" data-toggle="collapse" data-target="#objectives">
    <h1 >Objectifs <span class="glyphicon glyphicon-chevron-down pull-right"></span></h1>
  </div>
  <div id="objectives" class="panel-body objectives " data-projectid="{{$project->id}}">
    <div class="objectivesData">
      <div class="progressionCheckList">
        <div class="barre" style="background: linear-gradient(90deg, #20DE13 {{$objectifs->getCompletedPercent()}}%, #efefef 0%);"></div>
        <p>{{$objectifs->getNbItemsDone()}}/{{$objectifs->getNbItems()}}</p>
      </div>
      <div>
          <!-- Display all yourCheckList -->
          @if($objectifs->showToDo())
            @foreach($objectifs->showToDo() as $checkListItem)
              @unless($checkListItem->title == "Intérêt Général")
                @include('checkList.show', array('checkListItem'=>$checkListItem, 'modalBox' => true, 'projectId'=>$project->id, 'scenarios' => $objectifs->getScenariosState($checkListItem->id) ))
              @endunless
            @endforeach
          @endif

          @if($objectifs->showCompleted())
            @foreach($objectifs->showCompleted() as $checkListItem)
              @include('checkList.show', array('checkListItem'=>$checkListItem, 'modalBox' => true, 'projectId'=>$project->id))
            @endforeach
          @endif
      </div>
    </div>
    @if(Auth::user()->projects()->find($project->id))
      @include('objective.create')
      <a class="btn btn-primary newObjective">Ajouter</a>
    @endif
    <button class="btn btn-primary reloadobjectives pull-right" data-projectid="{{$project->id}}">
      <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
    </button>
  </div>
</div>
@endsection
