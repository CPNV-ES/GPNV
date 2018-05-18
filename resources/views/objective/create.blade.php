<!--
Created By: Antonio Giordano
Description: Form to add a new checkList
Requirement: Link checkList, objective js
-->
<div class="formNewObjective" style="max-height: 35px;" >
    <form class="form-horizontal" role="form" id="form" action="{{ URL('project') }}/{{$project->id}}/checklist/{{$objectifs->getId()}}/create">
        <div class="col-md-6">
            {{ csrf_field() }}
            <input type="text" class="form-control" name="name" id="name" required>
            <input type="hidden" name="description">
        </div>
        <div class="col-md-1">
            <button type="button" class="newCheckList btn btn-primary" aria-hidden="true">Ajouter</button>
        </div>
    </form>
</div>