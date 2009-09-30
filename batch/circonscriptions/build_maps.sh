#!/bin/sh -e
mkdir -p png svg html
rm -f png/* svg/* html/* cartes.html
xargs -l1  --arg-file=sources/circo.txt ./script/svgedit.py sources/circo.svg
cp sources/circo.svg svg/france.svg
inkscape -e png/france.png svg/france.svg
cd svg
for i in *.svg; do
 ../script/svg2imagemap.py $i 0 0 circonscriptions
 done
mv *.html ../html
cd ..
cat << EOF > cartes.html
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Circonscriptions</title>
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.maphilight.min.js"></script>
	<script>
$.fn.maphilight.defaults = {
	fill: true,
	fillColor: 'ff0000',
	fillOpacity: 0.4,
	stroke: true,
	strokeColor: 'ff0000',
	strokeOpacity: 1,
	strokeWidth: 1,
	fade: false,
	alwaysOn: false
}
	\$(function() {
		\$('.carte_departement').maphilight();
		});

</script>
</head>
<body>
EOF
cat html/*.html|sed 's/\/images\/circonscriptions/png/' >> cartes.html
echo "</body>" >> cartes.html
