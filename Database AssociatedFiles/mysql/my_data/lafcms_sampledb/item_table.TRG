TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER `before_insert_item_id` BEFORE INSERT ON `item_table` FOR EACH ROW BEGIN\n	DECLARE next_num int;\n    INSERT INTO item_id_counter VALUES(NULL);\n    SET next_num = LAST_INSERT_ID();\n    SET NEW.item_id = CONCAT(\'ITE\', LPAD((SELECT COUNT(*) FROM item_table) + 1, 4, \'0\'));\nEND'
sql_modes=524288
definers='root@localhost'
client_cs_names='utf8mb4'
connection_cl_names='utf8mb4_general_ci'
db_cl_names='utf8mb4_general_ci'
created=1744771402257547
