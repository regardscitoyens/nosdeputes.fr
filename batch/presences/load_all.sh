#!/bin/bash
mkdir -p json
perl parse_orga_inter.pl http://www.assemblee-nationale.fr/international/francophonie-seminaires-APF.asp "Section française de l'assemblée parlementaire de la francophonie" > json/apf.json
perl parse_orga_inter.pl http://www.assemblee-nationale.fr/13/international/groupes-amitie-activites.asp "Groupes d'amitié" "groupes" > json/grpeamitie.json
split -l 500 json/grpeamitie.json json/grpeamitie_
rm json/grpeamitie.json
perl parse_orga_inter.pl http://www.assemblee-nationale.fr/13/international/uip-activites.asp "Groupe français de l'union interparlementaire" > json/uip.json
