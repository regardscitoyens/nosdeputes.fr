<h1><?php echo $article->titre; ?></h1>
<p>par <?php include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$article->citoyen_id)); ?></p>
<p><?php echo myTools::escape_blanks($article->corps); ?></p>
<p>Envoyé le <?php echo myTools::displayDate($article->created_at); ?></p>
