<?php


class TagTable extends PluginTagTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Tag');
    }
}