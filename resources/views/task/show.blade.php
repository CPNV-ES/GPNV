@extends('layouts.project')

@section('projectContent')
    <div class="container">
        <div class="row">
            <div class="projectContent col-sm-9 col-md-10 col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h1>{{$currentTask->name}} </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ URL::asset('js/tasks.js') }}"></script>
        <script src="{{ URL::asset('js/app.js') }}"></script>
    @endpush
@endsection
