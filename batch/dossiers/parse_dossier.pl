#!/usr/bin/perl

while($l = <STDIN>) {
    foreach $_ (split(/<br>/, $l)) {
	if (/<font[^>]*size="2"[^>]*> *(<a[^>]*> *<\/a> *)?(Assembl|S[^>]*nat)/) {
	    $id = '';
	}
	if (/\/1\d\/(projets|propositions)\/\D+(\d+)\.asp/) {
	    $id = $2;
	}
	if (!/Nomination / && /<a href="(\/1\d\/cr-[^"]*.asp)/)  {
            $cr = "http://www.assemblee-nationale.fr".$1;
        }
        if ($cr && $id) {
            print "$cr;$id\n";
            $cr = '';
        }
    }
}
