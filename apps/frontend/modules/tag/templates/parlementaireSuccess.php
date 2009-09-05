<div>
<h1>Les mots les plus prononcés par <?php echo $parlementaire->getNom(); ?></h1>
<a href="<?php echo url_for('@parlementaire_tags?slug='.$parlementaire->slug); ?>">Les 12 derniers mois</a>, <?php 
   foreach ($sessions as $s) {
   echo '<a href="'.url_for('@parlementaire_session_tags?slug='.$parlementaire->slug.'&session='.$s['session']).'"> la session '.preg_replace('/^(\d{4})/', '\\1-', $s['session']).'</a>, ';
 }?><a href="<?php echo url_for('@parlementaire_all_tags?slug='.$parlementaire->slug); ?>">tout son mandat</a>
</div>
   <?php echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Intervention', 'min_tag' => 2, 'route' => '@tag_parlementaire_interventions?parlementaire='.$parlementaire->slug.'&', 'limit'=>1000)); ?>
