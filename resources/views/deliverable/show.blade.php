<!--*********************** partie PRW2***********************
Created By: Fabio Marques
Description: Show the checkList "Livrables"
-->
<div class="col-xs-12 col-lg-6">
  <div class="panel panel-default">
    <!-- Display all project informations like the members, a description and so on -->
    <div class="panel-heading showPanel" data-toggle="collapse" data-target=".deliverables">
        <h1>Livrables <span class="glyphicon glyphicon-chevron-down pull-right"></span></h1>
    </div>
    <div class="panel-body deliverables collapse" data-projectid="{{$project->id}}">
        <div class="deliveriesData">
          <div class="progressionCheckList">
              <div class="barre" style="background: linear-gradient(90deg, #20DE13 {{$livrables->getCompletedPercent()}}%, #efefef 0%);"></div>
              <p>{{$livrables->getNbItemsDone()}}/{{$livrables->getNbItems()}}</p>
          </div>
          <div>
              <!-- Display all livrables -->
              @if($livrables->showToDo())
                  @foreach($livrables->showToDo() as $checkListItem)
                      @include('checkList.show', array('checkListItem'=>$checkListItem, 'projectId'=>$project->id, 'fileData'=>$livrables->getLink($checkListItem->link), 'file'=>true ))
                  @endforeach
              @endif
          </div>
          <div class="completed hidden">
            <h3>Effectués</h3>
            @if($livrables->showCompleted())
                @foreach($livrables->showCompleted() as $checkListItem)
                    @include('checkList.show', array('checkListItem'=>$checkListItem, 'projectId'=>$project->id, 'fileData'=>$livrables->getLink($checkListItem->link), 'file'=>true ))
                @endforeach
            @endif
          </div>
        </div>


        @if(Auth::user()->projects()->find($project->id))
            @include('deliverable.create')
            <a class="btn btn-primary newDelivery">Ajouter</a>
        @endif
        @if($livrables->getNbItemsDone())
            <a class="btn btn-primary changeView">Voir les éléments effectués</a>
            <a class="btn btn-primary changeView hidden">Cacher les éléments effectués</a>
        @endif
        <button class="btn btn-primary reloadDeliveries pull-right" data-projectid="{{$project->id}}">
          <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
        </button>
    </div>
  </div>
</div>
@push('scripts')
    <script src="{{ URL::asset('deliverables.js') }}"></script>
@endpush
