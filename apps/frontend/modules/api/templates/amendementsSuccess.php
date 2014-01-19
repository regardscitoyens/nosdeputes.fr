<?php

exec("php symfony print:dumpAmendementsLoiCsv $loi $format", $output,$ret);
if (!$ret) {
	foreach($output as $o){
		print $o;
	}
}else{
	$task->run(array('format' => $format, 'loi_id'=>$loi));
}
