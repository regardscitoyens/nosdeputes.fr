<?php if (!count($textes)) { ?>
    <i>Ce député n'est intervenu sur aucun dossier.</i>
<?php return ;} ?>
<ul>
<?php $ct = 0;
foreach($textes as $texte) {
  if (preg_match('/questions?\s/', $texte['Section']['titre'])) continue;
  echo '<li>'.link_to(ucfirst(preg_replace('/\s*\?$/', '', $texte['Section']['titre'])).' (<span class="list_inter">'.$texte['nb'].'&nbsp;intervention'.($texte['nb'] > 1 ? 's' : '').'</span>)', '@parlementaire_texte?slug='.$parlementaire->slug.'&id='.$texte['section_id']).'</li>';
  $ct++;
  if (isset($limit) && $ct == $limit)
    break;
} ?>
</ul>
