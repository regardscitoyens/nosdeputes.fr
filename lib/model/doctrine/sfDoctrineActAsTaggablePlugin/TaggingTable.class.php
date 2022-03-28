<?php


class TaggingTable extends PluginTaggingTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Tagging');
    }
}