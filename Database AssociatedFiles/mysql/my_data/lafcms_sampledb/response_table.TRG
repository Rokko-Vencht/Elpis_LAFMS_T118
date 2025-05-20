TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER `before_insert_response_table` BEFORE INSERT ON `response_table` FOR EACH ROW BEGIN\n	DECLARE next_num INT;\n  	INSERT INTO response_id_counter VALUES(NULL);\n  	SET next_num = LAST_INSERT_ID();\n  	SET NEW.response_id = CONCAT(\'R1012-\', LPAD(next_num - 1, 5, \'0\'));\nEND'
sql_modes=524288
definers='root@localhost'
client_cs_names='utf8mb4'
connection_cl_names='utf8mb4_general_ci'
db_cl_names='utf8mb4_general_ci'
created=1744771402432139
