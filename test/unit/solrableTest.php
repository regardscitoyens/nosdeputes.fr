<?php
 
include(dirname(__FILE__).'/../bootstrap/unit.php');
 
new sfDatabaseManager(ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true));
$t = new lime_test(14, new lime_output_color());

/* MaJ Rapide pour test sur intervention courtes
foreach(array('303216', '42266', '191828', '303217', '42284', '110633', '111801', '148718', '152067', '168027', '247354', '259956', '348273', '213423', '87153') as $id) {
   Doctrine::getTable('Intervention')->find($id)->save();
}
*/

$s = new SolrConnector();
//$s->deleteAll();

$i = new Intervention();
$inter = "bonjour les amis comment allez vous ?";
$i->intervention = $inter;
$i->md5 = md5($inter.rand());
$i->parlementaire_id = 1;
$i->date = "2009-10-10";
$i->addTag('loi:1987');
$i->save();
$s->updateFromCommands();
$id = "Intervention/".$i->id;
$a = $s->search("id:$id");

$t->is(count($a['response']['docs']), 1, "L'intervention a été ajoutée");
$a = $s->search("bonjour id:$id");
$t->is($a['response']['docs'][0]['id'], $id, "L'intervention est trouvable");

$a = $s->search("salut id:$id");
$t->is(count($a['response']['docs']), 0, "L'intervention n'est pas retournée sur des mots non indexé");

$i->intervention = $inter." salut";
$i->save();
$s->updateFromCommands();
$a = $s->search("salut id:$id");
print_r($a);
$t->is($a['response']['docs'][0]['id'], $id, "L'intervention retournée sur des mots reindexés");

$p = new Parlementaire();
$p->nom = "Benjamin Ooghe";
$p->save();
$s->updateFromCommands();
$id = "Parlementaire/".$p->id;
$a = $s->search("id:$id");
$t->is(count($a['response']['docs']), 1, "Le parlementaire a été ajouté");
$a = $s->search("ooghe id:$id");
$t->is($a['response']['docs'][0]['id'], $id, "Le parlementaire est trouvable");
$a = $s->search("oogue object_name:Parlementaire");
$t->is($a['response']['docs'][0]['id'], $id, "Le parlementaire avec des fautes");

$q = new QuestionEcrite();
$q->question = "Ca va après les régionales ?";
$q->reponse = "On pourait aller mieux";
$q->parlementaire_id = 2;
$q->ministere = "Ministere de la crise et du déficit";
$q->save();
$s->updateFromCommands();
$id = "QuestionEcrite/".$q->id;
$a = $s->search("id:$id");
$t->is(count($a['response']['docs']), 1, "La question a été ajoutée");
$a = $s->search("régionales id:$id");
$t->is($a['response']['docs'][0]['id'], $id, "La question est trouvable");


$a = Doctrine::getTable('Amendement')->find(2);
$a->save();
$s->updateFromCommands();

$id = "Amendement/".$a->id;
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 1, "L'amendement a été ajoutée");
$s->deleteLuceneRecord($a);
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 0, "L'amendement a été supprimé dans lucene");

$id = "Intervention/".$i->id;
$i->delete();
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 0, "L'intervention a été supprimée");

$id = "Parlementaire/".$p->id;
$p->delete();
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 0, "Le parlementaire a été supprimée");

$id = "QuestionEcrite/".$q->id;
$q->delete();
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 0, "La question ecrite a été supprimée");

