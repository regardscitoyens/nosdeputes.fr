= Installation de CPC sur une machine GNU/Linux =

== Environnement de travail ==

 * Subversion

{{{
 sudo apt-get install subversion
}}}

 * Environnement LAMP

{{{
 sudo tasksel install lamp-server
 sudo apt-get install phpmyadmin php5-cli imagemagick php5-imagick
}}}

phpMyAdmin est optionnel mais recommandé

 * Environnement pour le parsing

{{{
  sudo aptitude install libwww-mechanize-perl libfile-path-perl
}}}


 * Modules Apache et PHP

Il est impératif d'activer le mod rewrite d'apache

{{{
 sudo a2enmod rewrite
}}}

Imagemagick et php5-imagick sont indispensables pour la carte des circonscriptions.


== Installation ==

 * Récupérer la version actuelle :

{{{
 svn co https://cpc.regardscitoyens.org/svn/cpc/trunk/project/ cpc
 cd cpc
}}}

Pour le Sénat (par la suite remplacer cpc par sénat):

{{{
 svn co https://cpc.regardscitoyens.org/svn/cpc/branches/senat senat
 cd senat
}}}


Installe cpc dans votre home

 * Créer une base mysql pour le projet :

{{{
 nom de la base : cpc
 login : cpc
 pass : MOT_DE_PASSE_A_CHOISIR
 host : localhost
}}}

Le plus simple étant de créer un utilisateur "cpc" avec ces paramètres dans phpMyAdmin (!Privilèges/Ajouter un utilisateur) en selectionnant "Créer une base portant son nom et donner à cet utilisateur tous les privilèges sur cette base"

 * Adapter la configuration du projet :

{{{
 bash bin/init
}}}

Cela crée les fichiers config/ProjectConfiguration.class.php, config/databases.yml, bin/db.inc et config/app.yml.

 * Adapter en fonction de votre installation :

{{{
 nano config/ProjectConfiguration.class.php
}}}

Changer "/home/cpc" pour le chemin vers votre configuration (1 modification)

Si vous suivez ce tuto à la lettre "/home/cpc" deviens donc "/home/NOM_UTILISATEUR/cpc"

{{{
 nano config/databases.yml
 nano bin/db.inc
}}}

Remplacer "MOT_DE_PASSE" par celui que vous avez choisi pour la base que l'on vient de créer (1 modification)

 * Adapter la configuration en fonction de la législature traitée :

{{{
 nano config/app.yml
}}}

 * Créer le fichier routing de la législature définie dans bin/db.inc

{{{
 bash bin/generate_routing.sh
}}}

 * copier et adapter la configuration apache :

{{{
 sudo cp config/vhost.sample /etc/apache2/sites-enabled/001-cpc
 sudo nano /etc/apache2/sites-enabled/001-cpc
}}}

Changer "/home/cpc" pour le chemin vers votre configuration comme précédemment (4 modifications)

 * Editer le fichier hosts

{{{
 sudo nano /etc/hosts
}}}

 * Ajouter cette ligne :

{{{
 127.0.0.1	my.cpc.regardscitoyens.org
}}}

 * Redémarrer apache

{{{
 sudo /etc/init.d/apache2 restart
}}}

 * Préparez l'environnement de travail php symfony :

{{{
 php symfony doctrine:build --all --no-confirmation
}}}

 * Télécharger le dernier dump de la base de données :

http://www.regardscitoyens.org/telechargement/donnees/

Pour le Sénat :
{{{
wget http://dev.nossenateurs.fr/dump-senat.sql.gz -O data/data.sql.gz --user=VOTRELOGIN --password=VOTREPASS
}}}

 * Importer le dump dans mysql :

{{{
 tar xzvf DATE_A_ADAPTER_nosdeputes.fr_donnees.tgz
 mysql -u cpc -p --default-character-set=utf8 cpc < nosdeputes.fr_donnees/data.sql
 (Un prompt vous demandera le mot de passe défini plus tôt)
}}}

 * Nettoyer le cache après l'import de la base de données :

{{{
 php symfony cc
}}}

 * Pour permettre la création de graphiques, créer le répertoire suivant et donnez lui les permissions correctes :

{{{
 mkdir -p web/images/tmp/xspchart
 sudo chown -R www-data:www-data web/images/tmp/xspchart
}}}

 * Tester

 http://my.cpc.regardscitoyens.org/frontend_dev.php/

 * Si à l'affichage de frontend_dev.php dans le navigateur, PHP dit qu'il n'a pas pu allouer assez de mémoire, augmenter la taille maximale de mémoire autorisée : 

{{{
sudo nano /etc/php5/cli/php.ini
}}}

cherchez la ligne

{{{
memory_limit = 16M      ; Maximum amount of memory a script may consume (16MB)
}}}

et mettez une valeur haute, par exemple

{{{
memory_limit = 128M      ; Maximum amount of memory a script may consume (16MB)
}}}

== Installation de Solr ==

installer tomcat6

{{{
sudo aptitude install tomcat6
}}}

Remplacer la valeur du dossier data dans le fichier de configuration de solr :

{{{
vim lib/vendor/SolrServer/solr/conf/solrconfig.xml

  <dataDir>/MON/REPERTOIRE/project/lib/vendor/SolrServer/solr/data</dataDir>
}}}

S'assurer que ce répertoire data soit accessible en écriture par l'utilisateur tomcat6 (ou tomcatXX suivant votre version de Tomcat) :

{{{
  sudo chmod g+w /MON/REPERTOIRE/project/lib/vendor/SolrServer/solr/data
  sudo chown tomcat6 /MON/REPERTOIRE/project/lib/vendor/SolrServer/solr/data

}}}

Brancher solr avec Tomcat en créant le fichier solr_nossenateur.xml dans /etc/tomcat6/Catalina/localhost/ contenant :

{{{
<Context docBase="/MON/REPERTOIRE/project/lib/vendor/SolrServer/webapps/solr.war" debug="0" crossContext="true" >
   <Environment name="solr/home" type="java.lang.String" value="/MON/REPERTOIRE/project/lib/vendor/SolrServer/solr" override="true" />
</Context>
}}}

Configurer symfony pour utiliser solr dans config/app.yml :

{{{
  solr:
    port: 8080
    url: /solr_nossenateurs
}}}

L'url est solr_nossenateurs car le fichier de configuration tomcat6 s'appelle ainsi.

Redémarrer tomcat et regénérer le cache de symfony :

{{{
sudo /etc/init.d/tomcat6 restart
php symfony cc
}}}

== Aller plus loin ==

* OptimisationProduction détaille les éléments à optimiser pour un passage en production