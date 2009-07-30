<?php $style = 'fixe'; # en attendant le style switcher ?>
<div class="fiche_depute">
  <div class="depute_gauche">
		<div class="photo_depute">
    <?php if ($parlementaire->getPhoto()) { echo image_tag($parlementaire->getPhoto(), 'class="photo_fiche" alt=Photo de '.$parlementaire->nom); } ?>
		</div>
  </div>
  <div class="depute_milieu">
    <h1><?php echo $parlementaire->nom; ?></h1>
    <p>Né le ... (... ans) à ... (...)</p>
    <ul>
      <li><?php echo $parlementaire->getLongStatut(); ?> depuis le <?php echo $parlementaire->debut_mandat ?></li>
      <li>Groupe politique : <?php echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?> (<?php echo $parlementaire->getGroupe()->getFonction(); ?>)</li>
      <li>Profession : <?php if ($parlementaire->profession) : echo link_to($parlementaire->profession, '@list_parlementaires_profession?profession='.$parlementaire->profession); else : ?>Non communiquée<?php endif; ?></li>
      <li><?php echo link_to('Fiche sur le site de l\'Assemblée Nationale', $parlementaire->url_an, array('title' => 'Lien externe', 'onclick' => 'window.open(this.href); return false;')); ?></li>
      <?php if ($parlementaire->site_web) : ?>
      <li><?php echo link_to('Site web', $parlementaire->site_web, array('title' => 'Lien externe', 'onclick' => 'window.open(this.href); return false;')); ?></li>
      <?php endif; ?>
      <li>Suppléant : Mr Toto</li>
    </ul>
  </div>
  <div class="depute_droite">
    <h2>Table des matières</h2>
    <ul>
      <li><a href="#b1">Infos générales</a></li>
      <li><a href="#b2">Travaux Législatifs</a></li>
      <li><a href="#b3">Questions au gouvernement</a></li>
      <li><a href="#b4">Présence en hémicycle et commission</a></li>
      <li><a href="#b5">Les votes en séance</a></li>
    </ul>
  </div>
  <div class="barre_activite">
    <h2>Activité parlementaire : </h2>
    <ul>
      <li title="Interventions en séance"><a href="#"><?php echo image_tag('../css/'.$style.'/images/seance.png', 'alt="Interventions en séance"'); ?> : 7</a></li>
      <li title="Interventions en commissions"><a href="#"><?php echo image_tag('../css/'.$style.'/images/rapport.png', 'alt="Interventions en commissions"'); ?> : 2</a></li>
      <li title="Rapports"><a href="#"><?php echo image_tag('../css/'.$style.'/images/rapport.png', 'alt="Rapports"'); ?> : 2</a></li>
      <li title="Propositions de loi (auteur)"><a href="#"><?php echo image_tag('../css/'.$style.'/images/balance.png', 'alt="Propositions de loi (auteur)"'); ?> : 0</a></li>
      <li title="Questions"><a href="#"><?php echo image_tag('../css/'.$style.'/images/question.png', 'alt="Questions"'); ?>   : 50</a></li>
      <li title="Depuis le"><?php echo image_tag('../css/'.$style.'/images/calendrier.png', 'alt="Depuis le : "'); echo $parlementaire->debut_mandat; ?></li>
    </ul>
  <span class="float_droite"><?php echo image_tag($parlementaire->getGroupe()->getNom().'.gif', 'alt="Logo '.$parlementaire->getGroupe()->getNom().' "'); ?></span>
  </div>
  <div class="stopfloat"></div>
</div>

