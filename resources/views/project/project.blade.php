@extends('layouts.project')

@section('projectContent')
    <div class="panel panel-default">
        <!-- Display all project informations like the members, a description and so on -->
        <div class="panel-heading">
            <h1>Informations du projet</h1>
        </div>
        <div id="filesInfo" class="panel-body filesInfo" data-projectid="{{$project->id}}">
            <div class="panel panel-default" id="files">
                @if(Auth::user()->projects()->find($project->id))
            </div>
            <div id="projectInfo" class="panel-body projectInfo collapse in" data-projectid="{{$project->id}}">
                <!-- Display the information about project -->
                <p>Nom : {{$project->name}}</p>
                <p>Date de début : {{$project->startDate}}</p>
                <p>
                    Description :<br/>
                <div id="summernote">{!! $project->description !!}</div>
                {{csrf_field()}}
                </p>
                @if(Auth::user()->projects()->find($project->id))
                    <a class="btn btn-primary editDescription">Editer la description</a>
                    <a class="btn btn-success saveDescription" data-projectid="{{$project->id}}" style="display:none;">Sauvegarder description</a>
                @endif
            </div>
                @endif


        </div>
    </div>
@endsection