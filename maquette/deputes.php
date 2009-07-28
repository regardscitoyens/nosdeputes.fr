<?php require 'includes/script.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <title>Les Député par départements</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="index, follow" />
    <meta name="author" content="Collectif Citoyen ?" />
    <meta name="classification" content="Tout public" />
    <meta name="expires" content="never" />
    <meta name="rating" content="general" />
    <meta name="revisit-after" content="7 days" />
    <meta http-equiv="content-language" content="fr" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/<?php echo $style; ?>/style.css" />
    <link rel="stylesheet" type="text/css" media="print" href="css/<?php echo $style; ?>/print.css" />
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" media="screen" href="css/<?php echo $style; ?>/ie6.css" />
    <script type="text/javascript" src="js/<?php echo $style; ?>/fonctions_ie.js"></script>
    <script type="text/javascript" src="includes/iepngfix/iepngfix_tilebg.js"></script>  
    <style type="text/css">
      img, div { behavior: url('includes/iepngfix/iepngfix.php') }
    </style>
    <![endif]-->
    <!--[if gt IE 6]>
    <script type="text/javascript" src="js/<?php echo $style; ?>/fonctions_ie.js"></script>
    <![endif]-->
    <!--[if !IE]><-->
    <script type="text/javascript" src="js/<?php echo $style; ?>/fonctions.js"></script>
    <!--><![endif]-->
  </head>
  <body>
    <?php include 'includes/haut.php'; ?>
    <div class="corps">
      <div class="contenu" id="contenu">
        <div class="conteneur_resultat_dep">
          <h1>La carte de Députés</h1>
          <div class="resultat_dep" id="resultat_dep">
            <p>Cliquez sur votre département (au hasard 86 Vienne ;)</p>
          </div>
        </div>
        <div class="image_map">
          <img src="images/carte-de-france.png" alt="Départements français" usemap="#france" />
          <map name="france" id="france"> <!-- il manque l'atribut alt pour chaque "area" comme la 1ère ligne sinon c'est pas valide W3C -->
            <area shape="rect" coords="396, 227, 415, 248" href="javascript:affiche_dep('001');" alt="01 Ain" />
            <area shape="rect" coords="350, 102, 369, 136" href="javascript:affiche_dep('002');" />
            <area shape="rect" coords="337, 219, 365, 237" href="javascript:affiche_dep('003');" />
            <area shape="rect" coords="419, 295, 443, 316" href="javascript:affiche_dep('004');" />
            <area shape="rect" coords="419, 276, 442, 293" href="javascript:affiche_dep('005');" />
            <area shape="rect" coords="445, 299, 466, 319" href="javascript:affiche_dep('006');" />
            <area shape="rect" coords="373, 279, 393, 298" href="javascript:affiche_dep('007');" />
            <area shape="rect" coords="372, 102, 393, 124" href="javascript:affiche_dep('008');" />
            <area shape="rect" coords="303, 345, 325, 362" href="javascript:affiche_dep('009');" />
            <area shape="rect" coords="363, 150, 385, 173" href="javascript:affiche_dep('010');" />
            <area shape="rect" coords="328, 336, 348, 353" href="javascript:affiche_dep('011');" />
            <area shape="rect" coords="330, 288, 352, 309" href="javascript:affiche_dep('012');" />
            <area shape="rect" coords="388, 314, 404, 337" href="javascript:affiche_dep('013');" />
            <area shape="rect" coords="258, 124, 283, 142" href="javascript:affiche_dep('014');" />
            <area shape="rect" coords="332, 264, 351, 283" href="javascript:affiche_dep('015');" />
            <area shape="rect" coords="273, 240, 291, 263" href="javascript:affiche_dep('016');" />
            <area shape="rect" coords="250, 235, 270, 265" href="javascript:affiche_dep('017');" />
            <area shape="rect" coords="327, 191, 349, 219" href="javascript:affiche_dep('018');" />
            <area shape="rect" coords="311, 258, 329, 278" href="javascript:affiche_dep('019');" />
            <area shape="rect" coords="465, 313, 483, 340" href="javascript:affiche_dep('02b');" />
            <area shape="rect" coords="459, 342, 480, 363" href="javascript:affiche_dep('02a');" />
            <area shape="rect" coords="377, 176, 397, 206" href="javascript:affiche_dep('021');" />
            <area shape="rect" coords="199, 149, 229, 164" href="javascript:affiche_dep('022');" />
            <area shape="rect" coords="316, 231, 335, 250" href="javascript:affiche_dep('023');" />
            <area shape="rect" coords="283, 261, 305, 286" href="javascript:affiche_dep('024');" />
            <area shape="rect" coords="417, 189, 438, 207" href="javascript:affiche_dep('025');" />
            <area shape="rect" coords="396, 275, 412, 298" href="javascript:affiche_dep('026');" />
            <area shape="rect" coords="289, 125, 313, 145" href="javascript:affiche_dep('027');" />
            <area shape="rect" coords="299, 146, 320, 169" href="javascript:affiche_dep('028');" />
            <area shape="rect" coords="170, 148, 194, 178" href="javascript:affiche_dep('029');" />
            <area shape="rect" coords="376, 305, 392, 322" href="javascript:affiche_dep('030');" />
            <area shape="rect" coords="304, 322, 317, 343" href="javascript:affiche_dep('031');" />
            <area shape="rect" coords="274, 313, 298, 334" href="javascript:affiche_dep('032');" />
            <area shape="rect" coords="250, 274, 276, 299" href="javascript:affiche_dep('033');" />
            <area shape="rect" coords="350, 318, 376, 335" href="javascript:affiche_dep('034');" />
            <area shape="rect" coords="228, 152, 250, 181" href="javascript:affiche_dep('035');" />
            <area shape="rect" coords="303, 202, 326, 229" href="javascript:affiche_dep('036');" />
            <area shape="rect" coords="284, 190, 303, 208" href="javascript:affiche_dep('037');" />
            <area shape="rect" coords="402, 257, 427, 275" href="javascript:affiche_dep('038');" />
            <area shape="rect" coords="403, 200, 420, 231" href="javascript:affiche_dep('039');" />
            <area shape="rect" coords="246, 299, 266, 330" href="javascript:affiche_dep('040');" />
            <area shape="rect" coords="305, 181, 323, 199" href="javascript:affiche_dep('041');" />
            <area shape="rect" coords="365, 232, 383, 257" href="javascript:affiche_dep('042');" />
            <area shape="rect" coords="359, 263, 379, 280" href="javascript:affiche_dep('043');" />
            <area shape="rect" coords="229, 182, 249, 208" href="javascript:affiche_dep('044');" />
            <area shape="rect" coords="320, 166, 344, 184" href="javascript:affiche_dep('045');" />
            <area shape="rect" coords="307, 280, 324, 303" href="javascript:affiche_dep('046');" />
            <area shape="rect" coords="278, 290, 299, 312" href="javascript:affiche_dep('047');" />
            <area shape="rect" coords="355, 283, 373, 306" href="javascript:affiche_dep('048');" />
            <area shape="rect" coords="250, 182, 279, 207" href="javascript:affiche_dep('049');" />
            <area shape="rect" coords="241, 117, 257, 154" href="javascript:affiche_dep('050');" />
            <area shape="rect" coords="364, 126, 391, 149" href="javascript:affiche_dep('051');" />
            <area shape="rect" coords="389, 155, 410, 180" href="javascript:affiche_dep('052');" />
            <area shape="rect" coords="252, 156, 272, 181" href="javascript:affiche_dep('053');" />
            <area shape="rect" coords="414, 138, 427, 153" href="javascript:affiche_dep('054');" />
            <area shape="rect" coords="390, 118, 410, 151" href="javascript:affiche_dep('055');" />
            <area shape="rect" coords="201, 168, 221, 187" href="javascript:affiche_dep('056');" />
            <area shape="rect" coords="421, 123, 440, 143" href="javascript:affiche_dep('057');" />
            <area shape="rect" coords="354, 193, 373, 216" href="javascript:affiche_dep('058');" />
            <area shape="rect" coords="351, 83, 373, 101" href="javascript:affiche_dep('059');" />
            <area shape="rect" coords="321, 115, 346, 133" href="javascript:affiche_dep('060');" />
            <area shape="rect" coords="266, 142, 294, 158" href="javascript:affiche_dep('061');" />
            <area shape="rect" coords="313, 75, 340, 92" href="javascript:affiche_dep('062');" />
            <area shape="rect" coords="339, 240, 364, 263" href="javascript:affiche_dep('063');" />
            <area shape="rect" coords="245, 330, 270, 351" href="javascript:affiche_dep('064');" />
            <area shape="rect" coords="271, 338, 288, 355" href="javascript:affiche_dep('065');" />
            <area shape="rect" coords="330, 354, 357, 369" href="javascript:affiche_dep('066');" />
            <area shape="rect" coords="445, 129, 462, 154" href="javascript:affiche_dep('067');" />
            <area shape="rect" coords="440, 160, 454, 185" href="javascript:affiche_dep('068');" />
            <area shape="rect" coords="381, 229, 391, 255" href="javascript:affiche_dep('069');" />
            <area shape="rect" coords="410, 173, 433, 190" href="javascript:affiche_dep('070');" />
            <area shape="rect" coords="371, 206, 402, 228" href="javascript:affiche_dep('071');" />
            <area shape="rect" coords="275, 163, 300, 185" href="javascript:affiche_dep('072');" />
            <area shape="rect" coords="428, 248, 447, 268" href="javascript:affiche_dep('073');" />
            <area shape="rect" coords="421, 226, 442, 246" href="javascript:affiche_dep('074');" />
            <area shape="rect" coords="463, 57, 486, 67" href="javascript:affiche_dep('075');" />
            <area shape="rect" coords="289, 106, 315, 123" href="javascript:affiche_dep('076');" />
            <area shape="rect" coords="340, 139, 358, 160" href="javascript:affiche_dep('077');" />
            <area shape="rect" coords="404, 48, 427, 72" href="javascript:affiche_dep('078');" />
            <area shape="rect" coords="262, 208, 278, 238" href="javascript:affiche_dep('079');" />
            <area shape="rect" coords="319, 97, 345, 114" href="javascript:affiche_dep('080');" />
            <area shape="rect" coords="318, 310, 340, 332" href="javascript:affiche_dep('081');" />
            <area shape="rect" coords="299, 300, 315, 321" href="javascript:affiche_dep('082');" />
            <area shape="rect" coords="418, 319, 446, 339" href="javascript:affiche_dep('083');" />
            <area shape="rect" coords="399, 300, 412, 315" href="javascript:affiche_dep('084');" />
            <area shape="rect" coords="233, 209, 260, 230" href="javascript:affiche_dep('085');" />
            <area shape="rect" coords="279, 213, 300, 235" href="javascript:affiche_dep('086');" />
            <area shape="rect" coords="299, 233, 315, 259" href="javascript:affiche_dep('087');" />
            <area shape="rect" coords="412, 155, 441, 171" href="javascript:affiche_dep('088');" />
            <area shape="rect" coords="352,171,396,191" href="javascript:affiche_dep('089');" />
            <area shape="rect" coords="352, 169, 374, 192" href="javascript:affiche_dep('089');" />
            <area shape="rect" coords="441, 185, 463, 203" href="javascript:affiche_dep('090');" />
            <area shape="rect" coords="429, 71, 447, 98" href="javascript:affiche_dep('091');" />
            <area shape="rect" coords="464, 78, 487, 94" href="javascript:affiche_dep('092');" />
            <area shape="rect" coords="465, 43, 487, 56" href="javascript:affiche_dep('093');" />
            <area shape="rect" coords="468, 69, 487, 81" href="javascript:affiche_dep('094');" />
            <area shape="rect" coords="422, 32, 441, 47" href="javascript:affiche_dep('095');" />
            <area shape="rect" coords="74, 72, 153, 118" href="javascript:affiche_dep('972');" />
            <area shape="rect" coords="74, 130, 123, 168" href="javascript:affiche_dep('973');" />
            <area shape="rect" coords="74, 184, 129, 216" href="javascript:affiche_dep('974');" />
            <area shape="rect" coords="77, 28, 153, 63" href="javascript:affiche_dep('971');" />
            <area shape="rect" coords="73,372,146,402" href="javascript:affiche_dep('988');" />
            <area shape="rect" coords="74, 314, 139, 352" href="javascript:affiche_dep('987');" />
            <area shape="rect" coords="74,432,176,451" href="javascript:affiche_dep('98601');" />
            <area shape="rect" coords="80, 233, 134, 253" href="javascript:affiche_dep('97601');" />
        <area shape="rect" coords="74, 259, 160, 289" href="javascript:affiche_dep('97501');" />
        </map>
      </div>
    <table width="100%">
      <tr class="dep">
        <td width="25%" valign="top">  
        <p>
          <a href="javascript:affiche_dep('001');">01 Ain</a><br />
          <a href="javascript:affiche_dep('002');">02 Aisne</a><br />
          <a href="javascript:affiche_dep('003');">03 Allier</a><br />
          <a href="javascript:affiche_dep('004');">04 Alpes-de-Haute-Provence</a><br />
          <a href="javascript:affiche_dep('005');">05 Hautes-Alpes</a><br />
          <a href="javascript:affiche_dep('006');">06 Alpes-Maritimes</a><br />
          <a href="javascript:affiche_dep('007');">07 Ard&egrave;che</a><br />
          <a href="javascript:affiche_dep('008');">08 Ardennes</a><br />
          <a href="javascript:affiche_dep('009');">09 Ari&egrave;ge</a><br />
          <a href="javascript:affiche_dep('010');">10 Aube</a><br />
          <a href="javascript:affiche_dep('011');">11 Aude</a><br />
          <a href="javascript:affiche_dep('012');">12 Aveyron</a><br />
          <a href="javascript:affiche_dep('013');">13 Bouches-du-Rhône</a><br />
          <a href="javascript:affiche_dep('014');">14 Calvados</a><br />
          <a href="javascript:affiche_dep('015');">15 Cantal</a><br />
          <a href="javascript:affiche_dep('016');">16 Charente</a><br />
          <a href="javascript:affiche_dep('017');">17 Charente-Maritime</a><br />
          <a href="javascript:affiche_dep('018');">18 Cher</a><br />
          <a href="javascript:affiche_dep('019');">19 Corr&egrave;ze</a><br />
          <a href="javascript:affiche_dep('02a');">2A Corse-du-Sud</a><br />
          <a href="javascript:affiche_dep('02b');">2B Haute-Corse</a><br />
          <a href="javascript:affiche_dep('021');">21 C&ocirc;te-d'Or</a><br />
          <a href="javascript:affiche_dep('022');">22 C&ocirc;tes-d'Armor</a><br />
          <a href="javascript:affiche_dep('023');">23 Creuse</a><br />
          <a href="javascript:affiche_dep('024');">24 Dordogne</a><br />
          <a href="javascript:affiche_dep('025');">25 Doubs</a>
        </p>
        </td>
        <td width="24%" valign="top">
        <p>
          <a href="javascript:affiche_dep('026');">26 Dr&ocirc;me</a><br />
          <a href="javascript:affiche_dep('027');">27 Eure</a><br />
          <a href="javascript:affiche_dep('028');">28 Eure-et-Loir</a><br />
          <a href="javascript:affiche_dep('029');">29 Finist&egrave;re</a><br />
          <a href="javascript:affiche_dep('030');">30 Gard</a><br />
          <a href="javascript:affiche_dep('031');">31 Haute-Garonne</a><br />
          <a href="javascript:affiche_dep('032');">32 Gers</a><br />
          <a href="javascript:affiche_dep('033');">33 Gironde</a><br />
          <a href="javascript:affiche_dep('034');">34 H&eacute;rault</a><br />
          <a href="javascript:affiche_dep('035');">35 Ille-et-Vilaine</a><br />
          <a href="javascript:affiche_dep('036');">36 Indre</a><br />
          <a href="javascript:affiche_dep('037');">37 Indre-et-Loire</a><br />
          <a href="javascript:affiche_dep('038');">38 Is&egrave;re</a><br />
          <a href="javascript:affiche_dep('039');">39 Jura</a><br />
          <a href="javascript:affiche_dep('040');">40 Landes</a><br />
          <a href="javascript:affiche_dep('041');">41 Loir-et-Cher</a><br />
          <a href="javascript:affiche_dep('042');">42 Loire</a><br />
          <a href="javascript:affiche_dep('043');">43 Haute-Loire</a><br />
          <a href="javascript:affiche_dep('044');">44 Loire-Atlantique</a><br />
          <a href="javascript:affiche_dep('045');">45 Loiret</a><br />
          <a href="javascript:affiche_dep('046');">46 Lot</a><br />
          <a href="javascript:affiche_dep('047');">47 Lot-et-Garonne</a><br />
          <a href="javascript:affiche_dep('048');">48 Loz&egrave;re</a><br />
          <a href="javascript:affiche_dep('049');">49 Maine-et-Loire</a><br />
          <a href="javascript:affiche_dep('050');">50 Manche</a><br />
          <a href="javascript:affiche_dep('051');">51 Marne</a><br />
        </p>
        </td>
        <td width="24%" valign="top">
        <p>
          <a href="javascript:affiche_dep('052');">52 Haute-Marne</a><br />
          <a href="javascript:affiche_dep('053');">53 Mayenne</a><br />
          <a href="javascript:affiche_dep('054');">54 Meurthe-et-Moselle</a><br />
          <a href="javascript:affiche_dep('055');">55 Meuse</a><br />
          <a href="javascript:affiche_dep('056');">56 Morbihan</a><br />
          <a href="javascript:affiche_dep('057');">57 Moselle</a><br />
          <a href="javascript:affiche_dep('058');">58 Ni&egrave;vre</a><br />
          <a href="javascript:affiche_dep('059');">59 Nord</a><br />
          <a href="javascript:affiche_dep('060');">60 Oise</a><br />
          <a href="javascript:affiche_dep('061');">61 Orne</a><br />
          <a href="javascript:affiche_dep('062');">62 Pas-de-Calais</a><br />
          <a href="javascript:affiche_dep('063');">63 Puy-de-D&ocirc;me</a><br />
          <a href="javascript:affiche_dep('064');">64 Pyrénées-Atlantiques</a><br />
          <a href="javascript:affiche_dep('065');">65 Hautes-Pyrénées</a><br />
          <a href="javascript:affiche_dep('066');">66 Pyrénées-Orientales</a><br />
          <a href="javascript:affiche_dep('067');">67 Bas-Rhin</a><br />
          <a href="javascript:affiche_dep('068');">68 Haut-Rhin</a><br />
          <a href="javascript:affiche_dep('069');">69 Rh&ocirc;ne</a><br />
          <a href="javascript:affiche_dep('070');">70 Haute-Sa&ocirc;ne</a><br />
          <a href="javascript:affiche_dep('071');">71 Sa&ocirc;ne-et-Loire</a><br />
          <a href="javascript:affiche_dep('072');">72 Sarthe</a><br />
          <a href="javascript:affiche_dep('073');">73 Savoie</a><br />
          <a href="javascript:affiche_dep('074');">74 Haute-Savoie</a><br />
          <a href="javascript:affiche_dep('075');">75 Paris</a><br />
          <a href="javascript:affiche_dep('076');">76 Seine-Maritime</a><br />
          <a href="javascript:affiche_dep('077');">77 Seine-et-Marne</a><br />
        </p>
        </td>
        <td width="27%" valign="top">
        <p>
          <a href="javascript:affiche_dep('078');">78 Yvelines</a><br />
          <a href="javascript:affiche_dep('079');">79 Deux-S&egrave;vres</a><br />
          <a href="javascript:affiche_dep('080');">80 Somme</a><br />
          <a href="javascript:affiche_dep('081');">81 Tarn</a><br />
          <a href="javascript:affiche_dep('082');">82 Tarn-et-Garonne</a><br />
          <a href="javascript:affiche_dep('083');">83 Var</a><br />
          <a href="javascript:affiche_dep('084');">84 Vaucluse</a><br />
          <a href="javascript:affiche_dep('085');">85 Vendée</a><br />
          <a href="javascript:affiche_dep('086');">86 Vienne</a><br />
          <a href="javascript:affiche_dep('087');">87 Haute-Vienne</a><br />
          <a href="javascript:affiche_dep('088');">88 Vosges</a><br />
          <a href="javascript:affiche_dep('089');">89 Yonne</a><br />
          <a href="javascript:affiche_dep('090');">90 Territoire de Belfort</a><br />
          <a href="javascript:affiche_dep('091');">91 Essonne</a><br />
          <a href="javascript:affiche_dep('092');">92 Hauts-de-Seine</a><br />
          <a href="javascript:affiche_dep('093');">93 Seine-Saint-Denis</a><br />
          <a href="javascript:affiche_dep('094');">94 Val-de-Marne</a><br />
          <a href="javascript:affiche_dep('095');">95 Val-d'Oise</a><br />
          <a href="javascript:affiche_dep('971');">971 Guadeloupe</a><br />
          <a href="javascript:affiche_dep('972');">972 Martinique</a><br />
          <a href="javascript:affiche_dep('973');">973 Guyane</a><br />
          <a href="javascript:affiche_dep('974');">974 Réunion</a><br />
          <a href="javascript:affiche_dep('97501');">975 Saint-Pierre-et-Miquelon</a><br />
          <a href="javascript:affiche_dep('97601');">976 Mayotte</a><br />
          <a href="javascript:affiche_dep('98601');">986 Wallis et Futuna</a><br />
          <a href="javascript:affiche_dep('987');">987 Polynésie française</a><br />
          <a href="javascript:affiche_dep('988');">988 Nouvelle-Calédonie</a>
        </p>
        </td>
      </tr>
    </table>
      </div>
    </div>
    <?php include 'includes/bas.php'; ?>
  </body>
</html>