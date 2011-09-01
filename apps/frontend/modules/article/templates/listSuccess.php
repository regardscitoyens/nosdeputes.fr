<h1><?php echo $titre; ?></h1>
<div class="table_des_matieres">
<ul>
<?php foreach($articles as $a) { ?>
<li><a href="#post_<?php echo $a->id; ?>"><?php echo $a->titre; ?></a></li>
<?php if (count($sousarticles[$a->id])) { ?><ul><?php
foreach($sousarticles[$a->id] as $sa) { ?>
<li><a href="#post_<?php echo $sa->id; ?>"><?php echo $sa->titre; ?></a></li>
<?php } //foreach ?>
</ul><?php } //if ?>
<?php } //foreach ?>
</ul>
</div>
<div>
<?php foreach($articles as $a) { ?>
  <h2><a name="post_<?php echo $a->id; ?>"></a><?php echo $a->titre;
   if ($sf_user->isAuthenticated() && !$sf_user->hasCredential('membre'))
    echo ' <span>('.link_to('Éditer', '@faq_edit?article_id='.$a->id).')</span>'; 
?></h2>
<p><?php echo myTools::escape_blanks($a->corps); ?></p>
<?php if (count($sousarticles[$a->id])) { ?><ul><?php
foreach($sousarticles[$a->id] as $sa) { ?>
<h3><a name="post_<?php echo $sa->id; ?>"></a><?php echo $sa->titre; 
   if ($sf_user->isAuthenticated() && !$sf_user->hasCredential('membre'))
    echo ' <span>('.link_to('Éditer', '@faq_edit?article_id='.$sa->id).')</span>'; 
?></h3>
<p><?php echo myTools::escape_blanks($sa->corps); ?></p>
<?php } //foreach ?>
</ul><?php } //if ?>
<?php } //foreach ?>
</div>
