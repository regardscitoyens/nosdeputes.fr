echo "== Load Présences en Commissions"
echo =======================
while ! php symfony load:JO ; do
    php symfony cc --env=test  --app=frontend > /dev/null
done;
while ls batch/commission/presents | grep [a-z] > /dev/null ; do
    php symfony cc --env=test  --app=frontend > /dev/null
    php symfony load:JO --source=cri
done;
echo == Load Hemicycle
echo =======================
while ls batch/hemicycle/out | grep [a-z] > /dev/null ; do
	php symfony cc --env=test  --app=frontend > /dev/null
	php symfony load:Hemicycle
done;
echo "== Load Commissions : http://www.nosdeputes.fr/backend.php/commissions"
echo =======================
while ls batch/commission/out | grep [a-z] > /dev/null ; do
	php symfony cc --env=test  --app=frontend > /dev/null
	php symfony load:Commission
done
echo "== Load Scrutins"
echo =======================
bash bin/load_scrutins
echo "== Tag Seances"
echo =======================
bash bin/tag_seance
php symfony set:Session
php symfony set:Vacances
echo "== Top Députés"
php symfony top:Deputes
php symfony cc > /dev/null
echo =======================
bash bin/update_hardcache
