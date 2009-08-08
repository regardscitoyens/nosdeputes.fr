<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('utilisateur/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?slug='.$form->getObject()->getSlug() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('utilisateur/index') ?>">Annuler</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'utilisateur/delete?slug='.$form->getObject()->getSlug(), array('method' => 'delete', 'confirm' => 'Etes vous sur?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['login']->renderLabel() ?></th>
        <td>
          <?php echo $form['login']->renderError() ?>
          <?php echo $form['login'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['pass']->renderLabel() ?></th>
        <td>
          <?php echo $form['pass']->renderError() ?>
          <?php echo $form['pass'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['sexe']->renderLabel() ?></th>
        <td>
          <?php echo $form['sexe']->renderError() ?>
          <?php echo $form['sexe'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['naissance']->renderLabel() ?></th>
        <td>
          <?php echo $form['naissance']->renderError() ?>
          <?php echo $form['naissance'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['profession']->renderLabel() ?></th>
        <td>
          <?php echo $form['profession']->renderError() ?>
          <?php echo $form['profession'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['parlementaire_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['parlementaire_id']->renderError() ?>
          <?php echo $form['parlementaire_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['mail']->renderLabel() ?></th>
        <td>
          <?php echo $form['mail']->renderError() ?>
          <?php echo $form['mail'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['circo']->renderLabel() ?></th>
        <td>
          <?php echo $form['circo']->renderError() ?>
          <?php echo $form['circo'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['circo_num']->renderLabel() ?></th>
        <td>
          <?php echo $form['circo_num']->renderError() ?>
          <?php echo $form['circo_num'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['photo']->renderLabel() ?></th>
        <td>
          <?php echo $form['photo']->renderError() ?>
          <?php echo $form['photo'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
