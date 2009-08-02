<h1>Les interventions de <?php echo $parlementaire->nom; ?></h1>
<?php foreach ($parlementaire->getInterventions() as $intervention) : ?>
<div id="<?php echo $intervention->id; ?>">
   <div class="info">
<b>
<?php echo $intervention->getSeance()->getDate(); ?> :
<?php if ($intervention->getType() == 'commission') 
    echo $intervention->getSeance()->getOrganisme()->getNom(); 
else
  echo $intervention->getSection()->getTitreComplet();
?> 
</b></div>
<div class="intervention"><?php echo $intervention->getIntervention(); ?></div>
<div class="source">
<a href="<?php echo url_for('@interventions_seance?seance='.$intervention->getSeance()->id); ?>#<?php echo $intervention->getId(); ?>">Voir l'intervention dans son contexte</a>
</div>
<div class="commentaires">
  3 commentaires dont celui de toto :
  Cette intervention c'est de la balle !
</div>
</div>
<?php endforeach; ?>