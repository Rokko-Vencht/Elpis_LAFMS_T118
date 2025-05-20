TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER `before_insert_loc_found_table` BEFORE INSERT ON `loc_found_table` FOR EACH ROW BEGIN\n	DECLARE next_num INT;\n  	INSERT INTO loc_found_id_counter VALUES(NULL);\n  	SET next_num = LAST_INSERT_ID();\n  	SET NEW.loc_found_id = CONCAT(\'LF00-\', LPAD(next_num, 4, \'0\'));\n    \nEND'
sql_modes=524288
definers='root@localhost'
client_cs_names='utf8mb4'
connection_cl_names='utf8mb4_general_ci'
db_cl_names='utf8mb4_general_ci'
created=1747376880383146
