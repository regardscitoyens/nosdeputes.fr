<h1>Liste des organismes et groupes</h1>
<p>
<ul>
<?php foreach($organisme_types as $t => $ht): ?>
<li><a href="<?php echo url_for('@list_organismes_type?type='.$t); ?>"><?php echo $ht; ?></li>
<?php endforeach; ?>
</ul>
</p>
