$(document).ready(function () {
    var deliveryID;

    $('.newDelivery').click(function () {
        $('.formNewDelivery').removeClass("hidden")
    });

    $('.cancelNewDelivery').click(function () {
        $('.formNewDelivery').addClass("hidden")
    });

    // Create a new deliverable
    $(".newDeliverable").click(function (e) {
        e.preventDefault()
        var div = $(this).parent('div'), form = div.parent('form'), url = form.attr('action'),
            title = form.children('div').children('#name').val()  //$('#name').val())

        $.ajax({
            url: url,
            type: 'POST',
            data: {name: title},
            success: function (data) {
                var result = $('<div />').append(data).find('.deliveriesData').html();
                $(".deliveriesData").html(result);
                bootbox.hideAll();
            }
        });
    });

    // Show the form to link a file or url to a deliverable
    $(document).on("click", 'a.linkDelivery', function (event) {
        //$('a.linkDelivery').click(function () {
        $(this).closest('.checklist-item').css('max-height', '350px')
        $('#' + deliveryID).addClass("hidden")
        deliveryID = this.getAttribute('data-id');
        $('#' + deliveryID).removeClass("hidden")
    });

    // Pop-up for delete deliverable of project
    $(document).on("click", 'a.removeDelivery', function (event) {
        var id = this.getAttribute('data-id');
        var projectid = this.getAttribute('data-projectid');

        bootbox.confirm("Voulez vous vraiment supprimer ce livrable ? ", function (result) {
            if (result) {
                $.ajax({
                    type: "DELETE",
                    url: "deliverable/" + id,
                    success: function (data) {
                        bootbox.alert("Livrable supprimé avec succès");
                        $.ajax({
                            url: "deliverables",
                            type: 'GET',
                            data: {projectID: projectid},
                            success: function (data) {
                                var result = $('<div />').append(data).find('.deliveriesData').html();
                                $(".deliveriesData").html(result);
                                bootbox.hideAll();
                            }
                        });
                    }
                });
            }
        });
    });

    // Need this for toggle after ajax get (for bootstrap generation)
    $(document).ajaxComplete(function () {
        $('input[type=checkbox][data-toggle^=toggle]').bootstrapToggle()
        $('.toggleDeliverable').change(function () {
            if ($(this).prop("checked") === true) {
                $('.fileform' + deliveryID).addClass('hidden')
                $('.urlform' + deliveryID).removeClass('hidden')
                $('.to-link' + deliveryID).text("Lier le lien")
                $('.url-input' + deliveryID).prop('required', true);
                $('.file-input' + deliveryID).removeAttr('required')
            } else {
                $('.fileform' + deliveryID).removeClass('hidden')
                $('.urlform' + deliveryID).addClass('hidden')
                $('.to-link' + deliveryID).text("Lier le fichier")
                $('.file-input' + deliveryID).prop('required', true);
                $('.url-input' + deliveryID).removeAttr('required')
            }
        })

        // Pop-up for delete confirmation to remove a linked file/url
        $('a.removeLink').click(function () {
            var checkListID = this.getAttribute('data-id');
            var projectid = this.getAttribute('data-projectid');

            bootbox.confirm({
                title: "Voulez-vous delier ce fichier/lien ?",
                message: "Cette action deliera le fichier/lien de ce livrable et le rendra disponible pour d'autres livrables",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Retour',
                        className: 'btn-success'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Délier le fichier/lien',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $.ajax({
                            url: "/deliverable/unlink/" + checkListID,
                            type: "DELETE",
                            data: {_method: "DELETE"},
                            success: function () {
                                bootbox.alert("Fichier/lien délié avec succés.");
                                $.ajax({
                                    url: "deliverables",
                                    type: 'GET',
                                    data: {projectID: projectid},
                                    success: function (data) {
                                        var result = $('<div />').append(data).find('.deliveriesData').html();
                                        $(".deliveriesData").html(result);
                                        bootbox.hideAll();
                                    }
                                });
                            },
                            error: function () {
                                console.log(result);
                            }
                        });
                    }
                }
            });
        });
    });
})
