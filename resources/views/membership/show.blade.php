@extends('layouts.project')

@section('projectContent')
  <div class="panel panel-default">

    <div class="panel-heading">
      <h1>Membres</h1>
    </div>

    <div id="membership" class="panel-body members membership" data-projectid="{{$project->id}}">
        <?php $Mails="";?>
        <div class="membershipsData">
          @foreach($project->users as $user)
              <?php $Mails.=$user->mail.';';?>
              @include('user.avatar', ['user' => $user, 'inProject' => true, 'projectName' => $project->name])
          @endforeach
        </div>

        <div class="row">
          <div class="col-md-12">
            @if(Auth::user()->projects()->find($project->id))
          <a class="btn btn-primary addStudents" data-projectid="{{$project->id}}" route="{{ route('memberships.getStudents',  $project->id) }}">Ajouter un élève</a>
              <a class="btn btn-primary addTeachers" data-projectid="{{$project->id}}" route="{{ route('memberships.getTeachers',  $project->id) }}">Ajouter un enseignant</a>
              <a class="btn btn-primary" href="mailto:<?=$Mails; ?>?Subject={{$project->name}}">
                Envoyer un mail aux membres
              </a>
              <a class="btn btn-danger quitProject pull-right" data-projectid="{{$project->id}}" data-id="{{Auth::user()->id}}" style="float: right;" route="{{ route('memberships.quitProject',  [$project->id, Auth::user()->id]) }}">Quitter le projet</a>
            @endif
          </div>
        </div>

    </div>
  </div>
</div>
@endsection

@push('projectScripts')
    <script src="{{ URL::asset('js/memberships.js') }}"></script>
@endpush