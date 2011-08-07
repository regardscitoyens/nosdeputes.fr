<?php
echo link_to('non', '@rate_id?object_type='.get_class($object).'&object_id='.$object->id.'&rate=0');
echo ' ';
echo link_to('neutre', '@rate_id?object_type='.get_class($object).'&object_id='.$object->id.'&rate=1');
echo ' ';
echo link_to('oui', '@rate_id?object_type='.get_class($object).'&object_id='.$object->id.'&rate=2');
