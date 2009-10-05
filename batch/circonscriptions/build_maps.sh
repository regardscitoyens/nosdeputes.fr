#!/bin/sh -e
mkdir -p png svg html
rm -f png/* svg/* html/* cartes.html
echo "Génération des svg et png par département"
xargs -l1  --arg-file=sources/circo.txt ./script/svgedit.py sources/circo.svg
echo "Image de la carte de France"
cp sources/circo.svg svg/france.svg
inkscape -w 900 -h 990 -e png/france.png svg/france.svg
echo "Carte de France par couleur politique"
script/colors.py svg/france
inkscape -w 900 -h 900 -e png/france-colors.png svg/france-colors.svg
cd svg
for i in *.svg; do
 echo "Calcul de l'image map pour" $i
 if [ $i = "france.svg" ] || [ $i = "france-colors.svg" ]
 then
 ../script/svg2imagemap.py $i 900 990 1 circonscriptions
 else
 ../script/svg2imagemap.py $i 0 0 1.5 circonscriptions
 fi
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

