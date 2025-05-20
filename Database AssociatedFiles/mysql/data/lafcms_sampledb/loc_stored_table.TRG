TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER `before_insert_loc_stored_table` BEFORE INSERT ON `loc_stored_table` FOR EACH ROW BEGIN\n	DECLARE next_num INT;\n    INSERT INTO loc_stored_id_counter VALUES(NULL);\n    SET next_num = LAST_INSERT_ID();\n    SET NEW.loc_stored_id = CONCAT(\'CZIT-\', next_num + 1099);\nEND'
sql_modes=524288
definers='root@localhost'
client_cs_names='utf8mb4'
connection_cl_names='utf8mb4_general_ci'
db_cl_names='utf8mb4_general_ci'
created=1747376880405524
