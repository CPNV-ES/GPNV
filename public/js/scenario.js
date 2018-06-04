/**
* Allow the drag & drop action and preventDefault
* @param ev the element dragged
*/
function allowDrop(ev) {
    ev.preventDefault();
}

/**
* transfer data on drag action
* @param ev the element dragged
*/
function drag(ev) {
    ev.dataTransfer.setData("url", ev.target.src);
    ev.dataTransfer.setData("id", ev.target.id);
}

/**
* change step image on drop
* @param ev element dragged
*/
function drop(ev) {
    ev.preventDefault();
    var url = ev.dataTransfer.getData("url");
    var id = ev.dataTransfer.getData("id");
    ev.target.src = url;
    ev.target.parentElement.href = url;

    var mockup = $(".tableRow.active input[name='mockup']");
    var mockupUrl = $(".tableRow.active input[name='mockupUrl']");
    var stepId = $(".tableRow.active input[name='id']");
    var token = $(".tableRow.active input[name='_token']");

    mockup.val(id);
    mockupUrl.val(url);

    var data = "_method=PUT&_token="+token.val()+"&mockupId="+mockup.val()+"&stepId="+stepId.val();
    $.ajax({
      url : update_image_route,
      type : 'POST',
      data : data
    })
}

/**
* Delete the image from HTML
* @param id Html element id
*/
function remove(id) {
    var elem = document.getElementById(id);
    return elem.parentNode.removeChild(elem);
}
/**
* delete the image
* @param ev Element dragged
*/
function delPicture(ev){
  ev.preventDefault();
  var id = ev.dataTransfer.getData("id");
  remove(id);
  var token = $("#uploadMockup input[name='_token']");

  var data = "_method=DELETE&_token="+token.val()+"&mockupId="+id;
  $.ajax({
    url : del_image_route,
    type : 'POST',
    data : data
  })
}

/**
* Change color on focus and load the linked image to the step clicked
*/
$('.scenario .tableRow').click(function(){
  $('.scenario .tableRow.active').removeClass('active');
  $(this).addClass('active');

  if(typeof this.mockupUrl !== 'undefined' && this.mockupUrl.value != '')
    var mockupUrl = this.mockupUrl.value;
  else{
    var getUrl = window.location;
    var mockupUrl = getUrl .protocol + "//" + getUrl.host +'/mockups/thumbnail-default.jpg';
  }

  $('.maquette img').attr('src', mockupUrl);
});

/**
* Reset Step bakground color
*/
function resetStepColor(element){
  $(element).css('border-width','1px');
  $(element).css('border-color', '#ccc');
}

/**
* Auto save the step on leave if modified
*/
function updateStep(form, element){
  if(form.oldReponse.value != form.reponse.value || form.oldAction.value != form.action.value){
    $.ajax({
      url : $(form).attr('action'),
      type : $(form).attr('method'),
      data : $(form).serialize(),
      success : function(){
        form.oldReponse.value = form.reponse.value;
        form.oldAction.value = form.action.value;
        $(element).css('border-width','2px');
        $(element).css('border-color', 'green');
      }
    })
  }
}


$(document).ready(function() {
    //Click on "Modifier" = Hide modifier, show buttons "Annuler" and "Enregistrer" + open edit field
    $('#modifyDescription').click(function () {
        $(this).addClass("hidden");
        $('#cancelDescription').removeClass("hidden");
        $('#saveDescription').removeClass("hidden");

        $('#projectDescriptionP').addClass("hidden");
        $('#projectDescriptionInput').removeClass("hidden");
        $('#projectNameP').addClass("hidden");
        $('#projectNameInput').removeClass("hidden");
        $('#slideValidated').prop('disabled', false);
        $('#slideTested').prop('disabled', false);

    })

    //Click on "Annuler" = hide buttons and show "Modifier" + cant edit field
    $('#cancelDescription').click(function(){
        $('#cancelDescription').addClass("hidden");
        $('#saveDescription').addClass("hidden");
        $('#modifyDescription').removeClass("hidden");

        $('#projectDescriptionP').removeClass("hidden");
        $('#projectDescriptionInput').addClass("hidden");
        $('#projectNameP').removeClass("hidden");
        $('#projectNameInput').addClass("hidden");
        $('#slideValidated').prop('disabled', true);
        $('#slideTested').prop('disabled', true);
    })


    //Click on "edit" to modify step per step
    $('.modifyStep').click(function(){
        var btn = $(this).attr('id');
        var id = btn.match(/_([^ ]*)/)[1];

        //Show edit field + button
        $('#stepAction1_'+id).addClass("hidden");
        $('#stepResult1_'+id).addClass("hidden");
        $('#stepAction_'+id).removeClass("hidden");
        $('#stepResult_'+id).removeClass("hidden");
        $('#'+btn).addClass("hidden");
        $('#delStep_'+id).removeClass("hidden");
        $('#delCellStep').removeClass("hidden");
        $('#validateStep_'+id).removeClass("hidden");
    })

    $('.addStep').click(function(){
        var btn = $(this).attr('id');
        var id = btn.match(/_([^ ]*)/)[1];
        var newValueAction = $('#stepAction_'+id)["0"].value;
        var newValueResponse =$('#stepResult_'+id)["0"].value;

        console.log(newValueAction);
        console.log(newValueResponse);
        //Return to initial view
        $('#stepAction1_'+id).removeClass("hidden");
        $('#stepResult1_'+id).removeClass("hidden");
        $('#stepAction_'+id).addClass("hidden");
        $('#stepResult_'+id).addClass("hidden");
        $('#modifyStep_'+id).removeClass("hidden");
        $('#delStep_'+id).addClass("hidden");
        $('#delCellStep').addClass("hidden");
        $('#validateStep_'+id).addClass("hidden");
        $('#stepAction1_'+id).text(newValueAction);
        $('#stepResult1_'+id).text(newValueResponse);


        var data0 = {action: newValueAction, response : newValueResponse};
        var request = JSON.stringify(data0 );

        $.ajax({
            url : 'scenario_steps.modify',
            type : 'POST',
            data : {itemId: id, request: request},
            success : function(modify){
            }
        })
    })

})
