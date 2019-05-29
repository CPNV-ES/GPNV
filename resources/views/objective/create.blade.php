<!--
Created By: Antonio Giordano
Description: Form to add a new checkList
Requirement: Link objective js
-->
<div class="form-new-objective">
    <form class="form-horizontal" method="post" role="form" id="form-objective" action="{{ URL('project') }}/{{$project->id}}/checklist/{{$objectifs->getId()}}/create">
        <div class="col-md-6">
            {{ csrf_field() }}
            <input type="text" name="name" id="name" class="form-control" required>
            <p class="text-danger msg-field-empty hidden"> Veuillez renseigner ce champ</p>
            <span></span>
            <input type="hidden" name="description" value="">
        </div>
        <div class="col-md-1">
            <button type="button" class="newObjective btn btn-primary" aria-hidden="true">Ajouter</button>
        </div>
    </form>
</div>