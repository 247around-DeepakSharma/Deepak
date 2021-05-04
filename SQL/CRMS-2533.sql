CREATE TABLE warranty_plan_serial_number_mapping (
id int(11) NOT NULL AUTO_INCREMENT,
plan_id int(11) NOT NULL,
serial_number varchar(512) NOT NULL,
is_active tinyint(1) NOT NULL DEFAULT 1,
create_date timestamp NOT NULL DEFAULT current_timestamp(),
created_by int(25) NOT NULL,
PRIMARY KEY (id),
UNIQUE KEY uk_serial_plan (plan_id,serial_number),
CONSTRAINT fk_wpsnm_plan_id_warranty_plan_plan_id FOREIGN KEY (plan_id) REFERENCES warranty_plans (plan_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
