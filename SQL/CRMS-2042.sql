ALTER TABLE custom_report_queries add column date_filter tinyint NOT NULL DEFAULT 0;
ALTER TABLE custom_report_queries add column department varchar(256) NOT NULL;