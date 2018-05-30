<!--
Created By: Antonio Giordano
Description: Form to add a new checkList
Requirement: Link checkList.js
-->
<div class="form-new-deliverable">
    <form class="form-horizontal" role="form" id="form" action="{{ URL('project') }}/{{$project->id}}/checklist/{{$livrables->getId()}}/create">
        <div class="col-md-6">
            {{ csrf_field() }}
            <input type="text" name="name" id="name" class="form-control" required>
            <input type="hidden" name="description" value="">
        </div>
        <div class="col-md-1">
            <button type="button" class="newCheckList btn btn-primary" aria-hidden="true">Ajouter</button>
        </div>
    </form>
</div>