<div class="contenu_depute">
  <div class="boite_depute" id="b1">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Infos générales</h2>
			
      <h3>Le mot de <?php echo $parlementaire->nom; ?></h3>
      <p class="mot_dep">Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa, quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt, explicabo. Nemo enim ipsam voluptatem, quia voluptas sit, aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos.</p>
			
			<h3>Responsabilités</h3>
			<ul>
				<li>Parlementaires :
					<ul>
						<?php foreach ($parlementaire->getResponsabilites() as $resp) { ?>
						<li><?php echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug()); ?> (<?php echo $resp->getFonction(); ?>)</li>
						<?php } ?>
					</ul>
				</li>
				<?php if ($parlementaire->getExtras()) { ?>
				<li>Extra-parlementaires :
					<ul>
						<?php foreach ($parlementaire->getExtras() as $extra) { ?>
						<li><?php echo link_to($extra->getNom(),'@list_parlementaires_organisme?slug='.$extra->getSlug() ); ?> (<?php echo $extra->getFonction(); ?>)</li>
						<?php } ?>
					</ul>
					<?php } ?>
				</li>
			</ul>
			
      <h3>Permanence parlementaire</h3>
			<p>102 Boulevard Blossac<br />
			86100 Châtellerault<br />
			Téléphone : <a href="callto:0033549021575">05 49 02 15 75</a><br />
			Télécopie : 05 49 02 15 76
			</p>
			
      <h3>Autres adresses</h3>
      <ul>
				<li>Assemblée nationale<br />126 rue de l'Université<br />75355 Paris 07 SP<br /></li>
				<li>Cabinet du Maire<br />78 Boulevard Blossac<br />BP619<br />86106 Châtellerault cedex<br /></li>
      </ul>
			
      <h3>Tables nominatives des débats</h3>
      <ul>
				<li><a href="http://www.assembleenationale.fr/13/tribun/tnom/2007/226.pdf" onclick="window.open(this.href); return false;">Table nominative en cours, actualisée périodiquement par le service des archives et de la recherche historique parlementaire</a></li>
      </ul>
			
      <h3>Place dans l'hémicycle</h3>
      <p>Numéro de la place occupée : 385</p>
      
			<h3>Autres mandats</h3>
			<ul>
				<li>Mandats intercommunaux
					<ul>
						<li>Président : 
							<ul>
								<li>communauté d'agglomération du Pays Châtelleraudais</li>
							</ul>
						</li>
					</ul>
				</li>
				<li>Mandats locaux
					<ul>
						<li>Maire :
							<ul>
								<li>Châtellerault, Vienne (34126 habitants)</li>
							</ul>
						</li>
					</ul>
				</li>
      </ul>
      <h3>Anciens mandats</h3>
      <ul>
				<li>Parlement Européen
					<ul>
						<li>Député
							<ul>
								<li>du 24/07/1984 au 25/05/1989</li>
							</ul>
						</li>
					</ul>
				</li>
				<li>Assemblée Nationale
					<ul>
						<li>Député
							<ul>
								<li>Élu le 19/03/1978 - Mandat du 03/04/1978 (élections générales) au 22/05/1981 (Fin de législature)</li>
								<li>Réélu le 16/03/1986 - Mandat du 02/04/1986 (élections générales) au 14/05/1988 (Fin de législature)</li>
								<li>Réélu le 28/03/1993 - Mandat du 02/04/1993 (élections générales) au 21/04/1997 (Fin de législature)</li>
								<li>Réélu le 01/06/1997 - Mandat du 01/06/1997 (élections générales) au 18/06/2002 (Fin de législature)</li>
								<li>Réélu le 16/06/2002 - Mandat du 19/06/2002 (élections générales) au 19/06/2007 (Fin de législature)</li>
							</ul>
						</li>
					</ul>
				</li>
				<li>Mandats intercommunaux
					<ul>
						<li>Conseil général de la Vienne
							<ul>
								<li>Membre
									<ul>
										<li>du 01/07/1977 au 21/03/1982</li>
										<li>du 10/03/2008 au 10/04/2008</li>
									</ul>
								</li>
								<li>Vice-président
									<ul>
										<li>du 22/03/1982 au 02/10/1988</li>
										<li>du 03/10/1988 au 27/03/1994</li>
										<li>du 28/03/1994 au 18/03/2001</li>
										<li>du 19/03/2001 au 09/03/2008</li>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
				</li>
				<li>Mandats locaux
					<ul>
						<li>Conseil municipal de Châtellerault (Vienne)
							<ul>
								<li>Membre
									<ul>
										<li>du 14/03/1983 au 19/03/1989</li>
										<li>du 20/03/1989 au 18/06/1995</li>
										<li>du 19/06/1995 au 30/09/2000</li>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
				</li>
      </ul>
      <p>Source : <a href="http://www.assembleenationale.fr/" onclick="window.open(this.href); return false;">Assemblée Nationale</a></p>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
    
  <div class="boite_depute" id="b2">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Travaux Législatifs</h2>
      <ul>
				<li><a href="#">Propositions de loi et de résolution</a></li>
				<li><a href="#">Rapports</a></li>
				<li><a href="#">Séances publiques contenant au moins une intervention de <?php echo $parlementaire->nom; ?></a></li>
				<li><a href="#">Réunions de commissions contenant au moins une intervention de <?php echo $parlementaire->nom; ?></a></li>
      </ul>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
	
	<div class="boite_depute" id="b3">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Questions au gouvernement</h2>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
  
  <div class="boite_depute" id="b4">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Présence en hémicycle et commission</h2>
			<h3><?php echo link_to("Présences",'@parlementaire_presences?slug='.$parlementaire->getSlug()); ?></h3>
			<h3><?php echo link_to("Interventions",'@parlementaire_interventions?slug='.$parlementaire->getSlug()); ?></h3>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
  
  <div class="boite_depute" id="b5">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Les votes en séance</h2>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
  
  <div class="bas_depute">
    <div class="bas_depute_g">
      <h2>Statut du Député : <span class="statut">Dépressif ^^</span></h2>
      <div class="boite_citoyen">
				<div class="b_c_h"><div class="b_c_hg"></div><div class="b_c_hd"></div></div>
				<div class="b_c_cont">
					<div class="b_c_photo">
						<?php if ($parlementaire->getPhoto()) { echo image_tag($parlementaire->getPhoto(), 'alt=Photo de '.$parlementaire->nom); } ?>
					</div>
					<div class="b_c_text">
						<h3><?php echo $parlementaire->nom; ?> <span class="note"><?php echo image_tag('../css/'.$style.'/images/etoile.png', 'alt="***"'); ?></span></h3>
						<p><a href="#">23 articles</a></p>
						<p><a href="#">Voir la fiche perso</a></p>
					</div>
				</div>
				<div class="b_c_b"><div class="b_c_bg"></div><div class="b_c_bd"></div></div>
      </div>
      
      <h2>Attachés Parlementaires inscrits</h2>
      <div class="boite_citoyen">
				<div class="b_c_h"><div class="b_c_hg"></div><div class="b_c_hd"></div></div>
				<div class="b_c_cont">
					<div class="b_c_photo">
					
					</div>
					<div class="b_c_text">
						<h3>Amandine M. <span class="note"><?php echo image_tag('../css/'.$style.'/images/etoile.png', 'alt="***"'); ?></span></h3>
						<p><a href="#">125 articles</a></p>
						<p><a href="#">Voir la fiche perso</a></p>
					</div>
				</div>
				<div class="b_c_b"><div class="b_c_bg"></div><div class="b_c_bd"></div></div>
      </div>
      
      <div class="boite_citoyen">
				<div class="b_c_h"><div class="b_c_hg"></div><div class="b_c_hd"></div></div>
				<div class="b_c_cont">
					<div class="b_c_photo">
					
					</div>
					<div class="b_c_text">
						<h3>Michael J. <span class="note"><?php echo image_tag('../css/'.$style.'/images/etoile.png', 'alt="***"'); ?></span></h3>
						<p><a href="#">1 articles</a></p>
						<p><a href="#">Voir la fiche perso</a></p>
					</div>
				</div>
				<div class="b_c_b"><div class="b_c_bg"></div><div class="b_c_bd"></div></div>
      </div>
    </div>
    <div class="bas_depute_d">
      <h2>Derniers articles des membres pour : <a href="#">#<?php echo $parlementaire->slug; ?></a> <span class="rss"><a href="#"><?php echo image_tag('../css/'.$style.'/images/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
      <div class="boite_extrait_article">
				<div class="b_e_a_h"><div class="b_e_a_hg"></div><div class="b_e_a_hd"></div></div>
				<div class="b_e_a_cont">
					<div class="mini_boite_citoyen">
						<div class="m_b_c_h"><div class="m_b_c_hg"></div><div class="m_b_c_hd"></div></div>
						<div class="m_b_c_cont">
							<div class="m_b_c_photo">
							
							</div>
							<div class="m_b_c_text">
							<h3>Jacqueline D. <span class="note"><?php echo image_tag('../css/'.$style.'/images/mini_etoile.png', 'alt="***"'); ?></span></h3>
							<p><a href="#">14 articles</a><br />
							<a href="#">Voir la fiche perso</a></p>
							</div>
						</div>
						<div class="m_b_c_b"><div class="m_b_c_bg"></div><div class="m_b_c_bd"></div></div>
					</div>
					<div class="b_e_a_article">
						<p>
						Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa, quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt, explicabo. Nemo enim ipsam voluptatem, quia voluptas sit... <span class="lire_suite"><a href="#">Lire la suite</a></span>
						</p>
					</div>
				</div>
				<div class="b_e_a_b"><div class="b_e_a_bg"></div><div class="b_e_a_bd"></div></div>
      </div>  
      
      <div class="boite_extrait_article">
				<div class="b_e_a_h"><div class="b_e_a_hg"></div><div class="b_e_a_hd"></div></div>
				<div class="b_e_a_cont">
					<div class="mini_boite_citoyen">
						<div class="m_b_c_h"><div class="m_b_c_hg"></div><div class="m_b_c_hd"></div></div>
						<div class="m_b_c_cont">
							<div class="m_b_c_photo">
							
							</div>
							<div class="m_b_c_text">
							<h3>André F. <span class="note"><?php echo image_tag('../css/'.$style.'/images/mini_etoile.png', 'alt="***"'); ?></span></h3>
							<p><a href="#">11 articles</a><br />
							<a href="#">Voir la fiche perso</a></p>
							</div>
						</div>
						<div class="m_b_c_b"><div class="m_b_c_bg"></div><div class="m_b_c_bd"></div></div>
					</div>
					<div class="b_e_a_article">
						<p>
						Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa, quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt, explicabo. Nemo enim ipsam voluptatem, quia voluptas sit... <span class="lire_suite"><a href="#">Lire la suite</a></span>
						</p>
					</div>
				</div>
				<div class="b_e_a_b"><div class="b_e_a_bg"></div><div class="b_e_a_bd"></div></div>
      </div>
      
      <div class="boite_extrait_article">
				<div class="b_e_a_h"><div class="b_e_a_hg"></div><div class="b_e_a_hd"></div></div>
				<div class="b_e_a_cont">
					<div class="mini_boite_citoyen">
						<div class="m_b_c_h"><div class="m_b_c_hg"></div><div class="m_b_c_hd"></div></div>
						<div class="m_b_c_cont">
							<div class="m_b_c_photo">
							
							</div>
							<div class="m_b_c_text">
							<h3>Bébert G. <span class="note"><?php echo image_tag('../css/'.$style.'/images/mini_etoile.png', 'alt="***"'); ?></span></h3>
							<p><a href="#">110 articles</a><br />
							<a href="#">Voir la fiche perso</a></p>
							</div>
						</div>
						<div class="m_b_c_b"><div class="m_b_c_bg"></div><div class="m_b_c_bd"></div></div>
					</div>
					<div class="b_e_a_article">
						<p>
						Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa, quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt, explicabo. Nemo enim ipsam voluptatem, quia voluptas sit... <span class="lire_suite"><a href="#">Lire la suite</a></span>
						</p>
					</div>  
				</div>
				<div class="b_e_a_b"><div class="b_e_a_bg"></div><div class="b_e_a_bd"></div></div>
      </div>
      <ul class="liens_articles">
				<li><strong>Articles pour #<?php echo $parlementaire->slug; ?> : </strong>
					<ul>
						<li><a href="#">- Les plus repris</a></li>
						<li><a href="#">- Top karma</a></li>
						<li><a href="#">- Voir tous</a></li>
					</ul>
				</li>
      </ul>
      <ul class="tags">
				<li><strong>Tags connexes : </strong>
					<ul>
						<li><a href="#">#hadopi</a></li>
						<li><a href="#">#<?php echo $parlementaire->slug; ?></a></li>
						<li><a href="#">#vienne</a></li>
						<li><a href="#">#maire</a></li>
						<li><a href="#">#godillot</a></li>
					</ul>
				</li>
      </ul>
    </div>
    <div class="stopfloat"></div>
  </div>
