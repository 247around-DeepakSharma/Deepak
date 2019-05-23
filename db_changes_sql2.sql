--Abhyay 20/5/2019
ALTER TABLE `service_center_booking_action` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0' AFTER `sf_purchase_date`;
ALTER TABLE `booking_unit_details` ADD `added_by_sf` INT(1) NOT NULL DEFAULT '0';

--Kajal 23/5/2019 starting --
insert into `partner_permission`(partner_id,permission_type,is_on,create_date,update_date) 
values(247001, 'partner_on_state_appliance',0,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);

insert into agent_filters(entity_type,entity_id,contact_person_id,agent_id,state) 
SELECT '247around',id, 0,account_manager_id,state FROM `partners` where account_manager_id is not NULL;
--Kajal 23/5/2019 ending --
