@extends('layouts.project')

@section('projectContent')
    <!-- Custom journal -->
    <div class="col-xs-12 col-lg-6">
        <div class="panel panel-default">
            <!-- Display all project informations like the members, a description and so on -->
            <div class="panel-heading showPanel" data-toggle="collapse" data-target="#logbookPanel" data-projectid="{{$project->id}}">
                <h1 id="logBookHeading">
                    <?php // $data = json_decode($data); ?>
                    @if ($members->contains($currentUser))
                        @unless ($badgeCount == 0)
                            <span id="logBookBadge" class="badge">{{$badgeCount}}</span>
                        @endunless
                    @endif
                    Journal de bord <span class="glyphicon glyphicon-chevron-down pull-right"></span>
                </h1>
            </div>
            <div class="panel-body collapse logbook" id="logbookPanel" data-projectid="{{$project->id}}">
                <div id="logBook" data-projectid="{{$project->id}}">
                    <form role="form">
                        <div class="awesomeCheckbox awesomeCheckbox-primary logBookCheckbox">
                            @if(Auth::user()->projects()->find($project->id))
                                <input type="checkbox" id="toggleUserEntries" class="styled">
                                <label for="toggleUserEntries">
                                    Avec mes entrées
                                </label>
                            @endif
                        </div>
                    </form>

                    <table id='logTable' class='table'>
                        <thead><tr><th>Qui</th><th>Quand</th><th>Quoi</th><th>Vu</th></tr></thead>
                        @foreach($events as $event)
                            {{-- Checking if the current user is the event creator --}}
                            @if($currentUser['id'] == $event->user_id)
                                <tr class="userMade hidden" data-eventId="{{$event->id}}">
                            @else
                                <tr data-eventId="{{$event->id}}">
                                    @endif
                                    <td>{{$event->users->find($event->user_id)->firstname}} {{$event->users->find($event->user_id)->lastname}}</td>
                                    <td>{{date('d.m.y H:i', strtotime($event->created_at))}}</td>
                                    <td>{{$event->description}}</td>
                                    <td>
                                        {{-- Member event validation handling --}}
                                        @foreach($members as $member)
                                            @unless($member->id == $currentUser['id'])
                                                <span title="{{ $member->firstname }} {{ $member->lastname }}" data-toggle="tooltip" data-placement="bottom">
                                    {{-- Checking if the member has validated this event --}}
                                                    @if(in_array($member->id, $validations[$event->id]))
                                                        <span class="glyphicon glyphicon-stop validEvent" aria-hidden="true" data-userId="{{$member->id}}"></span>
                                                    @else
                                                        <span class="glyphicon glyphicon-stop invalidEvent" aria-hidden="true" data-userId="{{$member->id}}"></span>
                                                    @endif
                                </span>
                                            @endunless
                                        @endforeach

                                        @unless(in_array($currentUser['id'], $validations[$event->id]))
                                            @if(Auth::user()->projects()->find($project->id))
                                                <button type="button" class="btn btn-primary validationButton" style="margin-left: 20px;" data-userId="{{$currentUser['id']}}">Valider</button>
                                            @endif
                                        @endunless
                                    </td>
                                </tr>
                                @endforeach
                    </table>
                </div>
                @if(Auth::user()->projects()->find($project->id))
                    <a href="{{ route('project.events.create',$project) }}" class="btn btn-primary events" data-id="{{$project->id}}">Ajouter un événement</a>
                @endif
            </div>
        </div>
    </div>
@endsection
