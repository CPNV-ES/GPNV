<script type='text/javascript'>
  $(document).ready(function () {
    $("#form").submit(function(event) {
      event.preventDefault();
      var form = $( this ), url = form.attr( 'action' );
      
      $.ajax({
          url: url,
          type: 'POST',
          data: form.serializeArray(),
          success: function (data) {
            
              var result = $('<div />').append(data).find('.membershipsData').html();
              $(".membershipsData").html(result);
              bootbox.hideAll();
              
          }
      });
    });
  });
</script>

<form class="form-horizontal" role="form" method="POST" id="form" action="{{ route('memberships.add', $project->id) }}">
   {!! csrf_field() !!}
     <div class="checkbox">
        @foreach($users as $user)
          <label class="userlist">
              <input type="checkbox" name="user[{{$user->id}}]">
              @include('user.avatar', ['user' => $user])
          </label>
        @endforeach
     </div>
     <br>
     <div class="form-group">
         <div class="col-md-6 col-md-offset-4">
             <button type="submit" class="btn btn-primary">
                 Ajouter
             </button>
         </div>
     </div>
</form>
