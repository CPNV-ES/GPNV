$(document).ready(function () {
  $('.showScenario').click(function () {
      var id = this.getAttribute('data-id');
      var projectId = this.getAttribute('data-projectid');
      var baseUrl = this.getAttribute('data-URL');
      $.get(baseUrl+"/"+projectId+"/scenario/"+id, {}, function (form) {
          bootbox.dialog({
              message: form,
              className: "modalScenario",
              buttons: {
                confirm: {
                    label: 'Valider',
                    className: 'btn-success',
                    callback:''
                },
                save: {
                    label: 'Sauvegarder',
                    className: 'btn-warning',
                    callback:''
                },
                cancel: {
                    label: 'Quitter',
                    className: 'btn-danger'
                }
              }
          });

          $('.scenario table tr').click(function(){
            $('.scenario table .active').toggleClass('active');
            $(this).toggleClass('active');
            $('.maquette img').attr("src",$(this).data('imgurl'));
            $('.maquette a').attr("href", $(this).data('imgurl'));
          });

          $('.scenario .validate').click(function(){
            $(this).parent().parent().removeClass('danger');
            $(this).parent().parent().addClass('success');
            $(this).siblings( "input[name=state]" ).val("true");
          });

          $('.scenario .reject').click(function(){
            $(this).parent().parent().removeClass('success');
            $(this).parent().parent().addClass('danger');
            $(this).siblings( "input[name=state]" ).val("false");
          });
      });
  });
});