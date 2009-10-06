<h1>Les députés : recherche par circonscriptions</h1>
<?php $sf_response->setTitle('Les députés : recherche par circonscriptions'); ?>
<p><?php $cpt = $query_parlementaires->count(); echo $cpt ; ?> député<?php if ($cpt > 1) echo 's' ; ?> trouvé<?php if ($cpt > 1) echo 's' ; ?> pour "<i><?php echo $search; ?></i>":</p>
<ul>
<?php foreach($query_parlementaires->execute() as $parlementaire) : ?>
<li><a href="<?php echo url_for('circonscription/show?departement='.$parlementaire->nom_circo); ?>"><?php echo $parlementaire->getNomNumCirco() ; ?></a>, <?php echo $parlementaire->getNumCircoString(1); ?>
&nbsp;:
<?php
echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); 
?>
&nbsp;(<?php echo $parlementaire->getStatut(1); ?>)</li>
<?php endforeach ; ?>
</ul>
