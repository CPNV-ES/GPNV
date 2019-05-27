<!--
  Created By: Fabio Marques
  Description: Objectives Blocks Objects
  Modified By : Philippe Baumann
-->

<div class="col-md-12 well well-sm checklist-item showObjectif" data-id="{{$checkListItem->id}}">
    @if(isset($modalBox))
        <a class="btn removeObjective pull-right icon-checklist" data-id="{{$checkListItem->id}}"
           data-projectid="{{$project->id}}">
            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
        </a>
    @endif

    @if(isset($modalBox) && $modalBox)
        <a class="" data-id="{{$checkListItem->id}}">
    @else
        </a>
    @endif

    <!-- Objective title -->
    <label class="objective-title">{{$checkListItem->title}}</label>

    <!-- Objective scenarios stats -->
    @if(isset($scenarios))
        <label class="pull-right">
        @if($scenarios['allComplete'])
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
        @else
                {{$scenarios['nbScenarios']}}/{{$scenarios['nbValidateScenarios']}}/{{$scenarios['nbCompletedScenarios']}}
        @endif
        </label>
    @endif

    <!-- Objective Edit Button -->
    <a class="btn pull-right icon-checklist updateObjective" data-id="{{$checkListItem->id}}">
        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
    </a>

    <!-- Objective Hidden Edit -->
    <div id="{{$checkListItem->id}}" class="col-md-12 hidden">
        @include("objective.form")
    </div>

    </a>
    <input type="hidden" id="validate" name="validate" value="true"/>
</div>

@push('projectCss')
<link rel="stylesheet" href="{{ URL::asset('css/checkList.css') }}"/>
@endpush

