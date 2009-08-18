<ul>
<?php foreach($tagSuggestions as $suggestion): ?>
<?php // No extraneous whitespace here, it shows up in text() ?>
<li><?php echo $suggestion['left']?><a href="#"><?php echo $suggestion['suggested']?></a><?php echo $suggestion['right']?></li>
<?php endforeach ?>
</ul>
