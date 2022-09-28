# Scrappers
Ce dossier `batch` rassemble l'ensemble des scripts (perl et python) qui servent à scrapper les données nécessaires au site NosDéputés.fr.

## Orchestration

1. Le script d'entrée est le fichier [bin/loadupdate](https://github.com/regardscitoyens/nosdeputes.fr/blob/master/bin/loadupdate) qui est joué quotidiennement.

2. Pour chaque type d'objet (amendements, commissions, députés ...), appelle le fichier `compute_latest.sh` ([exemple](https://github.com/regardscitoyens/nosdeputes.fr/blob/master/batch/commission/compute_latest.sh)).

3. Ce script `compute_latest`, commence par télécharger les pages HTML ([exemple](https://github.com/regardscitoyens/nosdeputes.fr/blob/master/batch/commission/download_commission.pl)) et les stocke dans un dossier `out`. Puis appelle le script de parsing ([exemple](https://github.com/regardscitoyens/nosdeputes.fr/blob/master/batch/commission/parse_commission.py)) pour transformer le HTML en JSON. Ces JSON sont déposés dans un dossier dédié `json`.

4. Enfin un script PHP va lister les fichiers du dossier `json` et les charger en base ([exemple](https://github.com/regardscitoyens/nosdeputes.fr/blob/master/lib/task/loadCommissionTask.class.php)). Le fichier JSON importé est alors déplacé dans le dossier `loaded`.

Idéalement,  à la fin du chargement en base, le répertoire json/ est vide et tous les fichiers sont bougés dans loaded/, jusqu'au prochain cycle de chargement.


## Errors

Les erreurs sont disponibles ici [https://scraping.nosdeputes.fr/logs/?C=N;O=D](https://scraping.nosdeputes.fr/logs/?C=N;O=D).

En pratique beaucoup d'erreur sur les formats de date.

La solution la plus simple est d'aller modifier *sur le serveur* le html téléchargé pour corriger les typos (ex "dé cembre" -> "décembre") et refaire tourner le parser.