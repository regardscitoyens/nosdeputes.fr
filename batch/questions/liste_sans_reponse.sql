SELECT source FROM `question_ecrite` WHERE reponse = "" AND `date` > DATE_SUB(CURDATE(), INTERVAL 6 MONTH)

