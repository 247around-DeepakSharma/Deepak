<?php
$db->select('permission_type, role, is_on');
$db->where('partner_id', $db->login_partner_id);
$p = $db->get('partner_permission');
$data = $p->result_array();
$permission = array();
foreach ($data as $value) {
    $permission[$value['permission_type']] = $value;
}
$j = json_encode($permission, TRUE);

define('PERMISSION_CONSTANT', $j);

define('SPARE_REQUESTED_ON_APPROVAL','auto_approve_requested_spare');
define('AUTO_APPROVED_OOW_CHARGES_ON_BEHALF_CUSTOMER','auto_approve_oow_charges_on_behalf_customer');
define('AUTO_PICK_OOW_PART_ESTIMATE','auto_pick_oow_part_estimate');
define('CALLING_FEATURE_IS_ENABLE','c2c_option_enable');
define('PARTNER_ON_SAAS','partner_on_saas');
define('CREATE_AUTO_MICRO_WAREHOUSE','create_auto_micro_warehouse');
define('AUTO_APPROVE_DEFECTIVE_PARTS_COURIER_CHARGES','auto_approve_defective_parts_courier_charges');