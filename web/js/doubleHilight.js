/* survol du txt */
$(".dep_map").live("mouseover", function() {
  dep = $(this).attr("id").substring(3);
  $("#map"+dep).mouseover();
})
$(".dep_map").live("mouseout", function() {
  dep = $(this).attr("id").substring(3);
  $("#map"+dep).mouseout();
})
/* survol de la map */
$("area").live("mouseover", function() {
  dep = $(this).attr("id").substring(3);
  $("#dep"+dep).css("background-color", "#D1EA74");
})
$("area").live("mouseout", function() {
  dep = $(this).attr("id").substring(3);
  $("#dep"+dep).css("background-color", "#fff");
})
