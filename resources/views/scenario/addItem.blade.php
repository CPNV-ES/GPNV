<form method="post" action="checkListItem/{{$checkListId}}/scenario/create">
  {{ csrf_field() }}
  {{ method_field('POST') }}
  <div class="form-group">
    <label for="name">Nom</label>
    <input id="name" name="name" type="text" class="form-control" value="">
  </div>
  <button type="submit" class="btn btn-default">Créer</button>
</form>
