@extends('layouts.app')

@section('content')

    <div class="projectContainer">

        <div class="row">
            <div class="sidebar col-sm-3 col-md-2">
                <div class="mini-submenu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>
                <div class="list-group">
                    <a href="#" class="list-group-item {{ (\Request::route()->getName() == 'project.show') ? 'active' : '' }}">
                        <h3><i class="fa fa-home"></i><br>Acceuil</h3>
                    </a>
                    <a href="{{route('objective.show', ['id' => $project->id])}}" class="list-group-item {{ (\Request::route()->getName() == 'objective.show') ? 'active' : '' }}">
                        <h3><i class="fa fa-crosshairs"></i><br>Objectifs</h3>
                    </a>
                    <a href="#" class="list-group-item {{ (\Request::route()->getName() == '') ? 'active' : '' }}">
                        <h3><i class="fa fa-list-ul"></i><br>Tâches</h3>
                    </a>
                    <a href="#" class="list-group-item {{ (\Request::route()->getName() == '') ? 'active' : '' }}">
                        <h3><i class="fa fa-book"></i><br>Journal de bord</h3>
                    </a>
                    <a href="{{route('files.show', ['id' => $project->id])}}" class="list-group-item {{ (\Request::route()->getName() == 'files.show') ? 'active' : '' }}">
                        <h3><i class="fa fa-copy"></i><br>Fichiers</h3>
                    </a>
                    <a href="{{route('deliverable.show', ['id' => $project->id])}}" class="list-group-item {{ (\Request::route()->getName() == 'deliverable.show') ? 'active' : '' }}">
                        <h3><i class="fa fa-list-ol"></i><br>Livrables</h3>
                    </a>
                    <a href="{{route('memberships.show', ['id' => $project->id])}}" class="list-group-item {{ (\Request::route()->getName() == 'memberships.show') ? 'active' : '' }}">
                        <h3><i class="fa fa-users"></i><br>Membres</h3>
                    </a>
                </div>
            </div>
            <div class="projectContent col-sm-9 col-md-10">

                @yield('projectContent')

            </div>
        </div>
    </div>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('css/project.css') }}"/>
    @yield('projectCss')
@endsection

@push('scripts')
    @stack('projectScripts')
@endpush
