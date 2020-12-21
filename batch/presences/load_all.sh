#!/bin/bash

source ../../bin/db.inc
mkdir -p json
perl parse_orga_inter.pl "http://www2.assemblee-nationale.fr/layout/set/ajax/content/view/ajax/42953/(titre)/true/(ajax)/true" "Section française de l'assemblée parlementaire de la francophonie" > json/apf.json
#perl parse_orga_inter.pl http://www.assemblee-nationale.fr/$LEGISLATURE/international/groupes-amitie-activites.asp "Groupes d'amitié" "groupes" > json/grpeamitie.json
#split -l 500 json/grpeamitie.json json/grpeamitie_
#rm json/grpeamitie.json
#perl parse_orga_inter.pl http://www.assemblee-nationale.fr/$LEGISLATURE/international/uip-activites.asp "Groupe français de l'union interparlementaire" > json/uip.json
#perl parse_orga_inter.pl http://www.assemblee-nationale.fr/international/conseil-europe/activites.asp "Délégation française au Conseil de l'Europe" > json/conseil-europe.json
