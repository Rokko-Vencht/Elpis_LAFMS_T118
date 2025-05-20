TYPE=TRIGGERS
triggers='CREATE DEFINER=`root`@`localhost` TRIGGER `before_insert_user_info` BEFORE INSERT ON `user_info` FOR EACH ROW BEGIN\n    DECLARE next_num INT;\n\n    -- INSERTING TO COUNTER FOR AUTO INCREMENTATION\n    INSERT INTO user_id_counter VALUES (NULL);\n    SET next_num = LAST_INSERT_ID();\n\n    -- FORMATTING OF THE USER ID\n    SET NEW.user_id = CONCAT(\'TX\', LPAD(next_num, 3, \'0\'), \' X\', LPAD(next_num + 4440, 4, \'0\'));\nEND'
sql_modes=1098907648
definers='root@localhost'
client_cs_names='utf8mb4'
connection_cl_names='utf8mb4_unicode_ci'
db_cl_names='utf8mb4_general_ci'
created=1744771942339509
