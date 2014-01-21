<?php

exec("php symfony print:dumpAmendementsLoiCsv $loi $format", $output,$ret);
if (!$ret) {
        foreach($output as $o){
                print "$o\n";
        }
}else{
       	echo "call to task failed: $ret";
}


