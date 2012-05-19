<h1>Inclure NosDéputés.fr sur votre site</h1>
<div id="editor">
<form>
<h2>Saisissez le nom de votre député</h2>
<p>Nom : <input type="text" id="nom" value="<?php if ($depute) echo $depute->getNom();?>" class="update"><input type="button" value="rechercher" id="bouton"/></p>
<input type="hidden" id="slug" value="<?php if ($depute) echo $depute->getSlug(); ?>"/>
<h2>Choisissez quelques options</h2>
<div style="margin-left: 150px; width: 300px; height: 138px; float: left;">
<p><label><input type="checkbox" id="titre" value="1" class="update" CHECKED>Inclure le titre</label></p>
<p><label><input type="checkbox" id="photo" value="1" class="update" CHECKED>Inclure la photo du député-e</label></p>
<p><label><input type="checkbox" id="tag" value="1" class="update">Inclure le nuage de tags</label></p>
<p id="maxtags" style="display:block;"><label>Afficher un maximum de <input size=3 type="textbox" id="nb_maxtags" value="40" class="update"/> mots-clés.</label></p>
</div>
<div style="width: 300px; float: left;">
<p><label><input type="checkbox" id="graph" value="1" class="update" CHECKED>Inclure le graphique d'activité</label></p>
<p><label><input type="checkbox" id="indicateurs" value="1" class="update" CHECKED>Inclure les indicateurs d'activité</label></p>
<p>Taille du widget : <input size=4 type="text" id="width" class="update" value="600"/> pixels.</p>
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
