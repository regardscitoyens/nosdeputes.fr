<?php use_helper('Text');

if (!count($commentaires)) : ?>
<p>Le travail de ce député n'a pas encore inspiré de commentaire aux utilisateurs.</p>
<?php else : 
  foreach($commentaires as $c) :
?>
      <div class="boite_extrait_article">
        <div class="b_e_a_h"><div class="b_e_a_hg"></div><div class="b_e_a_hd"></div></div>
        <div class="b_e_a_cont">
          <div class="mini_boite_citoyen">
            <div class="m_b_c_h"><div class="m_b_c_hg"></div><div class="m_b_c_hd"></div></div>
            <div class="m_b_c_cont">
              <div class="m_b_c_photo">
              
              </div>
              <div class="m_b_c_text">
    <h3><?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
 ?></h3>
              <p><?php echo myTools::displayDate($c->created_at); ?></p>
<p class="note"><img src="/css/fixe/images/mini_etoile.png" alt="***"/></p>
              </div>
            </div>
            <div class="m_b_c_b"><div class="m_b_c_bg"></div><div class="m_b_c_bd"></div></div>
          </div>
          <div class="b_e_a_article">
    <p><a href="<?php echo url_for($c->lien); ?>"><?php echo $c->presentation; ?></a></p>
    <p><?php echo truncate_text($c->commentaire, 150); ?>
<span class="lire_suite"><a href="<?php echo url_for($c->lien); ?>">Lire la suite</a></span>
            </p>
          </div>
        </div>
        <div class="b_e_a_b"><div class="b_e_a_bg"></div><div class="b_e_a_bd"></div></div>
      </div>  
<?php endforeach;
endif;?>