# Installation avec Ansible et Docker

Cette méthode utilise Ansible pour installer CPC sur une machine (distante ou non) à l'aide de containers docker.

## Prérequis

- Ansible 2.2+
- Docker **non installé**

Conseil : utiliser un virtualenv, par exemple

```sh
$ sudo apt install virtualenvwrapper
$ mkvirtualenv cpc
$ pip install "ansible>=2.2"
```

## Aperçu de la configuration des playbooks

Les variables suivantes sont disponibles (voir `ansible/group_vars/all/main.yml` pour les valeurs par défaut).

**Note**: les variables marquées [U] doivent avoir une valeur unique si plusieurs instances sont installées sur la même machine.

### Configuration de base

* `cpc_domain` [U] : nom de domaine de cette instance
* `cpc_instance_name` [U] : nom de l'instance (`cpc` par défaut) [U]
* `cpc_user` : utilisateur Unix (`cpc` par défaut)
* `cpc_home` [U] : répertoire hébergeant le repository et les fichiers de contrôle (`/opt/cpc` par défaut)
* `cpc_repo` : URL du repository git
* `cpc_version` : version du git à utiliser (branche, tag, commit...)
* `cpc_dump` : chemin local d'un dump (données seulement) à charger, au format SQL gzippé

### Ports TCP

* `cpc_port_web` [U] : port d'écoute pour apache sur le container web
* `cpc_port_phpmyadmin` [U] : port d'écoute pour phpmyadmin
* `cpc_port_mysql` [U] : port d'écoute mysql
* `cpc_port_memcached` [U] : port d'écoute Memcached
* `cpc_port_solr` [U] : port d'écoute Solr

### Eléments optionnels

* `cpc_memcached` : booléen, activer ou non Memcached
* `cpc_memcached_limit` : mémoire maximale utilisée par Memcached (des suffixes peuvent être utilisés, par exemple 100M ou 1G)
* `cpc_spool_mails` : booléen, activer ou non le spooling mail
* `cpc_solr` : booléen, activer ou non Solr
* `cpc_admin_ips` : liste des IP autorisées pour `/frontend_dev.php/`
* `cpc_php_cli_memory_limit` : limite mémoire PHP pour les scripts en ligne de commande
* `cpc_php_web_memory_limit` : limite mémoire PHP depuis Apache
* `cpc_php_show_errors` : booléen, afficher ou non les erreurs PHP
* ̀cpc_enable_cronjobs` : booléen, créer ou non les jobs cron

### Législature

* `cpc_legislature` : numéro de législature
* `cpc_debut_legislature` : date de début de la législature (YYYY-MM-DD)
* `cpc_fin_legislature` : booléen, législature terminée ou non
* `cpc_host_prev` : hostname de l'instance pour la législature précédente
* `cpc_host_next` : hostname de l'instance pour la législature suivante

### Analytics

* `cpc_analytics_id` : ID Google analytics
* `cpc_piwik_domain` : domaine Piwik
* `cpc_piwik_id` : ID Piwik

### SSL (optionnel)

* `cpc_ssl_cert` : chemin distant vers le certificat SSL
* `cpc_ssl_key` : chemin distant vers la clé privée serveur pour le certificat SSL
* `cpc_ssl_chain` : chemin distant vers la chaine complête de certificats SSL

## Instance de développement locale

### Installation

Depuis le répertoire `ansible` exécuter le playbook `local_install.yml` :

```sh
ansible-playbook -i inventories/local -b books/local_install.yml
```

Si vous souhaitez passer des valeurs différentes à certaines variables, ajouter `-e 'variable=valeur'` pour chacune d'entre elles.  Vous pouvez aussi indiquer leurs valeurs dans `ansible/group_vars/local/main.yml`.

Si vous souhaitez charger un dump de prod (home/nosdeputes/nd2012/data/sql/dumps/ sur goya), téléchargez-le localement puis passez son chemin via la variable `cpc_dump` :

```sh
ansible-playbook -i inventories/local -b books/local_install.yml -e 'cpc_dump=/path/to/dump.sql.gz'
```

Vous pouvez alors accéder à :

* CPC depuis http://localhost:8001/ (suivant la variable `cpc_port_web`)
* PHPMyAdmin depuis http://localhost:8002/ (suivant la variable `cpc_port_phpmyadmin`)
* MySQL directement sur localhost:8003 (suivant la variable `cpc_port_mysql`), par exemple avec `mysql -P 8003 --default-character-set=utf8 -u cpc -pcpc cpc` (note: si la machine hôte n'a pas de client MySQL installé, il est aussi possible d'utiliser celui installé sur le container web - voir ci-dessous)

Par défaut, memcache, solr, piwik sont désactivés lors d'une installation locale.  Si nécessaire, définissez les variables correspondantes et relancez le playbook.

### Exécuter un shell ou une commande

Il est possible d'exécuter une commande sur un container directement en utilisant `docker-compose`.  Un alias est automatiquement créé lors de l'installation permettant d'appeler `docker-compose` avec les bons paramètres pour l'instance installée :

```sh
$ # Lancer un shell sur le container web
$ sudo dc-dev_local exec web bash
$ # Lancer le client MySQL depuis le container web
$ sudo dc-dev_local exec web mysql -h database --default-character-set=utf8 -u cpc -pcpc cpc
```

**Note** : remplacer `dev_local` par la valeur de la variable `cpc_instance_name` si elle a été modifiée.

### Accéder au système de fichiers

Le répertoire `/opt/cpc/repo` (suivant la variable `cpc_home`) est monté dans le container web : toute modification dans ce répertoire est directement prise en compte.

### Afficher les logs

```sh
$ # Tous les services
$ sudo dc-dev_local logs
$ # Container web seul
$ sudo dc-dev_local logs web
$ # Options follow/tail 100 lignes
$ sudo dc-dev_local logs -f -t 100 web
```

## Installation distante

### Prérequis

Disposer d'un accès SSH sur la machine avec un user pouvant exécuter n'importe quelle commande en `sudo` (on utilisera `monuser` dans la suite de cette documentation).

### Création d'un inventaire

Créer un fichier d'inventaire `ansible/inventories/monserveur` avec le contenu suivant :

```
[remote]
monserveur ansible_host=monserveur.example.com ansible_user=monuser

[all]
monserveur
```

### Configuration

Créer un fichier de configuration `ansible/host_vars/monserveur/main.yml` sur le modèle ci-dessous.

**Attention** : si vous installez plusieurs instances sur la même machine, tenez compte des variables devant avoir une valeur différente (marquées [U] dans la section « Configuration » ci-dessus).

```yaml
---

variable1: valeur1
variable2: valeur2
```

### Installation

Depuis le répertoire `ansible` exécuter le playbook `remote_install.yml` :

```sh
ansible-playbook -i inventories/monserveur -b books/remote_install.yml
```

### Exécution de commandes

Un alias permettant l'appel de `docker-compose` avec les bons paramètres est automatiquement créé avec le nom de l'instance (la variable `cpc_instance_name`).  Par exemple, pour une instance nommée `nd2017` :

```sh
$ sudo dc-nd2017 exec web php symfony cc
$ sudo dc-nd2017 logs -f -t 100 solr
```
