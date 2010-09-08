<?php


class TexteLoiTable extends ObjectCommentableTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('TexteLoi');
    }
}