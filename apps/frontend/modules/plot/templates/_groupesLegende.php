<?php
echo '<p class="legende"><span style=\'background-color: rgb(200,200,200);\'>&nbsp;</span>&nbsp;'.link_to('Non-Inscrits', '@list_parlementaires_groupe?acro=NI').'&nbsp;&nbsp;';
echo '<span style=\'background-color: rgb(30,30,200);\'>&nbsp;</span>&nbsp;'.link_to('UMP,  RPF, RAD, et ratt.', '@list_parlementaires_groupe?acro=UMP').'&nbsp;&nbsp;';
echo '<span style=\'background-color: rgb(30,190,255);\'>&nbsp;</span>&nbsp;'.link_to('Union Centriste (NC, Modem, ...)', '@list_parlementaires_groupe?acro=UC').'<br/>';
echo '<span style=\'background-color: rgb(255,150,150);\'>&nbsp;</span>&nbsp;'.link_to('Socio-Radicaux (PRG, MRC, ...)', '@list_parlementaires_groupe?acro=RDSE').'&nbsp;&nbsp;';
echo '<span style=\'background-color: rgb(255,50,190);\'>&nbsp;</span>&nbsp;'.link_to('Socialistes (PS, Verts et ratt.)', '@list_parlementaires_groupe?acro=SOC').'&nbsp;&nbsp;';
echo '<span style=\'background-color: rgb(255,30,30);\'>&nbsp;</span>&nbsp;'.link_to('PCF, PG et ratt.', '@list_parlementaires_groupe?acro=CRC-SPG').'</p>';
?>
