<li class="taskContainer" >
    @if ($task->status->id == "1") <!-- id #1 = En cours -->
        <a href="{{ "/project/" . $task->project->id . "/tasks/" . $task->id}}" class="bg-info" >
        <span class="taskStatus glyphicon glyphicon-time"></span>
    @elseif ($task->status->id == "2")  <!-- id #2 = A faire -->
        <a href="{{ "/project/" . $task->project->id . "/tasks/" . $task->id}}" class="bg-warning" >
        <span class="taskStatus glyphicon glyphicon-pushpin"></span>
    @else  <!-- id #3 = Terminée -->
        <a href="{{ "/project/" . $task->project->id . "/tasks/" . $task->id}}" class="bg-success">
        <span class="taskStatus glyphicon glyphicon-ok"></span>
    @endif
    <span class="taskshow" data-id="{{$task->id}}">
      <p>{{$task->name}}
        @if($task->isChildrenDifferentTypes())
          (Mixte)
        @elseif(null !== $task->getTaskType())
          ({{$task->getTaskType()->name}})
        @endif
      </p>
    </span>
    @if($task->children->isEmpty())
        <button class="right btn taskuser" data-id="{{$task->id}}"> <span class="glyphicon glyphicon glyphicon-user" aria-hidden="true"></span> </button>
        <button class="right btn taskdestroy"  data-id="{{$task->id}}"> <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> </button>
    @endif
    <button class="right btn taskedit" data-id="{{$task->id}}"> <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> </button>
    <button class="right btn taskplus" data-id="{{$task->id}}"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
  </a>
  <div class="progression" style="background: linear-gradient(90deg, #20DE13 {{(($task->getElapsedDuration()*100/60/60)/$task->duration)}}%, #f6f6f6 0%);">
    <p style="text-align: left;">{{gmdate("H:i:s",$task->getElapsedDuration())}}</p>
    <p> | {{round(($task->getElapsedDuration()*100/60/60)/$task->getDurationTask(),1)}}%</p><!-- Display the task pourcent -->
    <p style="text-align: right;margin-left: auto;">{{$task->getDurationTask()}}h</p>
  </div>

  @unless($task->children->isEmpty())
    <ul>
      @each('project.task', $task->children, 'task')<!-- Display task children -->
    </ul>
  @endunless

</li>
