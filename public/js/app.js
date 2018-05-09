$(document).ready(function () {
    
    // Displays details of a given task
    $('#app-layout').on('click', '.taskContainer', function () {
        // var task = this.getAttribute('data-id');
        var task = $(this).find('.taskshow').first().attr('data-id');
        $.get("{{ route('tasks.index') }}/" + task, {}, function (taskDetails) {
            /*console.log(taskDetails);
            $('#taskdetail').html(taskDetails);*/
            bootbox.dialog({
                title: "Détails de la tâche",
                message: taskDetails,
                backdrop: true,
                onEscape: true
            });
        });
    });

    // Edit a task
    $('#app-layout').on('click', 'button.taskedit', function (event) {
        event.stopPropagation(); // Prevents the event from triggering functions on parents
        var task = this.getAttribute('data-id');
        $.get("{{ route('tasks.edit', '@') }}".replace('@', task), {}, function (task) {
            bootbox.dialog({
                title: "Editer une tâche",
                message: task,
                backdrop: true,
                onEscape: true
            });
        });
    });

    // Add a parent task
    $('#app-layout').on('click', 'button.taskplus', function (event) {
        event.stopPropagation();
        var task = this.getAttribute('data-id');
        $.get("{{ route('tasks.index') }}/" + task + "/children/create", {}, function (task) {
            bootbox.dialog({
                title: "Créer une sous-tâche",
                message: task,
                backdrop: true,
                onEscape: true
            });
        });
    });

    // Add a root task
    $('#app-layout').on('click', '.taskroot', function () {
        var task = this.getAttribute('data-id');
        $.get("{{ route('project.index') }}/" + task + "/tasks/create", {}, function (task) {
            bootbox.dialog({
                title: "Créer une tâche",
                message: task,
                backdrop: true,
                onEscape: true
            });
            //$('#taskdetail').html(task);
        });
    });

    // Return the view to add a user for a task
    $('#app-layout').on('click', 'a.events', function () {
        var projectId = this.getAttribute('data-id');
        bootbox.prompt({
            size: "large",
            backdrop: true,
            title: "Insérez un évènement",
            inputType: 'textarea',
            callback: function(result){
                if (result != null) {
                    if (result != "") {
                        $.ajax({
                            url: "{{ route('project.storeEvents', '@') }}".replace('@', projectId),
                            type: 'post',
                            data: { description: result },
                            success: function() {
                                if (result != null) {
                                    callEvents(projectId);
                                    displayConfirmation(true);
                                }
                            },
                            error: function() {
                                displayConfirmation(false);
                            }
                        });
                    } else {
                        bootbox.alert("La description d'un évènement ne peut pas être vide");
                    }
                }
            }
        });
    });

    // Call a view to add a user for a task
    $('#app-layout').on('click', 'button.taskuser', function (event) {
        event.stopPropagation();
        var task = this.getAttribute('data-id');
        $.ajax({
            url: "{{ route('tasks.users', '@') }}".replace('@', task),
            type: 'get',
            success: function (data) {
                bootbox.dialog({
                    title: "Gestion des utilisateurs de la tâche",
                    message: data,
                    backdrop: true,
                    onEscape: true
                });

            }
        });
    });

    // Delete a task
    $('#app-layout').on('click', 'button.taskdestroy', function (event) {
        event.stopPropagation();
        var task = this.getAttribute('data-id');
        bootbox.confirm("Vous allez supprimer cette tâches ? ", function (result) {
            if (result) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('tasks.index') }}/" + task,
                    data: task,
                    success: function (task) {
                        refreshDisplayedTasks();
                    },
                    error: function (task) {
                        refreshDisplayedTasks();
                    }
                });
            }
        });

    });

    // Delete user of project
    $('button.userprojectdestroy').click(function () {
        var id = this.getAttribute('data-id');
        var projectid = this.getAttribute('data-projectid');
        bootbox.confirm("Voulez vous vraiment retirer l'utilisateur du projet ? ", function (result) {
            //Example.show("Confirm result: "+result);
            if (result) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('project.index') }}/" + projectid + "/users/" + id + "/destroy",
                    success: function (data) {
                        //alert(data);
                        bootbox.alert("Element supprimer avec succès");
                        $('#taskdetail').html(data);
                    }
                });
            }
        });

    });

    // Add a target
    $('#app-layout').on('click', 'a.target', function () {
        var projectid = this.getAttribute('data-projectid');
        $.ajax({
            url: "{{ route('project.gettarget', '@') }}".replace('@', projectid),
            type: 'get',
            success: function (data) {
                bootbox.dialog({
                    title: "Ajouter un objectif",
                    message: data
                });
            }
        });
    });

    // Begin a rush for a task
    $('#app-layout').on('click', 'button.taskplay', function () {
        var usertaskid = this.getAttribute('data-usertaskid');
        $.ajax({
            url: "{{ route('tasks.play', '@') }}".replace('@', usertaskid),
            type: 'post',
            success: function (data) {

                if (data == "") {
                    bootbox.dialog({
                        title: "debug",
                        message: "Tache déja en cours"
                    });
                } else {
                    var button = $('button[data-usertaskid=' + usertaskid + ']');
                    $(button).children().removeClass();
                    button.children().addClass("glyphicon glyphicon-stop");
                    button.removeClass();
                    button.addClass("right btn taskstop btn-lg");
                    button.attr("data-duration", data);
                }
            }
        });
    });

    // Stop a rush for a task
    $('#app-layout').on('click', 'button.taskstop', function () {
        var duration = this.getAttribute('data-duration');
        $.ajax({
            url: "{{ route('tasks.stop', '@') }}".replace('@', duration),
            type: 'post',
            success: function (data) {
                var button = $('button[data-duration=' + duration + ']');
                $(button).children().removeClass();
                button.children().addClass("glyphicon glyphicon-play-circle");
                button.removeClass();
                button.addClass("right btn taskplay btn-lg");

            }
        });
    });

    function callEvents(project) {
        $.ajax({
            url: "{{ route('project.events', '@') }}".replace('@', project),
            type: 'get',
            dataType: 'json',
            success: function (data) {
                // console.log(data);
                var currentUserId = data.currentUser.id;
                var members = data.members;
                var validations = data.validations;
                var badgeCount = data.badgeCount;
                var content = ("<table class='table'><thead><tr><th>Qui</th><th>Quand</th><th>Quoi</th><th>Vu</th></tr></thead>");

                $.each(data.eventInfos, function () {
                    var eventId = this.eventId;

                    // Basic opening row tag
                    var openingRowTag = "<tr data-eventId=\"" + eventId + "\">";

                    // Displaying / hiding the row according to the checkbox status and the currently logged user
                    if (currentUserId == this.userId) {
                        openingRowTag = "<tr class=\"userMade\" data-eventId=\"" + eventId + "\">";
                        if (!$('#toggleUserEntries').prop( "checked" )) {
                            openingRowTag = "<tr class=\"userMade hidden\" data-eventId=\"" + eventId + "\">";
                        }
                    }

                    // Formatting the date to regional format (dd.mm.yy)

                    var fetchedDate = this.created_at;
                    var date = new Date(fetchedDate);

                    var formattedDate = ('0' + date.getDate()).slice(-2) + '.'
                        + ('0' + (date.getMonth()+1)).slice(-2) + '.'
                        + date.getFullYear().toString().slice(-2)
                        + " " + ('0' + date.getHours()).slice(-2)
                        + ":" + ('0' + date.getMinutes()).slice(-2);

                    content += (openingRowTag);
                    content += ("<td>" + this.firstname + " " + this.lastname + "</td>");
                    content += ("<td>" + formattedDate + "</td>");
                    content += ("<td>" + this.description + "</td>");
                    content += ("<td>");

                    // Validation status management
                    $.each(members, function() {
                        if (this.id != currentUserId) {
                            content += ("<span title=\"" + this.firstname + " " + this.lastname
                            + "\" data-toggle=\"tooltip\" data-placement=\"bottom\">");

                            var statusClass; // indicates whether the event has been validated or not

                            // Checking whether the member has validated the event
                            if ($.inArray(this.id, validations[eventId]) > -1) {
                                statusClass = "validEvent";
                            } else {
                                statusClass = "invalidEvent";
                            }

                            content += ("<span class=\"glyphicon glyphicon-stop " + statusClass + "\" aria-hidden=\"true\" data-userId=\"" + this.id + "\"></span>");
                            content += ("</span>");
                        }
                    });

                    // Displaying the validation button if the user hasn't validated an entry
                    if (!($.inArray(currentUserId, validations[eventId]) > -1)) {
                        content += ("<button type=\"button\" class=\"btn btn-primary validationButton\" style=\"margin-left: 20px;\" data-userId=\"" + currentUserId + "\">Valider</button>");
                    }

                    content += ("</td>");
                    content += ("</tr>");
                });

                content += ("</table>");
                // console.log(content);
                $('#logTable').html(content);

                // enabling bootstrap tooltips
                $('[data-toggle="tooltip"]').tooltip();


                $('.validationButton').click(function() {
                    updateValidationStatus(this);
                });

                // updating the badge count and visibility
                if(badgeCount > 0) {
                    if($('#logBookBadge').length == 0) {
                        var badge = "<span id=\"logBookBadge\" class=\"badge\">" + badgeCount + "</span>";
                        $("#logBook h1").append(badge);
                    } else {
                        $('#logBookBadge').html(badgeCount);
                    }
                } else {
                    $('#logBookBadge').remove();
                }

            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    // Delete a user for a task
    $('#app-layout').on('click', 'button.usertaskdestroy', function () {
        var usertaskdestroy = this.getAttribute('data-id');
        var projectId = $('.projectTasks').data('projectid');
        bootbox.prompt({
            size: "medium",
            backdrop: true,
            title: "Insérez un commentaire",
            inputType: 'textarea',
            callback: function(result){
                if (result != null) {
                    if (result != "") {
                        $.ajax({
                            url: "{{ route('tasks.userTaskDelete', '@') }}".replace('@', usertaskdestroy),
                            type: 'delete',
                            data: { projectId: projectId, comment: result},
                            success: function (data) {
                                bootbox.hideAll();
                                bootbox.dialog({
                                    title: "Suppression participant à la tâche",
                                    message: "Participant retiré de la tâche avec succès"
                                });
                                console.log(data);
                            },
                            error: function (data) {
                                console.log(data);
                            }
                        });
                    } else {
                        bootbox.alert("Veuillez insérer un commentaire");
                    }
                }
            }
        });
    });


    // Validate a task
    $('#app-layout').on('click', 'button.validate', function () {
        var taskvalidate = this.getAttribute('data-task');
        bootbox.confirm("Voulez-vous valider cette tâche ? ", function (result) {
            $.ajax({
                url: "{{ route('tasks.status', '@') }}".replace('@', taskvalidate),
                type: 'post',
                success: function (data) {
                    bootbox.dialog({
                        title: "Validation de la tâche",
                        message: data
                    });
                },
                error: function (data) {
                    bootbox.dialog({
                        title: "Validation de la tâche",
                        message: data
                    });
                }
            });
        });
    });

    // Validate a target
    $('#app-layout').on('click', 'button.validetarget', function () {
        var validetarget = this.getAttribute('data-targetid');
        bootbox.confirm("Voulez-vous valider cet objectif ? ", function (result) {
            $.ajax({
                url: "{{ route('project.validetarget', '@') }}".replace('@', validetarget),
                type: 'post',
                success: function (data) {
                    location.reload();
                }
            });
        });
    });

    // Delete a file
    $('#app-layout').on('click', 'button.filedestroy', function () {
        var file = this.getAttribute('data-id');
        var project = this.getAttribute('data-project');
        bootbox.confirm("Voulez-vous supprimer ce fichier ? ", function (result) {
            if(result){
                $.ajax({
                    url: "{{ route('files.destroy', ['@', '#']) }}".replace('@', project).replace('#', file),
                    type: 'delete',
                    success: function (data) {
                        $.ajax({
                            url: "{{ route('files.show', ['@']) }}".replace('@', project),
                            type: "get",
                            success: function (data) {
                                var result = $('<div />').append(data).find('.files').html();
                                $(".files").html(result);
                                bootbox.hideAll();
                            }
                        });
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        });
    });

    // Synchro Splash Screen
    $('a.synchro').click(function () {
        bootbox.dialog({
            title: "Synchro Intranet",
            closeButton: false,
            message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Traitement en cours...</div>'
        });
    });

    // Link file with delivery
    $(document).on("click", 'a.linkDelivery', function(event) {
        //$('a.linkDelivery').click(function () {
        var deliveryID = this.getAttribute('data-id');
        var projectID = this.getAttribute('data-projectid');

        $.get("{{ url('project') }}/" + projectID + "/link/" + deliveryID, function (projectid) {
            bootbox.dialog({
                title: "Choisir le lien",
                message: projectid
            })
        });
    });

    $('a.removeLink').click(function () {
        var checkListID = this.getAttribute('data-id');

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
            callback: function(result){
                if (result) {
                    $.ajax({
                        url: "{{ url('delivery/unlink/') }}/" + checkListID,
                        type: "DELETE",
                        data: { _method: "DELETE"},
                        success: function() {
                            bootbox.alert("Fichier/lien délié avec succés.");
                            location.reload();
                        },
                        error: function() {
                            console.log(result);
                        }
                    });
                }
            }
        });
    });

    // Tooltip handling (enabling bootstrap tooltips)
    $('[data-toggle="tooltip"]').tooltip();

    function updateCheckBoxStatus() {
        if ($('#toggleUserEntries').prop( "checked" )) {
            $('.userMade').removeClass("hidden");
        } else {
            $('.userMade').addClass("hidden");
        }
    }

    $('#toggleUserEntries').change(function() {
        updateCheckBoxStatus()
    });



    function displayConfirmation(success) {
        if (success) {
            bootbox.alert("L'évènement a été ajouté avec succès.");
        } else {
            bootbox.alert("Erreur. L'évènement n'a pas pu être ajouté.");
        }
    }

    function updateValidationStatus(elem) {
        var projectId = $('#logBook').attr('data-projectId');
        var userId = $(elem).attr('data-userId');
        var eventId = $(elem).closest('tr').attr('data-eventId');
        /*console.log(projectId);
         console.log(userId);
         console.log(eventId);*/

        // updating the validation button appearance
        $(elem).removeClass('btn-primary');
        $(elem).addClass('btn-success');
        $(elem).html("Validé!");

        $.ajax({
            url: "{{ route('project.storeEventsValidation', '@') }}".replace('@', projectId),
            type: 'post',
            data: { userId: userId, eventId: eventId },
            success: function(data) {
                // console.log(data);
                // updating the event list
                callEvents(projectId);
            },
            error: function(data) {
                console.log(data);
                console.log("couldn't save the event validation");
            }
        });
    }

    $('.validationButton').click(function() {
        updateValidationStatus(this);
    });

    // Init
    // Coping with the fact some browsers preserves the checkbox status after reloading pages
    updateCheckBoxStatus(); // logbook checkboxes


    /* RELOAD FUNCTION */
    $('button.reloadDeliveries').click(function () {
        var projectID = this.getAttribute('data-projectid');
        $.ajax({
            url: "{{ route('project.showDeliveries', '@') }}".replace('@', projectID),
            type: 'get',
            success: function (data) {
                var result = $('<div />').append(data).find('.deliveriesData').html();
                $(".deliveriesData").html(result)
            }
        });
    });

    $('button.reloadobjectives').click(function () {
        var projectID = this.getAttribute('data-projectid');
        $.ajax({
            url: "{{ route('project.showObjectives', '@') }}".replace('@', projectID),
            type: 'get',
            success: function (data) {
                var result = $('<div />').append(data).find('.objectivesData').html();
                $(".objectivesData").html(result)
            }
        });
    });

    $("#sendFile").submit(function(event) {
        event.preventDefault();
        var form = $( this ), url = form.attr( 'action' );
        var data = new FormData($(this)[0]);

        bootbox.dialog({
            title: "Envoi de fichier",
            closeButton: false,
            message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Envoi en cours...</div>'
        });

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                var result = $('<div />').append(data).find('.files').html();
                $(".files").html(result);
                bootbox.hideAll();
            }
        });
    });

    $("#addTaskType").submit(function(event) {
        event.preventDefault();
        var form = $( this ), url = form.attr( 'action' );

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serializeArray(),
            success: function (data) {
                var result = $('<div />').append(data).find('#data-tasktypes').html();
                $("#data-tasktypes").html(result);
            }
        });
    });

    // Delete objective of project
    $(document).on("click", 'a.removeObjective', function(event) {
        var id = this.getAttribute('data-id');
        var projectid = this.getAttribute('data-projectid');

        bootbox.confirm("Voulez vous vraiment supprimer cet objectif ? ", function (result) {
            if (result) {
                $.ajax({
                    type: "DELETE",
                    url: projectid + "/objective/" + id,
                    success: function (data) {
                        bootbox.alert("Objectif supprimé avec succès");
                        $.ajax({
                            url: "{{ route('project.showObjectives', '@') }}".replace('@', projectid),
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
    });

    // Delete delivery of project
    $(document).on("click", 'a.removeDelivery', function(event) {
        var id = this.getAttribute('data-id');
        var projectid = this.getAttribute('data-projectid');

        bootbox.confirm("Voulez vous vraiment supprimer ce livrable ? ", function (result) {
            if (result) {
                $.ajax({
                    type: "DELETE",
                    url: projectid + "/delivery/" + id,
                    success: function (data) {
                        bootbox.alert("Livrable supprimé avec succès");
                        $.ajax({
                            url: "{{ route('project.showDeliveries', '@') }}".replace('@', projectid),
                            type: 'get',
                            success: function (data) {
                                var result = $('<div />').append(data).find('.deliveriesData').html();
                                $(".deliveriesData").html(result)
                            }
                        });
                    }
                });
            }
        });
    });


});