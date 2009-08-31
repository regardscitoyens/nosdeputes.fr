<h1>Voulez vous supprimer cet article ?</h1>
<h2><?php echo $article->titre; ?></h2>
<p><?php echo $article->corps; ?></p>
<form method='post'>
<input type='submit' value='Non'/>
<input type='submit' name='confirm' value='Oui'/>
</form>
