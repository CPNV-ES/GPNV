<!--
Created By: Antonio Giordano
Description: Form to add a new checkList
Requirement: Link checkList.js
-->
<div class="well well-sm formNewDelivery hidden" style="max-height: 35px;" >
    <div class="media">
        <div class="media-body" style="position: relative;top: -8px;">
            <form class="form-horizontal" role="form" id="form" action="{{ URL('project') }}/{{$project->id}}/checklist/{{$livrables->getId()}}/create">
                {{ csrf_field() }}

                <input type="text" name="name" id="name" required>
                <input type="hidden" name="description" value="">
                <button type="button" class="newCheckList glyphicon glyphicon-floppy-save" aria-hidden="true"></button>
                <a class="btn cancelNewDelivery pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
            </form>
        </div>
    </div>
</div>

