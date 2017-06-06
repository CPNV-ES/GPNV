<script type='text/javascript'>
    $(document).ready(function () {
        $("#editTaskForm").submit(function(event) {
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
                    var bootBoxContainer = $('#editTaskButton').closest('.bootbox-body');
                    bootBoxContainer.html('<p>Tâche modifiée avec succès !</p>');
                }
            });
            /*var fields = $( this ).serializeArray()
            jQuery.each( fields, function( i, field ) {
                console.log(field);
            });*/
        });

        $(".statusContainer .statusList li a").click(function(event) {
          event.preventDefault();
          var currentStatus = $(this).text();

          $('#statusButtonValue').html(currentStatus);
          $("#statusInput").val($(this).attr("data-status-id"));
        });
    });
</script>

<form class="form-horizontal" role="form" method="POST" id="editTaskForm" action="{{ route('tasks.update',$task->id)}}">
    {!! csrf_field() !!}

    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
        <label class="col-md-4 control-label">Nom de la tâche</label>

        <div class="col-md-6">
            <input type="text" class="form-control" name="name" value="{{$task->name}}" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label">Durée de la tâche</label>

        <div class="col-md-6">
            <input type="number" class="form-control" name="duration" min="00:30" max="200" value="{{$task->duration}}" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label">Statut</label>

        <div class="col-md-6 statusContainer">
            <button class="btn btn-default dropdown-toggle" type="button" id="statusButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            <span id="statusButtonValue">{{$task->status->name}}</span>
            <span class="caret"></span>
            </button>
            <ul class="dropdown-menu statusList" aria-labelledby="statusButton" >
                @foreach($statuses as $status)
                    <li><a data-status-id="{{$status->id}}">{{$status->name}}</a></li>
                @endforeach
            </ul>
            <input id="statusInput" type="hidden" class="form-control" name="status"> <!-- holds the status id passed via the form -->
        </div>
    </div>


    <!-- <div class="form-group">
        <label class="col-md-4 control-label">Parent id</label>

        <div class="col-md-6">
            <input type="text" class="form-control" name="parent_id" value="{{ $task->parent_id }}">
        </div>
    </div> -->

    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-primary" id="editTaskButton">
                <i class="fa fa-btn fa-sign-in"></i>Enregistrer
            </button>


        </div>
    </div>
</form>
