$(document).ready(function() {
  $("#expose_complet").slideUp(0.5);
  $("#expose_court").show();
  $("#expose_court").bind("click", function() {
    $("#expose_court").hide();
    $("#expose_complet").slideDown(500);
  });
});
