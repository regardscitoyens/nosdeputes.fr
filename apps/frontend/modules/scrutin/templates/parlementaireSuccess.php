<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => "Votes") )
?>

<?php 
  foreach ($scrutins as $s) {
?>
<li>
	<a href="<?= $s->getScrutin()->getURL() ?>">
		<?= myTools::displayVeryShortDate($s->getScrutin()->date) ?> : <?= $s->getScrutin()->titre ?> (<?= $s->position ?>)
	</a>
</li>
<?php
  }
?>