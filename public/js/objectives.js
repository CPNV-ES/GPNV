/**
 * Show informations about abjectif in a modal
 */
$(document).ready(function () {
    var objectiveID;

    $(".newObjective").click(function (e) {
        e.preventDefault()

        if ($('#name').val() != "") {
            $('.msg-field-empty').addClass('hidden')
            $.ajax({
                url: $(this).parent('div').parent('form').attr('action'),
                type: 'POST',
                data: {name: $('#name').val()},
                success: function (data) {
                    var result = $('<div />').append(data).find('.objectivesData').html();
                    $(".objectivesData").html(result);
                    bootbox.hideAll();
                }
            });
        }
        else {
            $('.msg-field-empty').removeClass('hidden')
        }
    });

    $(document).on("click", 'a.removeObjective', function (event) {
        var id = this.getAttribute('data-id');
        var projectid = this.getAttribute('data-projectid');

        bootbox.confirm("Voulez vous vraiment supprimer cet objectif ?", function (result) {
            if (result) {
                $.ajax({
                    type: "DELETE",
                    url: "objective/" + id,
                    success: function (data) {
                        bootbox.alert("Objectif supprimé avec succès");
                        $.ajax({
                            url: "",
                            type: 'get',
                            success: function (data) {
                                var result = $('<div />').append(data).find('.objectivesData').html();
                                $(".objectivesData").html(result)
                                bootbox.hideAll()
                            }
                        });
                    }
                });
            }
        });
    })

    //Show dialog to create a new scenario for objective and show actuals
    $(document).on("click", '.showObjectif', function (event) {
        objectiveID = this.getAttribute('data-id');
        $.get("objective/" + objectiveID, function (form) {
            bootbox.dialog({
                message: form
            });
        });
    });


    $('.reloadobjectives').click(function () {
        $.ajax({
            url: null,
            type: 'get',
            success: function (data) {
                var result = $('<div />').append(data).find('.objectivesData').html();
                $(".objectivesData").html(result)
            }
        });
    });

    $(document).ajaxComplete(function () {
        // Show the form to link a file or url to a deliverable
        $('.updateObjective').click(function () {
            //$('a.linkDelivery').click(function () {
            $(this).closest('.checklist-item').css('max-height', '350px')
            $('#' + objectiveID).addClass("hidden")
            objectiveID = this.getAttribute('data-id');
            $('#' + objectiveID).removeClass("hidden")
        });
    })
})
