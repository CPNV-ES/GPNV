<!--
  Description: Form for update an objective
-->
<form method="post" action="objective/{{$checkListItem->id}}">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="form-group">
        <label for="name">Nom</label>
        <input id="name" name="title" type="text" class="form-control" value="{{$checkListItem->title}}">
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control">{{$checkListItem->description}}</textarea>
    </div>
    <button type="submit" class="btn btn-default">Sauvegarder</button>
</form>