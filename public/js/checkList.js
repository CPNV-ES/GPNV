/*
    Created By: Fabio Marques
    Last modification by: Raphaël B. on 20.05.2017
    Description: Functions to handle checkLists
*/
$(document).ready(function () {
  /**
   * Add a new checkList
   */
  $(".newCheckList").click(function(e) {
      e.preventDefault()
      var div = $(this).parent('div'), form = div.parent('form'), url = form.attr('action'), title = form.children('div').children('#name').val()  //$('#name').val())

      $.ajax({
          url: url,
          type: 'POST',
          data: {name: title},
          success: function (data) {
              var result = $('<div />').append(data).find('.deliveriesData').html();
              $(".deliveriesData").html(result);
              var result = $('<div />').append(data).find('.objectivesData').html();
              $(".objectivesData").html(result);
              bootbox.hideAll();
          }
      });
  });

    $(document).on("click", 'a.removeObjective', function(event) {
      var id = this.getAttribute('data-id');
      var projectid = this.getAttribute('data-projectid');

      bootbox.confirm("Voulez vous vraiment supprimer cet objectif ?", function (result) {
          if (result) {
              $.ajax({
                  type: "DELETE",
                  url: "/project/" + projectid + "/objective/" + id,
                  success: function (data) {
                      bootbox.alert("Objectif supprimé avec succès");
                      $.ajax({
                          url: "",
                          type: 'get',
                          success: function (data) {
                              var result = $('<div />').append(data).find('.objectivesData').html();
                              $(".objectivesData").html(result)
                          }
                      });
                  }
              });
          }
      });
  })

  /**
   *
   * switch view button hidde or not the completed items
   */
  $('.changeView').click(function(){
    var parent = $(this).parent();
    parent.children('.deliveriesData').children('.completed').toggleClass('hidden');
    parent.children('.deliveriesData').children('.changeView').toggleClass('hidden');
  });
});
