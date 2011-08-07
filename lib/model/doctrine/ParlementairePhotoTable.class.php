<?php


class ParlementairePhotoTable extends Doctrine_Table
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('ParlementairePhoto');
    }

    public function findOrCreate($id, $slug) {
      $p = $this->find($id);
      if (!$p) {
	$p = new ParlementairePhoto();
	$p->id = $id;
	$p->slug = $slug;
      }
      return $p;
    }
}