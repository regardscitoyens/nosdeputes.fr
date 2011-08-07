Nous avons reçu une demande d'abonnement pour l'alerte email suivante : « <?php echo $alerte->titre; ?> »

Si vous êtes bien à l'origine de cette demande, merci de confirmer en cliquant sur le lien suivant :

<?php echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('alerte/confirmation?verif='.$alerte->getVerif())); ?>

Si ce n'est pas le cas, vous pouvez ignorer cet e-mail et nous vous prions de nous excuser pour la gêne occasionnée.

L'équipe de Regards Citoyens, l'association à l'initiative de NosDeputes.fr
