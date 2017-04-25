/**
 * Simple JavaScript Examples using AJAX
 *
 * @depends
 *    jQuery 3.x
 *    jQuery UI 1.12 (For Focus Effect, Trimmed down to 100 lines when unminified for effects only)
 */
'use strict';

$(function() {

  // Get Query Strings (If Set)
  var query = getParameterByName('query');
  var page = getParameterByName('page');

  if (query) {
    $("#search-form").find("input[name='query']").val(query);
  }

  // I want a button with an icon to behave as a submit :)
  $("#search-query-btn").click(function(evt) {
    $("#search-form").submit();
  });

  // -- start:SEARCH_FORM_EVENT
  $("#search-form").submit(function(evt) {
    evt.preventDefault();

    // -- start:COLLECT_DATA
    // Get the Parameters to Post
    var url = $(this).attr('action');
    var postData = {
      "query": $(this).find('input[name="query"]').val()
    };
    // -- end:COLLECT_DATA

    // -- start:POST
    // Post the Form to "search.php"
    $.post(url, postData, function(resp) {
      // Note: resp.data is actually hits.hits for convenience (set in search.php)

      // For URI Strings
      var encodedQuery = encodeURIComponent(resp.query);

      // Re-use the same element
      var elem = $("#search-results");

      if (resp.error.length) {
        elem.html(resp.error);
        return;
      }


      /**
       * Build a basic result output.
       * PS: I know, I could use any "framework", but I don't care this is made simple. :)
       */
      var result_set = [];
      var item;

      // Reset any HTML
      elem.html("");
      for(var i = 0; i < resp.data.length; i++) {
        item = resp.data[i]._source;

        /*jshint multistr: true */
        elem.append('\
          <div class="row">\
            <div class="panel panel-default">\
              <div class="panel-heading">'+ item.name +'</div>\
              <div class="panel-body">'+ item.description +'</div>\
            </div>\
          </div>\
        ');
      }

      var query_string = $.param({
        "query": encodedQuery,
        "page": 1
      });

      // Pagination
      var paginate = "\
        <p>Displaying Results " + (resp.extra.from + 1) + " to " + resp.extra.to + " of " + resp.extra.total +"</p>\
        <ul class='pagination'>\
          <li>\
            <a href='?query="+ encodedQuery +"&page="+ (i - 1) +"'><span>&laquo;</span></a>\
          </li>";

        for (i = 0; i < 3; i++) {
          paginate += "\
            <li>\
            <a href='?query="+ encodedQuery +"&page="+ i +"'><span>"+ ( i + 1) +"</span></a>\
            </li>\
          ";
        }

        paginate += "\
          <li>\
            <a href='?query="+ encodedQuery +"?>&page="+ (i + 1) +"'><span>&raquo;</span></a>\
          </li>\
        </ul>";

        console.log(paginate)

      $("#search-pagination").html(paginate);

    }, "json");
    // -- end:POST

  });
  // -- end:SEARCH_FORM_EVENT


  // -- start:SIMPLE_FOCUS_CLICK
  // Example Use: <span class="focus" data-id="search-input">Search Form</a>
  $('.focus').click(function() {
    // Use an ID, otherwise there could be duplicated classes.
    var id = $(this).data('id');

    if ( ! id.length) {
      console.log('.focus item has no data attribute of "data-id"');
      return false;
    }

    // Prepend a # CSS ID to this beauty.
    var cssID = "#" + id;
    if ( $(cssID) ) {

      // Simple animation for attention grabbing
      $(cssID).effect("highlight", {"color": "#d3dde5"}, 3000);
      $(cssID).focus();
      return;
    }

    // Error when the ID is not found
    console.log("%s not found in the DOM tree".format(cssID));

  });
  // -- end:SIMPLE_FOCUS_CLICK

});

// Query String from Stack Overflow
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
