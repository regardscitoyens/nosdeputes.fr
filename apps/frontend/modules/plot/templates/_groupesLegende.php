<?php
echo '<p style="text-align: center;"><span style=\'background-color: rgb(200,200,200);\'>&nbsp;</span>&nbsp;'.link_to('Non-Inscrits (Modem, div.)', '@list_parlementaires_groupe?acro=NI').'&nbsp;&nbsp;';
echo '<span style=\'background-color: rgb(30,30,200);\'>&nbsp;</span>&nbsp;'.link_to('UMP et ratt.', '@list_parlementaires_groupe?acro=UMP').'&nbsp;&nbsp;';
echo '<span style=\'background-color: rgb(30,190,255);\'>&nbsp;</span>&nbsp;'.link_to('Nouveau Centre', '@list_parlementaires_groupe?acro=NC').'<br/>';
echo '<span style=\'background-color: rgb(255,50,190);\'>&nbsp;</span>&nbsp;'.link_to('Socialistes (PS, MRC, PRG et ratt)', '@list_parlementaires_groupe?acro=SRC').'&nbsp;&nbsp;';
echo '<span style=\'background-color: rgb(255,30,30);\'>&nbsp;</span>&nbsp;'.link_to('PCF, Verts et ratt', '@list_parlementaires_groupe?acro=GDR').'</p>';
?>
