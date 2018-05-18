/**
* Show informations about abjectif in a modal
*/

$(document).ready(function () {
    $('.newDelivery').click(function () {
        $('.formNewDelivery').removeClass("hidden")
    });

    $('.cancelNewDelivery').click(function () {
        $('.formNewDelivery').addClass("hidden")
    });

})
