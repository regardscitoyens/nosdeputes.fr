<p><?php echo count($amendements) ?> amendements</p>
<ul>
<?php foreach($amendements as $a) :?>
<li><?php echo link_for($a->numero, '@amendement?id='.$a->id); ?></li>
<?php endforeach; ?>
</ul>