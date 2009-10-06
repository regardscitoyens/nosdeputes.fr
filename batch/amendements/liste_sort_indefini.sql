SELECT source FROM `amendement` WHERE `sort` LIKE "Ind%" AND `date` > DATE_SUB(CURDATE() , INTERVAL 1 YEAR)

