<h1>Suppression d'une alerte email</h1>
<p>Confirmez vous la suppression de votre alerte <i><?php echo $alerte->getTitre(); ?></i> ?</p>
<form method="POST">
<input type="hidden" name="verif" value="<?php echo $alerte->verif; ?>"/>
<input type="submit" name="confirmed" value="Oui, je veux la supprimer"/> <input type="submit" value="Non"/>
</form>