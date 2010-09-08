
DELETE ta FROM `tagging` ta where taggable_model = "Section" and taggable_id in (784,785,1043,7003,7004);

DELETE co FROM `commentaire_object` co where object_type = "Section" and object_id in (784,785,1043,7003,7004);

DELETE s FROM `section` s where id in (784,785,1043,7003,7004);

DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id where taggable_model = "Section" and t.triple_key = "amendement";

DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id = 4450 and t.triple_value in (1360,1479);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id = 2832 and t.triple_value in (1101);
DELETE ta FROM `tagging` ta join tag t on t.id = ta.tag_id join section s on s.id = ta.taggable_id where taggable_model = "Section" and t.triple_key = "numero" and s.section_id = 2834 and t.triple_value in (1160);
