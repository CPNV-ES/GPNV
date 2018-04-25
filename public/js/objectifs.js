/**
* Show informations about abjectif in a modal
*/
$(document).on("click", '.showObjectif', function(event) {
    var id = this.getAttribute('data-id');
    var projectId = this.getAttribute('data-projectid');
    var baseUrl = this.getAttribute('data-URL');
    $.get(baseUrl+"/"+projectId+"/checkListItem/"+id, {}, function (form) {
        bootbox.dialog({
            message: form
        });

        /**
        * Show modal to create a new scenarion on the objectif
        */
        $('.addScenario').click(function () {
          var id = this.getAttribute('data-id');
          var projectId = this.getAttribute('data-projectid');
          var baseUrl = this.getAttribute('data-URL');
          $.get(baseUrl+"/"+projectId+"/checkListItem/"+id+"/scenario/create", {}, function (form) {
              bootbox.dialog({
                  message: form,
                    cancel: {
                        label: 'Quitter',
                        className: 'btn-danger'
                    }
              });
          });
        });
    });
  });

$(document).ready(function () {
    $('.newObjective').click(function () {
        $('.formNewObjective').removeClass("hidden")
    });

    $('.cancelNewObjective').click(function () {
        $('.formNewObjective').addClass("hidden")
    });
})
