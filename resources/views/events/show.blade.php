@extends('layouts.project')

@section('projectContent')
    <div class="col-xs-12 col-lg-6">
    <div class="panel panel-default">

        <div class="panel-heading showPanel" data-toggle="collapse" data-target="#membership">
            <h1>Journal de bord <span class="glyphicon glyphicon-chevron-down pull-right"></span></h1>
        </div>

        <form class="form-horizontal" role="form" method="POST" action="{{route('project.storeEvents',$id)}}">
            {!! csrf_field() !!}

            <div class="form-group">
                <label class="col-md-4 control-label">Description de l'événement</label>

                <div class="col-md-6">
                    <input type="text" class="form-control" name="description" value="" required>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                    <button id="storeEventBtn" type="submit" class="btn btn-primary">
                        <i class="fa fa-btn fa-sign-in"></i>Sauvegarder
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
