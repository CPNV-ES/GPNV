/*
    Created By: Fabio Marques
    Last modification by: Raphaël B. on 20.05.2017
    Description: Functions to handle checkLists
*/
$(document).ready(function () {
  /**
   * switch view button hidde or not the completed items
   */
  $('.changeView').click(function(){
    var parent = $(this).parent();
    parent.children('.deliveriesData').children('.completed').toggleClass('hidden');
    parent.children('.deliveriesData').children('.changeView').toggleClass('hidden');
  });
});
