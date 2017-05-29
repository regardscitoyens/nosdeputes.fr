# NosDéputés.fr

Ce dossier git contient le code source des sites web http://www.nosdeputes.fr
et https://www.nossenateurs.fr. Pour le télécharger :

 * nosdeputes.fr ``git clone git@github.com:regardscitoyens/nosdeputes.fr.git``
 * nossenateurs.fr ``git clone git@github.com:regardscitoyens/nosdeputes.fr.git --branch nossenateurs.fr``

## Instructions d'installation pour une machine GNU/Linux

### Environnement de travail

Sous une distribution type Ubuntu, installer les packages suivants:

```bash
sudo apt-get install git
sudo tasksel install lamp-server php5-cli
sudo apt-get install phpmyadmin # optionnel mais recommandé
sudo apt-get install imagemagick php5-imagick # Pour la carte des circonscriptions
```

Pour le parsing :

```bash
sudo aptitude install libwww-mechanize-perl libfile-path-perl
```

En environnement de production, il est impératif d'activer le mod rewrite
d'apache:

```bash
sudo a2enmod rewrite
```

### Installation

 * Récupérer la version courante :

   ```bash
   git clone git@github.com:regardscitoyens/nosdeputes.fr.git
   ```

   Ou bien, pour nossenateurs.fr :

   ```bash
   git clone git@github.com:regardscitoyens/nosdeputes.fr.git --branch nossenateurs.fr
   ```

 * Créer une base mysql pour le projet :

   ```
   nom de la base : cpc
   login : cpc
   pass : MOT_DE_PASSE_A_CHOISIR
   host : localhost
   ```

   Le plus simple étant de créer un utilisateur `cpc` avec ces paramètres dans
   phpMyAdmin (Privilèges/Ajouter un utilisateur) en sélectionnant *"Créer une
   base portant son nom et donner à cet utilisateur tous les privilèges sur
   cette base"*

 * Lancer la commande suivante. Elle créera des fichiers qui devront être modifiés par la suite.

   ```bash
   bash bin/init
   ```

   Cela crée les fichiers `config/ProjectConfiguration.class.php`,
   `config/databases.yml`, `bin/db.inc` et `config/app.yml`.

 * Adapter en fonction de votre installation :

   * `config/ProjectConfiguration.class.php` : Le chemin vers le dossier de
     travail est `/home/cpc` dans le fichier. Remplacez-le (1 modification) pour
     le faire correspondre à votre configuration: il s'agit probablement du
     chemin `/home/NOM_UTILISATEUR/nosdeputes.fr`.

   * `config/databases.yml` et `bin/db.inc` : Remplacer "MOT_DE_PASSE" par celui que
     vous avez choisi pour la base que l'on vient de créer (1 modification).

   * `config/app.yml` : Adapter la configuration en fonction de la législature traitée.

 * Créer le fichier routing de la législature définie dans `bin/db.inc` par la
   commande suivante:

   ```bash
   bash bin/generate_routing.sh
   ```

 * Préparez l'environnement de travail php symfony. La commande suivante
   réinitialise la structure de la base de données (et la vide si nécessaire) :

   ```bash
   php symfony doctrine:build --all --no-confirmation
   ```

 * Télécharger le dernier dump de la base de données depuis https://www.regardscitoyens.org/telechargement/donnees/nosdeputes.fr/

 * Importer le dump dans mysql. Un prompt vous demandera le mot de passe défini
   plus tôt :

   ```bash
   tar xzvf DATE_A_ADAPTER_nosdeputes.fr_donnees.tgz
   mysql -u cpc -p --default-character-set=utf8 cpc < nosdeputes.fr_donnees/data.sql
   ```

 * Nettoyer le cache après l'import de la base de données :

   ```bash
   php symfony cc
   ```

 * Pour permettre la création de graphiques, créez le répertoire suivant et donnez lui les permissions correctes :

   ```bash
   mkdir -p web/images/tmp/xspchart
   sudo chown -R www-data:www-data web/images/tmp/xspchart
   ```

