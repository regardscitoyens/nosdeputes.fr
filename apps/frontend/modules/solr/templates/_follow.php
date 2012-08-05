<div class="options">
  <div class="mail">
  <?php if (!isset($zero)) { ?>
  <h3 class="aligncenter">S'abonner aux résultats<br/>de cette recherche</h3>
  <?php } else { ?>
  <h3 class="aligncenter">Être alerté lorsque cette<br/>recherche a un résultat</h3>
  <?php } ?>
<?php $args = '';
  foreach(array_keys($selected) as $k) {
    if (!is_array($selected[$k]) || $k == 'date')
      continue;
    if ($args)
      $args .= '&';
    $args.= "$k=".implode(',', array_keys($selected[$k]));
  } ?>
<table width=100% style="text-align: center"><tr>
       <td><a href="<?php echo url_for('alerte/create?filter='.urlencode($args).'&query='.urlencode($query)); ?>"><?php echo image_tag('xneth/email.png', 'alt="Email"'); ?></a><br/><a href="<?php echo url_for('alerte/create?filter='.urlencode($args).'&query='.urlencode($query)); ?>">par email</a></td>
       <td><?php if (!isset($norss)) : ?><a href="<?php $newargs_rss = $selected; $newargs_rss['format']['rss'] = 'rss'; if (isset($newargs_rss['date'])) unset($newargs_rss['date']); if (isset($newargs_rss['sort'])) unset($newargs_rss['sort']); echo url_for(url_search($query, $newargs_rss)); ?>"><?php echo image_tag('xneth/rss_obliq.png', 'alt="Flux rss"'); ?></a><br/><a href="<?php echo url_for(url_search($query, $newargs_rss)); ?>">par RSS</a><?php endif; ?></td>
</tr></table></div>
<?php if (!isset($opendiv) || !$opendiv) echo '</div>'; ?>

