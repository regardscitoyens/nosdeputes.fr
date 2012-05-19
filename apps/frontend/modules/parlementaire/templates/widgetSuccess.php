<?php if ($parl) include_component('parlementaire', 'widget', array('slug' => $parl, 'options' => $options));
else echo '<span>Aucun député trouvé pour « '.$search.' ».</span>'; ?>
<?php if ($internal) : ?>
<script>if ($('#slug').val() != "<?php echo $parl; ?>") {$('#slug').val("<?php echo $parl; ?>"); updatePreview();} </script>
<?php endif; ?>
