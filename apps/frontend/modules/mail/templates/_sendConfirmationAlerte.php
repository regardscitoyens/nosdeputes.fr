Nous avons recu une demande d'abonnement pour l'alerte email suivante : « <?php echo $alerte->titre; ?> »

Si vous êtes bien à l'origine de cette demande, merci de la confirmer en cliquant sur le lien suivant :

<?php echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('alerte/confirmation?verif='.$alerte->getVerif())); ?>

Si ce n'est pas le cas, merci d'ignorer ce mail et de nous excuser de vous avoir importuné.

L'équipe de Regards Citoyens, l'association en charge de NosDeputes.fr