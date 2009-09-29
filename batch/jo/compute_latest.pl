#!/usr/bin/perl

for ($cpt = 0 ; 1 ; $cpt++) {
	@time = localtime(time()-60*60*24*$cpt);
	print "@time \n";
	$year = $time[5];
	$mon = $time[4] + 1;
	$mday = $time[3];
	$year += 1900;
	$file = sprintf('pdf/%04d%02d%02d.pdf', $year, $mon, $mday);
	print $file.(-e $file)."\n";
	if (-e $file) {
		print "break\n";
		exit;
	}
	$ret = system('sh compute_jo.sh '.$mday.' '.$mon.' '.$year);
	if ($ret) {
		print "ret\n";
		exit;
	}
}

