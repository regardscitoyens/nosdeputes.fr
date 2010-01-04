<?php

class setCommissionsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'Commissions';
    $this->briefDescription = 'print correspondances commissions';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $option = Doctrine::getTable('VariableGlobale')->findOneByChamp('commissions');

    $commissions = array(

"comité d'évaluation et de contrôle, des politiques publiques" => "comité d’évaluation et de contrôle des politiques publiques",
"commission chargée des affaires européennes" => "commission des affaires européennes",
"commission des affaires culturelles et de l’éducation" => "commission des affaires culturelles et de l'éducation",
"commission des affaires économiques" => "commission des affaires économiques, de l’environnement et du territoire",
"commission des affaires culturelles, familiales et sociales" => "commission des affaires culturelles et de l'éducation",
"commission des affairesétrangères" => "commission des affaires étrangères",
"commission des finances, de l’économie générale et du contrôle budgétaire" => "commission des finances, du contrôle budgétaire et des comptes économiques de la nation",
"commission des finances, de l’économie générale et du plan" => "commission des finances, du contrôle budgétaire et des comptes économiques de la nation",
"commission des lois constitutionnelles, de la législation et de l’administration générale de la république" => "commission des lois constitutionnelles, de législation, du suffrage universel, du règlement et d’administration générale",
"commission spéciale chargée de vérifier et d’apurer les comptes" => "commission spéciale chargée de vérifier et d’apurer les comptes de la nation",
"commission spéciale chargée d’examiner le projet de loi organique relatif à la nomination des présidents des sociétés de l’audiovisuel public et le projet de loi sur le service public de la télévision" => "commission spéciale chargée d’examiner le projet de loi organique relatif à la nomination des présidents des sociétés france télévisions, radio france et de la société en charge de l’audiovisuel extérieur de la france et le projet de loi",
"délégation aux droits des femmes et à l’égalité des chances entre les hommes et les femmes" => "délégation aux droits des femmes et l’égalité des chances entre les hommes et les femmes"

    );


    $option->setValue(serialize($commissions));
    $option->save();
    print_r(unserialize($option->getValue()));
  }
}

