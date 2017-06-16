<?php

exec("php symfony print:dumpAmendementsLoi $loi $format", $output, $ret);
if (!$ret) {
	foreach($output as $o){
		print "$o\n";
	}
}else{
	$task->run(array('format' => $format, 'loi_id'=>$loi));
}
