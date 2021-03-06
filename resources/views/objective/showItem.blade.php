<!--
  Description: Popup to show scenarios for an objective and include scenario.addItem form to create a new one
-->
<div class="objectif">
    @if(isset($scenarios))
        <form method="post" action="{{$projectId}}/scenario">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <div class="form-group">
                <label>Scénarios</label>
                @if(count($scenarios)>0)
                    <div class="container">
                        @foreach($scenarios as $scenario)
                            <div class="row">
                                <a class="col-xs-9 col-md-7 col-lg-12 col checklist-item well well-sm" href="{{route('scenario.show', array('projectId'=>$projectId, 'stepId'=>$scenario->id))}}">
                                    <label class="objective-title">{{$scenario->name}}</label>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>Aucun scénario disponible</p>
                @endif

            </div>
        </form>
    @endif
        @include("scenario.addItem")
</div>