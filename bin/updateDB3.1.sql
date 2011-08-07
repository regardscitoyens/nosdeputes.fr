
UPDATE intervention SET section_id = 1 WHERE id IN (395528,395529,395530);
UPDATE intervention SET section_id = 1 WHERE id IN (49344,49345);
UPDATE intervention SET section_id = 1 WHERE id IN (21725,21726);
UPDATE intervention SET section_id = 1 WHERE id IN (161761,161762,161763);
UPDATE intervention SET section_id = 1 WHERE id IN (457894,457895,457896);
UPDATE intervention SET section_id = 1 WHERE id IN (436326,436327,436328);
UPDATE intervention SET section_id = 1 WHERE id IN (404257,404258,404259);


DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 9;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (101,103,105);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 178;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (2223, 2225, 2227, 2229, 2231, 2233, 2235, 2237, 2239, 2241, 2243, 2245, 2247, 2251);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 227;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (2832, 2834, 2836, 2838, 2840, 2842);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 492;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (5868, 5870, 5872, 5874, 5876);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 2641;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (7095, 7097, 7099, 7101);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 3454;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (9569, 9575);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 484;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (5773);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 514;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (6048, 6056);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 4009;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (11285);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join intervention i on i.id = ta.taggable_id where taggable_model = "Intervention" and t.triple_key = "numero" and i.seance_id = 206;
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (2626);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id in (11343,11349);


