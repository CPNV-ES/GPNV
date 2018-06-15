<!--
  Created By: Fabio Marques
  Description: View to show each item of the checkList
-->
<div class="well well-sm checklist-item">
    <div class="media">
        <div class="media-body">
            <div class="col-md-12">
                @if(isset($modalBox))
                    <a class="btn removeObjective pull-right icon-checklist" data-id="{{$checkListItem->id}}"
                       data-projectid="{{$project->id}}">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </a>
                @endif

                @if(isset($modalBox) && $modalBox)
                    <a class="showObjectif" data-id="{{$checkListItem->id}}">
                @else
                    <a/>
                @endif

                <label>{{$checkListItem->title}}</label>


                @if(isset($scenarios))
                    <label class="pull-right">
                    @if($scenarios['allComplete'])
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                    @elseif($scenarios['nbScenarios']!=0)
                        {{$scenarios['nbScenarios']}}/{{$scenarios['nbValidateScenarios']}}
                        /{{$scenarios['nbCompletedScenarios']}}
                    @else
                        ?
                    @endif
                    </label>
                @endif
                    <a class="btn pull-right icon-checklist updateObjective" data-id="{{$checkListItem->id}}">
                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                    </a>
                        <div id="{{$checkListItem->id}}" class="col-md-12 hidden">
                            @include("objective.form")
                        </div>

                </a>
                <input type="hidden" id="validate" name="validate" value="true"/>
            </div>
        </div>
    </div>
</div>

@push('projectCss')
<link rel="stylesheet" href="{{ URL::asset('css/checkList.css') }}"/>
@endpush

