@extends('layouts.project')

@section('projectContent')
    <script type='text/javascript'>
        $(document).ready(function () {
            $("#createTaskForm").submit(function(event) {
                event.preventDefault();
                var form = $( this ), url = form.attr( 'action' );

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serializeArray(),
                    success: function (data) {
                        // console.log(data);
                        refreshDisplayedTasks(); // refresh the task list

                        // Display a confirmation message to the user
                        var bootBoxContainer = $('#createTaskButton').closest('.bootbox-body');
                        bootBoxContainer.html('<p>Tâche ajoutée avec succès !</p>');
                    }
                });
                /*var fields = $( this ).serializeArray()
                jQuery.each( fields, function( i, field ) {
                    console.log(field);
                });*/
            });
        });
    </script>

    <form class="form-horizontal" role="form" method="POST" id="createTaskForm" action="{{ route('project.tasks.create') }}">
        {!! csrf_field() !!}
        <?php
        use App\Models\CheckList;
        $objectifs = new CheckList('Project', $project, 'Objectifs', 'project/scenario');
        ?>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label class="col-md-4 control-label">Nom de la tâche</label>

            <div class="col-md-6">
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label">Durée de la tâche (h)</label>

            <div class="col-md-6">
                <input type="number" class="form-control" name="duration" min="1" value="{{ old('duration') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label">Liée à l'objectif</label>

            <div class="col-md-6">
                <select class="form-control" name="root_task" required>
                    @if($objectifs->showToDo())
                        @foreach($objectifs->showToDo() as $checkListItem)
                            <option name="" value="{{ $checkListItem->id }}">{{ $checkListItem->title }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-4 control-label">Statut</label>

            <div class="col-md-6 statusContainer">
                <?php
                use App\Models\Status;
                $statuses = Status::get();
                ?>
                <select class="form-control" name="status" required>
                    @foreach($statuses as $status)
                        <option name="" value="{{$status->id}}">{{$status->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label">Type</label>

            <div class="col-md-6">
                <select class="form-control" name="taskTypes" required>
                    @foreach($taskTypes as $taskType)
                        <option name="" value="{{ $taskType->id }}">{{ $taskType->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6">
                <input type="hidden" class="form-control" name="project_id" value="{{$project}}" required>
            </div>
        </div>


        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit" class="btn btn-primary" id="createTaskButton">
                    <i class="fa fa-btn fa-sign-in"></i>Créer
                </button>

            </div>
        </div>
    </form>
@endsection