</div>

<!-- 
<h1><?php echo $parlementaire->nom; ?></h1>
<?php if ($parlementaire->getPhoto()) { echo image_tag($parlementaire->getPhoto(), 'alt=Photo de '.$parlementaire->nom); } ?>
<h2><?php echo $parlementaire->getLongStatut(); ?></h2>
<p>Mandat en cours débuté le <?php echo $parlementaire->debut_mandat ?></p>
<?php if ($parlementaire->site_web) : ?>
<p><a href="<?php echo $parlementaire->site_web ?>">Site web</a></p>
<?php endif; ?>
<p><a href="<?php echo $parlementaire->url_an ?>">Fiche sur le site de l'Assemblée Nationale</a></p>
<p>Profession : 
<?php if ($parlementaire->profession) : ?>
<?php echo link_to($parlementaire->profession, '@list_parlementaires_profession?profession='.$parlementaire->profession); ?>
<?php else : ?>
Non communiquée
<?php endif; ?></p>
<p>Groupe : <?php echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?> (<?php echo $parlementaire->getGroupe()->getFonction(); ?>)</p>
<p>Responsabilités parlementaires :</p>
<ul>
<?php foreach ($parlementaire->getResponsabilites() as $resp) { ?>
<li><?php echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug()); ?> (<?php echo $resp->getFonction(); ?>)</li>
<?php } ?>
</ul>
<?php if ($parlementaire->getExtras()) { ?>
<p>Responsabilités extra-parlementaires :</p>
<ul>
<?php foreach ($parlementaire->getExtras() as $extra) { ?>
<li><?php echo link_to($extra->getNom(),'@list_parlementaires_organisme?slug='.$extra->getSlug() ); ?> (<?php echo $extra->getFonction(); ?>)</li>
<?php } ?>
</ul>
<?php } ?>
<p><?php echo link_to("Présences",'@parlementaire_presences?slug='.$parlementaire->getSlug()); ?></p>
<p><?php echo link_to("Interventions",'@parlementaire_interventions?slug='.$parlementaire->getSlug()); ?></p>
 -->
