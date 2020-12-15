ALTER TABLE vendor_escalation_log add column agent_id int(11) NOT NULL;
ALTER TABLE vendor_escalation_log add column escalation_source varchar(25) NULL DEFAULT NULL;