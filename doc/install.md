# Instructions d'installation pour une machine GNU/Linux

## Dépendances et environnement de travail

Sous une distribution type Ubuntu, installer les packages suivants :

```bash
sudo apt-get install git
sudo tasksel install lamp-server php5-cli
sudo apt-get install phpmyadmin # optionnel mais recommandé
sudo apt-get install imagemagick php5-imagick # Pour la carte des circonscriptions
sudo apt-get install php5-gd # Pour les photos et plots sur certaines configs
```

Pour le parsing :

```bash
sudo aptitude install libwww-mechanize-perl libfile-path-perl
```

## Installation

 * Récupérer la version courante :

    ```bash
    git clone https://github.com/regardscitoyens/nosdeputes.fr.git
    cd nosdeputes.fr
    ```

    Ou bien, pour NosSénateurs.fr (puis remplacer `cpc` par `senat` pour la suite du tutoriel) :

    ```bash
    git clone https://github.com/regardscitoyens/nosdeputes.fr.git --branch nossenateurs.fr nossenateurs.fr
    cd nossenateurs.fr
    ```

 * Créer une base mysql pour le projet :

    ```
    nom de la base : cpc
    login : cpc
    pass : MOT_DE_PASSE_A_CHOISIR
    host : localhost
    ```

    Le plus simple étant de créer un utilisateur `cpc` avec ces paramètres dans phpMyAdmin (Privilèges/Ajouter un utilisateur) en sélectionnant *"Créer une base portant son nom et donner à cet utilisateur tous les privilèges sur cette base"*

 * Préparer la configuration :

    ```bash
    bash bin/init
    ```

    Cette commande créera les fichiers suivants à adapter en fonction de votre installation :

    * `config/ProjectConfiguration.class.php` : Le chemin vers le dossier de travail est `/home/cpc/project` dans le fichier. Remplacez-le (1 modification) pour le faire correspondre à votre configuration, par exemple `/home/NOM_UTILISATEUR/nosdeputes.fr`.

    * `config/databases.yml` : Remplacer `MOT_DE_PASSE` par celui que vous avez choisi pour la base que l'on vient de créer (1 modification), et `cpc` par le nom choisi pour la base et son utilisateur si nécessaire (2 modifications)`.

    * `config/app.yml` : Adapter la configuration en fonction de la législature traitée.

    * `apps/frontend/config/factories.yml` : Ajuster la configuration uniquement pour optimiser la production ([voir section dédiée](#optimisations-de-la-configuration-pour-le-déploiement-en-production)).

    * `bin/db.inc` : Adapter `MYSQLID`, `DBNAME`, `PATH_APP` et `LEGISLATURE` comme pour les précédents fichiers.


 * Créer le fichier routing pour la législature définie dans `bin/db.inc` :

    ```bash
    bash bin/generate_routing.sh
    ```

 * Préparer l'environnement de travail php symfony :

    La commande suivante réinitialise la structure de la base de données (et la vide si nécessaire) :

   ```bash
   php symfony doctrine:build --all --no-confirmation
   ```

 * Charger des données :

    * Télécharger le dernier dump de la base de données :

      * pour NosDéputés.fr : https://www.regardscitoyens.org/telechargement/donnees/nosdeputes.fr/
      * pour NosSénateurs.fr : https://www.regardscitoyens.org/telechargement/donnees/nossenateurs.fr/

    * Importer le dump dans mysql. Un prompt vous demandera le mot de passe défini plus tôt :

    ```bash
    tar xzvf DATE_A_ADAPTER_nosdeputes.fr_donnees.tgz
    mysql -u cpc -p --default-character-set=utf8 cpc < nosdeputes.fr_donnees/data.sql
    # ou
    zcat DATE_A_ADAPTER_nosdeputes.fr_donnees.tgz | mysql -u cpc -p --default-character-set=utf8 cpc
    ```

 * Nettoyer le cache :

    ```bash
    php symfony cc
    ```

 * Préparer les droits sur les fichiers :

    Pour permettre la création de graphiques, créez le répertoire suivant et donnez lui les permissions correctes :

    ```bash
    mkdir -p web/images/tmp/xspchart
    sudo chown -R www-data:www-data web/images/tmp/xspchart
    ```

## Déploiement et développement

### Configuration du serveur via Apache

*Si vous ne souhaitez pas mettre en place un environnement de production, vous pouvez sauter cette étape.*

 * Copier et adapter la configuration apache :

    ```bash
    sudo cp config/vhost.sample /etc/apache2/sites-enabled/001-cpc
    sudo nano /etc/apache2/sites-enabled/001-cpc
    ```

    Changer `/home/cpc/project` pour le chemin vers votre configuration comme précédemment (4 modifications).

 * Activer le mod-rewrite d'Apache

    ```bash
    sudo a2enmod rewrite
    ```

 * Pour accéder en local à votre instance de développement sur my.cpc.regardscitoyens.org : 

    Ajouter cette ligne au fichier `/etc/hosts` (sudo) :

    ```
    127.0.0.1	my.cpc.regardscitoyens.org
    ```

 * Redémarrer apache :

    ```bash
    sudo service apache2 restart
    ```

### Déploiement local simplifié pour développement

Sans passer par Apache, vous pouvez lancer un serveur web léger en tapant ``php -S 127.0.0.1:8000 -t web`` dans le dossier `nosdeputes.fr/`.

### Tester

Si vous avez installé un environnement de développement via Apache, l'adresse suivante devrait fonctionner : http://my.cpc.regardscitoyens.org/frontend_dev.php/

Si vous utilisez le déploiement simplifié : http://127.0.0.1:8000/frontend_dev.php/

L'utilisation de la page `frontend_dev.php` vous permet de naviguer sur le site avec des informations de debug très pratiques pour le développement.

### Problèmes connus

Si à l'affichage de frontend_dev.php dans le navigateur, PHP dit qu'il n'a pas pu allouer assez de mémoire, augmenter la taille maximale de mémoire autorisée : 

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

N'hésitez pas à nous contacter ou [laisser une issue](https://github.com/regardscitoyens/nosdeputes.fr/issues) pour tout problème rencontré.

## Installation de Solr

Solr est le moteur de recherche utilisé dans le projet. Il s'installe sur un moteur de servlet (jetty, tomcat, ...). Cette section décrit le déploiement avec tomcat6 dans le cadre du projet.

*Si vous n'avez pas besoin du moteur de recherche interne au site web, vous pouvez sauter cette étape.*

 * Installer tomcat6 :

    ```bash
    sudo aptitude install tomcat6
    ```

 * Préparer le dossier d'accueil des données Solr :

    * Remplacer le chemin du dossier `dataDir` dans le fichier de configuration de solr `lib/vendor/SolrServer/solr/conf/solrconfig.xml` :

    ```xml
    <dataDir>/MON/REPERTOIRE/lib/vendor/SolrServer/solr/data</dataDir>
    ```

    * Et faire de même dans `bin/db.inc`:

    ```bash
    SOLR_DATA_PATH="$PATH_APP/lib/vendor/SolrServer/solr/data"
    ```

 * S'assurer que ce répertoire data soit accessible en écriture par l'utilisateur tomcat6 (ou tomcatXX suivant votre version de Tomcat) :

    ```bash
    sudo chmod g+w /MON/REPERTOIRE/lib/vendor/SolrServer/solr/data
    sudo chown tomcat6 /MON/REPERTOIRE/lib/vendor/SolrServer/solr/data
    ```

 * Brancher solr avec Tomcat en créant le fichier `solr_nosdeputes.xml` (ou `solr_nossenateur.xml`) dans `/etc/tomcat6/Catalina/localhost/` contenant :

    ```xml
    <Context docBase="/MON/REPERTOIRE/lib/vendor/SolrServer/webapps/solr.war" debug="0" crossContext="true" >
       <Environment name="solr/home" type="java.lang.String" value="/MON/REPERTOIRE/lib/vendor/SolrServer/solr" override="true" />
    </Context>
    ```

 * Redémarrer tomcat :

    ```bash
    sudo /etc/init.d/tomcat6 restart
    ```

    Solr sera alors joignable sur le port 8080 à l'url tomcat définie, par exemple : http://localhost:8180/solr_nosdeputes/

 * Configurer symfony pour utiliser solr dans `config/app.yml` :

    ```
      solr:
        port: 8080
        url: /solr_nosdeputes (ou /solr_nossenateurs)
    ```

    L'url correspond au nom du fichier de configuration tomcat6 créé sans son suffixe `.xml`.

    Puis nettoyer le cache de symfony :

    ```bash
    php symfony cc
    ```

## Optimisations de la configuration pour le déploiement en production

Pour le passage en production, un certain nombre d'optimisations sont activables depuis `apps/frontend/config/factories.yml`.

### Utilisation de memcache :

Pour utiliser memcache comme outil de cache au lieu de l'usage de fichiers, décommentez et ajustez le port de la section dédiée dans apps/frontend/config/factories.yml :

```
# Memcache
# (uncomment and setup to use memcache)
  view_cache:
    class: sfMemcacheCache
    param:
      host: localhost
      port: 11211
      persistent: true
```

### Utilisation de l'envoi différé des mails :

Certains services de mail, ralentissent voire bloquent les envois de mails massif. C'est par défaut le cas de `exim` qui préfère les envois de pas plus de 10 mails par connexion tcp. Pour activer, le mode spool pour l'envoi des mail, il faut modifier la configuration comme suit en commentant la section "Regular mail handling" et en décommentant la section "Pool mail handling" dans `apps/frontend/config/factories.yml` :

```
  mailer:
    param:
# Regular mail handling
#      delivery_strategy: realtime
# Pool mail handling
# (to avoid rate limitations with stronger user)
      delivery_strategy: spool
      spool_class: Swift_FileSpool
      spool_arguments:
        Swift_FileSpool: %SF_ROOT_DIR%/data/mails
```
 
