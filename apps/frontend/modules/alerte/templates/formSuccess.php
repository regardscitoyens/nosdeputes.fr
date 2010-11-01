<?php if ($submit == 'Créer') 
  $titre = "Création d'une alerte email";
else $titre = "Modification d'une alerte email";
$sf_response->setTitle($titre); ?>
<div class="boite_form large_boite_form">
  <div class="b_f_h"><div class="b_f_hg"></div><div class="b_f_hd"></div></div>
    <div class="b_f_cont">
      <div class="b_f_text">
        <form method="POST">
        <table>
          <tr>
            <th colspan="2">
              <h1><?php echo $titre; ?></h1>
            </th>
          </tr>
         <?php if ($form->getObject()->citoyen_id) { ?>
          <tr>
            <th style="text-align:left;">Email</th>
            <td>
              <?php echo $form->getObject()->Citoyen->email; ?>
            </td>
          </tr>
         <?php }
	 if ($form->getObject()->no_human_query) { ?>
           <tr>
            <th>Alerte portant sur</th>
            <td>
              <?php echo $form->getObject()->titre; ?>
            </td>
          </tr>
         <?php } 
         if ($f = $form->getObject()->filter) { ?>
          <tr>
            <th>Filtré sur</th>
            <td>
              <?php if ($submit != 'Créer') echo link_to('Supprimer', 'alerte/delete?verif='.$form->getObject()->verif);?>
              <?php echo preg_replace('/[\&,] ?/', ', ', preg_replace('/[^=\&\,]+=/i', '', strtolower(urldecode($f)))); ?>
            </td>
          </tr>
         <?php }
         echo $form; ?>
          <tr>
            <th></th>
            <td><input type="submit" value="<?php echo $submit; ?>" tabindex="40" style="float:right;" /></td>
          </tr>
        </table>
        </form>
        <br/>
      </div>
    </div>
  <div class="b_f_b"><div class="b_f_bg"></div><div class="b_f_bd"></div></div>
</div>
