<noscript>
  <p class="flash_error">Attention : cette interface est prévue pour fonctionner avec Javascript, vous devez donc l'activer pour en profiter.<br/><small>(Les widgets proposées ne contiennent en revanche pas de javascript.)</small></p>
</noscript>
<h1>Inclure NosDéputés.fr sur votre site</h1>
<div id="editor">
<form>
<h2>Saisissez le nom de votre député</h2>
<p style="margin-left:45px; margin-bottom: 20px;">Nom :&nbsp;&nbsp;<input type="text" id="nom" value="<?php if ($depute) echo $depute->getNom();?>" class="update"/>&nbsp;&nbsp;<input type="button" value="rechercher" id="bouton"/></p></center>
<input type="hidden" id="slug" value="<?php if ($depute) echo $depute->getSlug(); ?>"/>
<h2>Choisissez quelques options</h2>
<div style="margin-left: 150px; width: 300px; height: 138px; float: left;">
<p><label><input type="checkbox" id="titre" value="1" class="update" CHECKED>Inclure un titre</label></p>
<p><label><input type="checkbox" id="photo" value="1" class="update" CHECKED>Inclure la photo</label></p>
<p><label><input type="checkbox" id="tag" value="1" class="update">Inclure un nuage de mots-clés</label></p>
<p id="maxtags" style="display:block; margin-left: 19px;"><label>Afficher un maximum de <input size=3 type="textbox" id="nb_maxtags" value="40" class="update"/> tags</label></p>
</div>
<div style="width: 300px; float: left;">
<p><label><input type="checkbox" id="graph" value="1" class="update" CHECKED>Inclure le graphique d'activité</label></p>
<p><label><input type="checkbox" id="indicateurs" value="1" class="update" CHECKED>Inclure les indicateurs d'activité</label></p>
<p style="margin-left: 19px;">Taille du widget : <input size=4 type="text" id="width" class="update" value="600"/> pixels</p>
</div>
<input type="hidden" id="height" value=""/>
<input type="hidden" id="url" value=""/>
</form>
</div>
<div id="preview" style="clear:both; display:none";>
<h2>Prévisualisez le résultat</h2>
<center>
<div id="resultat">
</div>
</center>
</div>
<h2>Embarquez votre député</h2>
<p>Pour insérer ce widget sur votre site, il vous suffit de copier/coller le contenu HTML ci-dessous :</p>
<center><textarea style="font-size: 8px;" cols="175" rows=2 id="iframe" onclick="this.focus();this.select()" readonly="readonly"></textarea></center>
<p style="margin: 20px;"><a href="http://cpc.regardscitoyens.org/trac/wiki/API">Pour un usage avancé, plus de précisions ou un accès direct aux données brutes, nous vous invitons à vous référer à la documentation de notre API.</a></p>
<script>
var updateIframe = function() {
  if (!$('#url').val()) return;
  $('#height').val($('#resultat').height());
  $("#iframe").val('<iframe frameborder="0" scrolling="no" src="'+$('#url').val()+'" height="'+($('#height').val()*1+20)+'" width="'+$('#width').val()+'"></iframe>');
}
var updatePreview = function() {
   var nom = $('#nom').val();
   if (!nom) return;
   if ($(this).attr('id') == "nom") {
     $('#slug').val(null);
   } else {
     var slug = $('#slug').val();  
     if (slug)   nom = slug;
   }
   $("#preview").show();
   var url = 'http://'+window.location.hostname+'/widget/'+nom.replace(/ /, '-')+"?iframe=true&";
   if (!$("#titre:checked").val()) {
     url += "notitre=1&"; 
   }
   if (!$("#tag:checked").val()) {
     url += "notags=1&";
     $("#maxtags").hide();
   } else {
     $("#maxtags").show();
     if ($("#nb_maxtags").val() != 40) {
       url += "maxtags="+$("#nb_maxtags").val()+"&";
     }
   }
   if (!$("#graph:checked").val()) {
     url += "nographe=1&"; 
   }
   if (!$("#photo:checked").val()) {
     url += "nophoto=1&"; 
   }
   if (!$("#indicateurs:checked").val()) {
     url += "noactivite=1&"; 
   }
   url+= "width="+($('#width').val()-20);
   $('#url').val(url);
   updateIframe();
   $('#resultat').load(url+"&internal=1", function(text, status) {
     updateIframe();
   });
};
$('.update').change(updatePreview);
$('#bouton').click(updatePreview);
$("#height").change(updateIframe);
updatePreview();
setInterval(updateIframe, 1000);
</script>
