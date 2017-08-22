<h1><?php echo $title; ?></h1>
<p>
<ul class="list_orgas_types">
<?php foreach($organisme_types as $t => $ht): ?>
<li><a href="<?php echo url_for('@list_organismes_type?type='.$t); ?>"><br/><?php if ($t != "parlementaire") echo "<br/>"; echo $ht; ?></a></li>
<?php endforeach; ?>
</ul>
</p>
