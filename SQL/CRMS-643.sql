alter table agent_outbound_call_log ADD COLUMN booking_primary_id int NULL DEFAULT NULL;
alter table agent_outbound_call_log ADD COLUMN recording_url varchar(500) NULL DEFAULT NULL;
alter table agent_outbound_call_log ADD COLUMN call_sid varchar(250) NULL DEFAULT NULL;
alter table agent_outbound_call_log ADD COLUMN status varchar(50) NULL DEFAULT NULL;
alter table agent_outbound_call_log ADD COLUMN start_time datetime NULL DEFAULT NULL;
alter table agent_outbound_call_log ADD COLUMN end_time datetime NULL DEFAULT NULL;

