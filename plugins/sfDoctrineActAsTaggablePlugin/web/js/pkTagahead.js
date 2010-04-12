// You need to bring in jQuery first in order for this to work
//
// Call it like this:
// pkTagahead(<?php echo json_encode(url_for("taggableComplete/complete")) ?>);
//
// Or similar. Now all of your input elements with the input-tag class
// automatically gain the typeahead suggestion feature.
//
// If you're not using Symfony and sfDoctrineActAsTaggablePlugin, 
// pass your own URL that returns a <ul> containing <li>s with the
// FULL TEXT of what the ENTIRE tag string will be if the user takes
// that suggestion, with the new tag you're suggesting an <a> link
// to #. Then use CSS to hide (visibility: none) the part of the 
// <li> that is not in the <a>. Don't introduce any extra whitespace.

function pkTagahead(tagaheadUrl)
{
  $(function() {
    function getKey(event)
    {
      // Portable keycodes sigh
      return event.keyCode ? event.keyCode : event.which;
    }
    function setClick(target)
    {
      $(target).find('a').click(function(event)
      {
        // span contains ul contains li contains a
        var span = this.parentNode.parentNode.parentNode;
        var input = $(span).data("tag-peer");
        // Get text from the li
        var parent = this.parentNode;
        $(input).val($(parent).text());
        $(input).focus();
        return false;
      });
    }
    // Add suggestions span (you'll need to style that)
    $('input.tag-input').after("<div class='tag-suggestions'></div>");
    // Each tag field remembers its suggestions span...
    $('input.tag-input').each(function() 
    {
      $(this).data("tag-peer", $(this).next()[0]);
    });
    // And vice versa
    $('div.tag-suggestions').each(function() 
    {
      $(this).data("tag-peer", $(this).prev()[0]);
    });
    // Now we can really throw down
    $('input.tag-input').keyup(function(event) 
    {
      var key = getKey(event);
      // Tab key 
      if (key == 9)
      {
        var peer = $(this).data("tag-peer");
        var suggestions = $(peer).find("li"); 
        if (suggestions.length)
        {
          $(this).val($(suggestions[0]).text());
          $(this).focus();
        }
        // In any case don't insert the tab
        return false;
      }
      else
      {
        // Trigger ajax update of suggestions
      } 
    });
    $('input.tag-input').keypress(function(event) 
    {
      // Firefox 2.0 mac is stubborn and only allows cancel here
      // (we will still get the keyup and do the real work there)
      var key = getKey(event);
      if (key == 9)
      {
        // Don't insert tabs, ever
        return false;
      }
    });
    var lastValues = {};
    setInterval(function() 
    {
      // AJAX query for suggestions only when changes have taken place
      $('input.tag-input').each(function() 
      {
        var last = $(this).data('tag-last');  
        var value = $(this).val();
        var peer = $(this).data('tag-peer');
        if (last !== value)
        {
          $(this).data('tag-last', value);
          $.post(
            tagaheadUrl, 
            { 
              current: $(this).val() 
            },
            function(data, textStatus) 
            {
              $(peer).html(data);       
              setClick(peer);
            }
          );
        }
      });
    }, 200);
  });
}

