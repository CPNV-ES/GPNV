$(document).ready(function() {

  /**
  * setCookies
  * @param cname Cookie name
  * @param cvalue Cookie value
  * @param path base path to the cookie
  */
  setCookie = function(cname, cvalue, path=null){
    if(!path) path="/";
    var d = new Date();
    d.setTime(d.getTime() + (1*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path="+path;
  }

  /**
  * get cookies
  * @return array with all cookies
  */
  getCookies = function() {
    var c = document.cookie, v = 0, cookies = {};
    if (document.cookie.match(/^\s*\$Version=(?:"1"|1);\s*(.*)/)) {
        c = RegExp.$1;
        v = 1;
    }
    if (v === 0) {
        c.split(/[,;]/).map(function(cookie) {
            var parts = cookie.split(/=/, 2),
                name = decodeURIComponent(parts[0].trimLeft()),
                value = parts.length > 1 ? decodeURIComponent(parts[1].trimRight()) : null;
            cookies[name] = value;
        });
    } else {
        c.match(/(?:^|\s+)([!#$%&'*+\-.0-9A-Z^`a-z|~]+)=([!#$%&'*+\-.0-9A-Z^`a-z|~]*|"(?:[\x20-\x7E\x80\xFF]|\\[\x00-\x7F])*")(?=\s*[,;]|$)/g).map(function($0, $1) {
            var name = $0,
                value = $1.charAt(0) === '"'
                          ? $1.substr(1, -1).replace(/\\(.)/g, "$1")
                          : $1;
            cookies[name] = value;
        });
    }
    return cookies;
  }

  /**
  * Get cookie by name
  * @param name Cookie name
  * @return value of the asked cookie
  */
  getCookie = function(name) {
      return getCookies()[name];
  }

  /**
  * Prepare text editor to edit the description
  */
  $('a.editDescription').click(function () {
    if($('a.saveDescription').is(':hidden')){
      $('#summernote').summernote();
      $('#summernote').css('display','None');
      $('div.note-editor').css('display','block');
      $('a.saveDescription').css('display','initial');
      $('a.editDescription').text("Quitter l'édition");
    }
    else{
      $('#summernote').css('display','block');
      $('div.note-editor').css('display','None');
      $('a.saveDescription').css('display','None');
      $('a.editDescription').text("Editer la description");
    }
  });

  /**
  * Save the description
  */
  $('a.saveDescription').click(function () {
    var description = $('#summernote').summernote('code');
    var projectid = this.getAttribute('data-projectid');
    var token = $("input[name=_token]");
    var data = "_method=POST&_token="+token.val()+"&description="+description;
    $.ajax({
        url: projectid + "/editDescription",
        type: "POST",
        data: data,
        success: function (data) {
            bootbox.alert("Description modifiée avec succés.");
            var result = $('<div />').append(data).find('#summernote').html();
            $("#summernote").html(result);
            $('#summernote').css('display','block');
            $('div.note-editor').css('display','None');
            $('a.saveDescription').css('display','None');
        }
    });
  });

  /**
  * Create a new project
  */
  $('button.createProject').click(function () {
      var description = $('#createDescription').summernote('code');
      $('#createDescription').val(description);
  });

  /**
  * initialize dataPicker
  */
  $(function() {
    $("#datepicker").datepicker();
    $('#createDescription').summernote();
  });

  /**
  * initialize collapsible content
  */
  $(function() {
    var theCookies = Object.keys(getCookies());
    for (var i = 0; i < theCookies.length; i++) {
      if (theCookies[i].startsWith('#')){
        var cookie = getCookie(theCookies[i]);
        if(cookie == 'true'){
          $(theCookies[i]).toggleClass("in");
          if(theCookies[i].startsWith('#users'))
            $(theCookies[i]).parent().children('h4').children('span').toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
          else
            $(theCookies[i]).parent().children('.showPanel').children('h1').children('span').not($("#logBookBadge")).toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
        }
      }
    }
  });

  /**
  * Update collapsible content cookies to project view
  */
  $('.showPanel').click(function(){
    $(this).children('h1').children('span').not($("#logBookBadge")).toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
    var cookieName = $(this).attr('data-target');
    if(cookieName!=null){
      var cookieValue = getCookie(cookieName);
      if(cookieValue && cookieValue == 'true')
        setCookie(cookieName, 'false', document.location.pathname);
      else
        setCookie(cookieName, 'true', document.location.pathname);
    }
  });

  /**
  * Update collapsible content cookies to projects view
  */
  $('.showMembres').click(function(){
    $(this).children('span').toggleClass("glyphicon-chevron-down glyphicon-chevron-up");
    var cookieName = $(this).attr('data-target');
    if(cookieName!=null){
      var cookieValue = getCookie(cookieName);
      if(cookieValue && cookieValue == 'true')
        setCookie(cookieName, 'false');
      else
        setCookie(cookieName, 'true');
    }
  });

  /**
  * View File
  */
  $('button.viewFile').click(function () {
      var fileID = this.getAttribute('data-fileid');
      var deliveryID = this.getAttribute('data-id');
  });

  /**
  * Displays / hides tasks according to the active filters
  */
  refreshDisplayedTasks = function() {
    var projectId = $('.projectTasks').attr('data-projectid');
    var status = [];
    $(".checkboxFilter").each(function(checkbox) {
      if (this.checked) {
        status.push($(this).attr('data-status-id'));
      }
    });

    var taskOwner = $(".dropTaskFilter .owner li a.activeOwner").attr("data-taskOwner");
    var taskObjective = $(".dropTaskFilter .objective li a.activeObjective").attr("data-objective");

    $.ajax({
      url: projectId + "/getTasks",
      type: 'get',
      data: {status: status, taskOwner: taskOwner, taskObjective: taskObjective},
      success: function (tasks) {
        $("#tree-menu ul").html(tasks);
      },
      error: function() {
      }
    });
  }

  /**
  * Checkbox filters management
  */
  $(function() {
    var theCookies = Object.keys(getCookies());
    for (var i = 0; i < theCookies.length; i++) {
      if (theCookies[i].startsWith('#check')){
        var cookie = getCookie(theCookies[i]);
        var checkStatus = (cookie == 'true');

        $(theCookies[i]).prop('checked', checkStatus);
      }
    }
  });

  /**
  * Dropdown filters management
  */
  $(function() {
    var theCookies = Object.keys(getCookies());
    var cookieValues = Object.values(getCookies());

    var expr = "Dropdown";

    for (var i = 0; i < theCookies.length; i++) {
      var componentNameIndex = theCookies[i].search(expr);
      if (componentNameIndex != -1){
        // ex: extracts 'people' from #peopleDropdown
        var componentName = theCookies[i].substr(1, componentNameIndex - 1);
        // Gets the index of the containing <li> within the <ul>
        var listItemIndex = cookieValues[i];

        switch (componentName) {
          case 'people':
            $(".dropTaskFilter .owner li a").removeClass("activeOwner");
            var listItem = $(".dropTaskFilter .owner li").get(listItemIndex);
            var targetLink = $(listItem).find('a').first();
            targetLink.addClass("activeOwner");
            // Updating dropdown button text
            $("#dropdownTitleOwner").html(targetLink.text())
            break;
          case 'objectives':
            $(".dropTaskFilter .objective li a").removeClass("activeObjective");
            var listItem = $(".dropTaskFilter .objective li").get(listItemIndex);
            var targetLink = $(listItem).find('a').first();
            targetLink.addClass("activeObjective");
            $("#dropdownTitleObjective").html(targetLink.text());
            break;

          default:
        }
      }
    }
    window.refreshDisplayedTasks();
  });

  var inputs = document.querySelectorAll( '.inputfile' );
  Array.prototype.forEach.call( inputs, function( input )
  {
      var label	 = input.nextElementSibling,
          labelVal = label.innerHTML;

      input.addEventListener( 'change', function( e )
      {
          var fileName = '';
          if( this.files && this.files.length > 1 )
              fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
          else
              fileName = e.target.value.split( '\\' ).pop();

          if( fileName )
              label.querySelector( 'span' ).innerHTML = fileName;
          else
              label.innerHTML = labelVal;
      });
  });

});
