SELECT source FROM `question_ecrite` WHERE `reponsei` = "" AND `date` > DATE_SUB(CURDATE(), INTERVAL 75 DAY)