### Configuration du serveur Apache

*Si vous ne souhaitez pas mettre en place un environnement de production, vous pouvez sauter cette étape*

 * Copier et adapter la configuration apache :

   ```bash
    sudo cp config/vhost.sample /etc/apache2/sites-enabled/001-cpc
    sudo nano /etc/apache2/sites-enabled/001-cpc
   ```

   Changer `/home/cpc` pour le chemin vers votre configuration comme
   précédemment (4 modifications)

 * Editer le fichier `hosts` :

   ```bash
   sudo nano /etc/hosts
   ```

 * Ajouter cette ligne :

   ```
   127.0.0.1	my.cpc.regardscitoyens.org
   ```

 * Redémarrer apache

   ```bash
   sudo /etc/init.d/apache2 restart
   ```

### Installation de Solr

*Si vous n'avez pas besoin du moteur de recherche interne au site web, vous pouvez sauter cette étape.*

 * installer tomcat6

   ```bash
   sudo aptitude install tomcat6
   ```

 * Remplacer la valeur du dossier data dans le fichier de configuration de solr :

   ```bash
   vim lib/vendor/SolrServer/solr/conf/solrconfig.xml
   ```
   ```xml
   <dataDir>/MON/REPERTOIRE/project/lib/vendor/SolrServer/solr/data</dataDir>
   ```

 * S'assurer que ce répertoire data soit accessible en écriture par l'utilisateur tomcat6 (ou tomcatXX suivant votre version de Tomcat) :

   ```bash
   sudo chmod g+w /MON/REPERTOIRE/project/lib/vendor/SolrServer/solr/data
   sudo chown tomcat6 /MON/REPERTOIRE/project/lib/vendor/SolrServer/solr/data
   ```

 * Brancher solr avec Tomcat en créant le fichier `solr_nossenateur.xml` dans /etc/tomcat6/Catalina/localhost/ contenant :

   ```xml
   <Context docBase="/MON/REPERTOIRE/project/lib/vendor/SolrServer/webapps/solr.war" debug="0" crossContext="true" >
      <Environment name="solr/home" type="java.lang.String" value="/MON/REPERTOIRE/project/lib/vendor/SolrServer/solr" override="true" />
   </Context>
   ```

 * Configurer symfony pour utiliser solr dans `config/app.yml` :

   ```
     solr:
       port: 8080
       url: /solr_nossenateurs
   ```

   L'url est `solr_nossenateurs` car le fichier de configuration tomcat6 s'appelle ainsi.

 * Redémarrer tomcat et régénérer le cache de symfony :

   ```bash
   sudo /etc/init.d/tomcat6 restart
   php symfony cc
   ```

### Utilisation et développement

 Si vous avez installé un environnement de production (par Apache), l'adresse
 suivante devrait fonctionner : http://my.cpc.regardscitoyens.org/frontend_dev.php

 Sinon, vous pouvez lancer un serveur web léger en tapant ``php -S
 127.0.0.1:8000 -t web`` dans le dossier `nosdeputes.fr/` et vous connecter à
 http://127.0.0.1:8000/frontend_dev.php

 L'utilisation de la page `frontend_dev.php` vous permet de naviguer sur le site
 avec des informations de debug très pratiques pour le développement.

### Aller plus loin

 * [OptimisationProduction](http://cpc.regardscitoyens.org/trac/wiki/OptimisationProduction)
   détaille les éléments à optimiser pour un passage en production

### Bugs connus

 * Si à l'affichage de frontend_dev.php dans le navigateur, PHP dit qu'il n'a pas pu allouer assez de mémoire, augmenter la taille maximale de mémoire autorisée : 

   ```bash
   sudo nano /etc/php5/cli/php.ini
   ```

   cherchez la ligne

   ```
   memory_limit = 16M      ; Maximum amount of memory a script may consume (16MB)
   ```

   et mettez une valeur haute, par exemple

   ```
   memory_limit = 128M      ; Maximum amount of memory a script may consume (16MB)
   ```

