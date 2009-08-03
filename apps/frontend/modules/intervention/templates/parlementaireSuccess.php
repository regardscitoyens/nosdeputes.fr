<div class="titre_int_et_seance">
<h1>Les interventions de <?php echo $parlementaire->nom.' '; if ($parlementaire->getPhoto()) { echo image_tag($parlementaire->getPhoto(), ' alt=Photo de '.$parlementaire->nom); } ?></h1>

</div>
<div class="interventions">
  <?php foreach ($parlementaire->getInterventions() as $intervention) : ?>
  <div class="intervention" id="<?php echo $intervention->id; ?>">
    <div class="info">
    <strong>  
    <?php 
    echo $intervention->getSeance()->getDate().' : ';
    
    if ($intervention->getType() == 'commission') { echo $intervention->getSeance()->getOrganisme()->getNom(); }
    else { echo $intervention->getSection()->getTitreComplet(); }
    ?> 
    </strong>
    <span class="source">
      <a href="<?php echo url_for('@interventions_seance?seance='.$intervention->getSeance()->id); ?>#<?php echo $intervention->getId(); ?>">Voir l'intervention dans son contexte</a>
    </span>
    </div>
    <div class="texte_intervention"><?php echo $intervention->getIntervention(); ?></div>
    <div class="commentaires">
      3 commentaires dont celui de toto :
      Cette intervention c'est de la balle !
    </div>
  </div>
<?php endforeach; ?>
</div>