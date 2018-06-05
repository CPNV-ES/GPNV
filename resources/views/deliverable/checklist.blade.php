<!--
  Created By: Fabio Marques
  Description: View to show each item of the checkList
-->
<div class="well well-sm checklist-item">
    <div class="media">
        <div class="media-body">
            <div class="col-md-12">
                <a><label>{{$checkListItem->title}}</label></a>
                @if(isset($file))
                    <a class="btn removeDelivery pull-right icon-checklist" data-id="{{$checkListItem->id}}"
                       data-projectid="{{$project->id}}">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </a>
                    @if(isset($fileData->id))
                        <a class="btn removeLink pull-right icon-checklist" data-id="{{$checkListItem->id}}">
                            <span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span>
                        </a>
                        <a class="btn viewFile pull-right icon-checklist" data-fileid="{{$fileData->id}}"
                           data-id="{{$checkListItem->id}}"
                           onclick="window.open('{{asset('files/'.$project->id.'/'.$fileData->url)}}', '_blank');">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                        </a>
                    @elseif($fileData!=null)
                        <a class="btn removeLink pull-right icon-checklist" data-id="{{$checkListItem->id}}">
                            <span class="glyphicon glyphicon-floppy-remove" aria-hidden="true"></span>
                        </a>
                        <a class="btn pull-right icon-checklist" data-id="{{$checkListItem->id}}"
                           onclick="window.open('{{$fileData}}', '_blank');">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                        </a>
                    @else
                        <a class="btn linkDelivery pull-right icon-checklist" data-id="{{$checkListItem->id}}"
                           data-projectid="{{$project->id}}">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </a>
                        <div id="{{$checkListItem->id}}" class="col-md-12 hidden">
                            @include("project.toLink")
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

@push('projectCss')
    <link rel="stylesheet" href="{{ URL::asset('css/checkList.css') }}"/>
@endpush



