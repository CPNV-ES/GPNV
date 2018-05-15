$(document).ready(function () {

  // Add student user to project
  $('a.addStudents').click(function () {
      var projectid = this.getAttribute('data-projectid');
      var route = this.getAttribute('route');
      $.get(route, function (projectid) {
          bootbox.dialog({
              title: "Ajouter un élève de la classe",
              message: projectid
          });
      });
  });

  // Add teacher user to project
  $('a.addTeachers').click(function () {
      var projectid = this.getAttribute('data-projectid');
      var route = this.getAttribute('route')
      $.get(route, function (projectid) {
          bootbox.dialog({
              title: "Ajouter un enseignant",
              message: projectid
          });
      });
  });

  $('a.quitProject').click(function () {
      var projectid = this.getAttribute('data-projectid');
      var userid = this.getAttribute('data-id');
      var route = this.getAttribute('route')

      bootbox.confirm({
          title: "Voulez-vous quitter le projet ?",
          message: "Cette action vous retirera du projet, cette action ne peut être annulée.<br/> Vos tâches attribuées resteront mais ne vous seront plus attribuées. (Les autres membres seront informés des changements)",
          buttons: {
              cancel: {
                  label: '<i class="fa fa-times"></i> Retour',
                  className: 'btn-success'
              },
              confirm: {
                  label: '<i class="fa fa-check"></i> Quitter le projet',
                  className: 'btn-danger'
              }
          },
          callback: function(result){
              if (result) {
                  $.ajax({
                      url: route,
                      type: "POST",
                      success: function(data) {
                        window.location.replace(data);
                      },
                      error: function() {
                          console.log(result);
                      }
                  });
              }
          }
      });
  });

  $(document).on("click", 'button.removeUser', function(event) {
      var projectid = this.getAttribute('data-projectid');
      var userid = this.getAttribute('data-id');

      bootbox.confirm({
          title: "Voulez-vous vraiment supprimer cet utilisateur ?",
          message: "Cette action le retirera du projet, cette action ne peut être annulée.<br/> Les tâches attribuées $ l'utilisateur resteront mais ne vous seront plus attribuées. (Les autres membres seront informés des changements)",
          buttons: {
              cancel: {
                  label: '<i class="fa fa-times"></i> Retour',
                  className: 'btn-success'
              },
              confirm: {
                  label: '<i class="fa fa-check"></i> Supprimer du projet',
                  className: 'btn-danger'
              }
          },
          callback: function(result){
              if (result) {
                  $.ajax({
                      url: "{{ route('project.index') }}/" + projectid + "/removeFromProject/" + userid,
                      type: "POST",
                      success: function(data) {
                          var result = $('<div />').append(data).find('.membershipsData').html();
                          $(".membershipsData").html(result);
                          bootbox.alert("Utilisateur supprimé avec succés.")
                      }
                  })
              }
          }
      })
  })
})