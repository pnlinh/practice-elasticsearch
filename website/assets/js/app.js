// Using Simple jQuery AJAX
'use strict';

$(function() {

  $("#search-form").submit(function(evt) {
    evt.preventDefault();

    // Get the Parameters to Post
    var url = $(this).attr('action');
    var query = $(this).find('input[name="query"]').val();

    // Post the Form to "search.php"
    $.post(url, query, function(resp) {
      console.log(resp);
    }, 'json');

  });

})
