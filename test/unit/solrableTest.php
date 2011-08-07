<?php
 
include(dirname(__FILE__).'/../bootstrap/unit.php');
 
new sfDatabaseManager(ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true));
$t = new lime_test(17);

$s = new SolrConnector();

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
/*
$a = $s->search("oghe object_name:Parlementaire");
$t->is($a['response']['docs'][0]['id'], $id, "Le parlementaire avec des fautes");
*/

$q = new QuestionEcrite();
$q->question = "Ca va après les régionales ?";
$q->reponse = "On pourait aller mieux";
$q->parlementaire_id = 2;
$q->ministere = "Ministere de la crise et du déficit";
$q->save();

$iexists = Doctrine::getTable('Intervention')->find(3);
$iexists->save();

$s->updateFromCommands();
$id = "QuestionEcrite/".$q->id;
$a = $s->search("id:$id");
$t->is(count($a['response']['docs']), 1, "La question a été ajoutée");
$a = $s->search("régionales id:$id");
$t->is($a['response']['docs'][0]['id'], $id, "La question est trouvable");

$a = Doctrine::getTable('Amendement')->find(7);
$a->save();
$s->updateFromCommands();

$id = "Amendement/".$a->id;
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 1, "L'amendement a été ajoutée");
$s->deleteLuceneRecord($id);
$s->updateFromCommands();
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 0, "L'amendement a été supprimé dans lucene");

$id = "Intervention/".$i->id;
$i->delete();
$s->updateFromCommands();
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 0, "L'intervention a été supprimée");

$id = "Parlementaire/".$p->id;
$p->delete();
$s->updateFromCommands();
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 0, "Le parlementaire a été supprimée");

$id = "QuestionEcrite/".$q->id;
$q->delete();
$s->updateFromCommands();
$r = $s->search("id:$id");
$t->is(count($r['response']['docs']), 0, "La question ecrite a été supprimée");

$c = new Commentaire();
$c->commentaire = "commentaire test";
$c->is_public = 0;
$c->object_type = "Parlementaire";
$c->object_id = "1";
$c->citoyen_id = 2;

$c->save();
$s->updateFromCommands();
$id = $c->id;
$r = $s->search("id:Commentaire/$id");
$t->is(count($r['response']['docs']), 0, "Le commentaire n'est pas trouvable car is_public = 0");

$c = Doctrine::getTable('Commentaire')->find($id);
$c->is_public = 1;
$c->save();
$s->updateFromCommands();
$r = $s->search("id:Commentaire/$id");
$t->is(count($r['response']['docs']), 1, "Le commentaire est trouvable car is_public = 1");

$c = Doctrine::getTable('Commentaire')->find($id);
$c->is_public = 0;
$c->save();
$s->updateFromCommands();
$r = $s->search("id:Commentaire/$id");
$t->is(count($r['response']['docs']), 0, "Le commentaire n'est pas trouvable car is_public = 0");

$c->delete();
$s->updateFromCommands();
$r = $s->search("id:Commentaire/$id");
$t->is(count($r['response']['docs']), 0, "Le commentaire n'est pas trouvable car il est supprimé");
