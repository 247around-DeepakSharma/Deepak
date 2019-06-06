<?php

class Apis extends CI_Model {

    private $db_location;

    /**
     * @desc load both db
     */
    function __construct() {
        parent::__Construct();
        
        $this->db = $this->load->database('default', TRUE, TRUE);
    }

    function saveRequestData($headerData) {
        $this->db->insert("all_requests", $headerData);
    }

    function saveDeviceinfo($deviceInfo, $email, $device_id) {
        $deviceInfoArray = array();
        $deviceInfoArray['user_email'] = $email;
        $deviceInfoArray['device_id'] = $device_id;
        $deviceInfoArray['update_time'] = date("Y-m-d H:i:s");
        $deviceInfoArray['device_info'] = $deviceInfo;
        $this->db->insert("deviceinfo", $deviceInfoArray);
    }

    public function updateVerificationCode($phone_number, $name, $email, $deviceId, $salt, $install_source, $account_email, $existing_flags, $app_version) {
        log_message('info', __METHOD__);

        $updateData = array('name' => $name, 'user_email' => $email, 'verify_code' => $salt,
            'install_source' => $install_source, 'account_email' => $account_email,
            'existing_flags' => $existing_flags, 'app_version' => $app_version);
        $this->db->where(array('phone_number' => $phone_number, 'device_id' => $deviceId));

        $this->db->update('users', $updateData);

        $result = (bool) ($this->db->affected_rows() > 0);

        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    }

    public function insertUserPhoneNumber($phone_number, $name, $email, $deviceId, $salt, $install_source, $account_email, $existing_flags, $app_version) {
        log_message('info', "Entering: " . __METHOD__);
        //log_message('info', "Phone: " . $phone_number . ",name: " . $name . ", email: " . $email . ", Device ID: " .
        //$deviceId . ", salt: " . $salt);

        $uid = uniqid();
        //log_message ('info', "UID: " . $uid);


        $id = base64_encode(hash_hmac('sha256', $uid, "authToken"));
        //log_message ('info', "ID: " . $id);




        $sql = "insert into users (user_email, name, user_image, phone_number, device_id, is_active, verify_code, user_token,
                install_source, account_email, existing_flags, app_version)
                values (?,?,?,?,?,?,?,?,?,?,?,?)";

        $this->db->query($sql, array($email, $name, "", $phone_number, $deviceId, 1, $salt, $id,
            $install_source, $account_email, $existing_flags, $app_version));


        $result = (bool) ($this->db->affected_rows() > 0);

        //log_message ('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        log_message('info', "SQL result: " . $result);
    }

    /**
     * @input: phone no and device id
     * @description: Checks phone no & device ID combination in Users table
     * @output: print response
     */
    public function checkUserPhoneNumber($phone_number, $deviceId) {
        $this->db->where(array('phone_number' => $phone_number, 'device_id' => $deviceId));
        $query = $this->db->get('users');
        return $query->result_array();
    }

    public function checkUserPhoneNumberVeri($phone_number, $deviceId) {
        $this->db->where(array('phone_number' => $phone_number, 'device_id' => $deviceId, 'is_verified' => 1));
        $query = $this->db->get('users');

        $result = (bool) ($this->db->affected_rows() > 0);

        //log_message ('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    /**
     * @input: Device ID
     * @description: Check Device ID exists or not
     * @output: Array
     */
    public function checkDevIDExists($deviceId) {
        //$this->db->where('device_id', $deviceId);
        $this->db->where(array('device_id' => $deviceId, 'is_verified' => '1'));
        $query = $this->db->get('users');

        $result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    /**
     * @input: Phone Number
     * @description: Check Phone exists or not
     * @output: Array
     */
    public function checkPhoneNumExists($phone) {
        //$this->db->where('phone_number', $phone);
        $this->db->where(array('phone_number' => $phone, 'is_verified' => '1'));
        $query = $this->db->get('users');

        $result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    /**
     * @input: Device ID and Phone Num
     * @description: Check whether the combination exists or not
     * @output: Array
     */
    public function checkDevIDAndPhoneExists($deviceId, $phone_number) {
        $this->db->where(array('phone_number' => $phone_number, 'device_id' => $deviceId, 'is_verified' => '1'));
        $query = $this->db->get('users');

        $result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    /**
     * @input: Account Email
     * @description: Check Account Email exists or not
     * @output: Array
     */
    public function checkAccEmailExists($account_email) {
        //$this->db->where('account_email', $account_email);
        $this->db->where(array('account_email' => $account_email, 'is_verified' => '1'));

        $query = $this->db->get('users');

        $result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    public function insertPassthruCall($callDetails) {
        $this->db->insert('passthru_misscall_log', $callDetails);
        return $this->db->insert_id();
    }

    public function checkPassThruLog($phoneNumber) {
        $query = "select * from passthru_misscall_log where `from_number` like '%$phoneNumber%';";
        $data = $this->db->query($query);
        return $data->result_array();
    }

    public function insertPassthruVendorExtnCall($callDetails) {
        $this->db->insert('passthru_vendor_extn_log', $callDetails);
        return $this->db->insert_id();
    }

    public function checkPassThruVendorExtnLog($callSid) {
        $query = "select * from passthru_vendor_extn_log where `callSid` like '%$callSid%';";
        $data = $this->db->query($query);

        return $data->result_array();
    }

    public function verifyUserNumber($phoneNumber) {
        $this->db->where('phone_number', $phoneNumber);
        $query = $this->db->get('users');

        $result = $query->result_array();
        if (count($result) > 0) {
            $this->db->where('phone_number', $phoneNumber);
            $this->db->update('users', array('is_verified' => 1));
        }

        $result = (bool) ($this->db->affected_rows() > 0);

        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    }

    function getSocialLogin($email, $type, $device_id) {
        $sql = "SELECT user_email as email, user_country as country, user_token as token FROM users WHERE user_email = ? and login_type = ? and device_id = ?";
        $data = $this->db->query($sql, array($email, $type, $device_id));
        return $data->result_array();
    }

    function checkSocialLogin($email, $type, $device_id) {
        $checkLogin = $this->getSocialLogin($email, $type, $device_id);
        if ($checkLogin) {
            return $checkLogin;
        }
        $uid = uniqid();
        $id = base64_encode(hash_hmac('sha256', $uid, "authToken"));
        $sql = "insert into users (user_email, user_password, login_type, is_active, user_token, device_id) values (?,?,?,?,?,?)";
        $this->db->query($sql, array($email, $id, $type, 1, $id, $device_id));

        if ($this->db->affected_rows()) {
            return $this->getSocialLogin($email, $type, $device_id);
        }
    }

    function userLogin($email, $password, $type, $device_id) {
        $sql = "SELECT user_email as email, user_country as country, user_token as token FROM users WHERE user_email = ? and user_password = ?";
        $data = $this->db->query($sql, array($email, $password));
        $result = $data->result_array();
        if ($result) {
            $this->updateDeviceId($email, $device_id);
            return $result;
        } else if ($type == "facebook" || $type == "google") {
            return $this->checkSocialLogin($email, $type, $device_id);
        }
    }

    function updateDeviceId($email, $device_id) {
        $sql = "SELECT device_id FROM users WHERE user_email = ?";
        $data = $this->db->query($sql, array($email));
        $result = $data->result_array();
        if ($result) {
            return true;
        } else {
            $updateData = array('device_id' => $device_id);
            $this->db->where('user_email', $email);
            $this->db->update('users', $updateData);
        }
    }

    /* @desc : this function get handyman id name service address
     * @param : handyman id
     * @return : array handyman information
     */

    function gethandyman($handymanid) {
        $this->db->select('*');
        $this->db->where('id', $handymanid);
        $query = $this->db->get('handyman');
        return $query->result_array();
    }

    function gethandymanphone($handymanid) {
        $this->db->select('phone');
        $this->db->where('id', $handymanid);
        $query = $this->db->get('handyman');
        return $query->result_array();
    }

    /* @desc : this function get handyman details from extn passed from the mobile app
     * @param : handyman extn (unique)
     * @return : array handyman information
     */

    function gethandymanfromextn($handyman_extn) {
        log_message('info', "Entering: " . __METHOD__);

        $this->db->select('id, name, phone');
        $this->db->from('handyman');
        $this->db->where('handyman.extension', $handyman_extn);
        $query = $this->db->get();

        return $query->result_array();
    }

    /* @desc : this function get handyman id name service address
     * @param : handyman id
     * @return : array handyman information
     */

    function getHandymanName($handymanid) {
        $this->db->select('name, service_id, services.services as service');
        $this->db->from('handyman');
        $this->db->join('services', 'services.id = handyman.service_id');
        $this->db->where('handyman.id', $handymanid);
        $query = $this->db->get();
        return $query->result_array();
    }

    /* @desc : this function get handyman id name service address
     * @param : handyman id
     * @return : array handyman information
     */

    function getHandymanProfile($handymanid) {
        $this->db->select('*');
        $this->db->from('handyman');
        $this->db->where('handyman.id', $handymanid);
        $query = $this->db->get();
        return $query->result_array();
    }

    function checkLocation($deviceId, $lat, $long) {
        $locationLast = $this->getLocationByLatLong($deviceId, $lat, $long);
        $location = $this->getLocation($deviceId);
        if ($locationLast) {
            return true;
        } else if ($location) {
            $location = array('latitude' => $lat, 'longitude' => $long);
            $this->updateLocation($deviceId, $location);
        } else {
            $location = array('latitude' => $lat, 'longitude' => $long);
            $this->saveLocation($deviceId, $location);
        }
    }

    function updateLocation($deviceId, $location) {
        $locationInfo = array();
        $locationInfo['latitude'] = $location['latitude'];
        $locationInfo['longitude'] = $location['longitude'];
        $locationInfo['update_time'] = date("Y-m-d H:i:s");
        if (array_key_exists("email_id", $location)) {
            $locationInfo['user_email'] = $location['email_id'];
        }
        $this->db->where('device_id', $deviceId);
        $this->db->update("location", $locationInfo);
    }

    function getLocation($deviceId) {
        $this->db->select('latitude, longitude');
        $this->db->where('device_id', $deviceId);
        $query = $this->db->get('location');
        return $query->result_array();
    }

    function getLocationByLatLong($deviceId, $lat, $long) {
        $this->db->select('latitude, longitude');
        $this->db->where(array('device_id' => $deviceId, 'latitude' => $lat, 'longitude' => $long));
        $query = $this->db->get('location');
        return $query->result_array();
    }

    function saveLocation($deviceId, $location) {
        $locationInfo = array();
        $locationInfo['device_id'] = $deviceId;
        $locationInfo['latitude'] = $location['latitude'];
        $locationInfo['longitude'] = $location['longitude'];
        $locationInfo['update_time'] = date("Y-m-d H:i:s");
        if (array_key_exists("email_id", $location)) {
            $locationInfo['user_email'] = $location['email_id'];
        }
        $this->db->insert("location", $locationInfo);
    }

    /*     * description* Post request to get authentication token
     */

    function getAuthToken($user) {

        $sql = "select user_token from users where user_email = '$user'";
        $data = $this->db->query($sql);
        return $data->result_array();
    }

    function resetPassword($email, $password) {
        $uid = uniqid();
        $id = base64_encode(hash_hmac('sha256', $uid, "authToken"));
        $sql = "update users set user_password = '$password', user_token = '$id' where user_email = '$email'";
        $this->db->query($sql);
        if ($this->db->affected_rows()) {
            $token['token'] = $uid;
            $token['notify'] = true;
            return $token;
        } else {
            return false;
        }
    }

    /*     * description* Post request to update authentication token
     */

    function updateAuthToken($email) {

        $uid = uniqid();
        $id = base64_encode(hash_hmac('sha256', $uid, "authToken"));
        $sql = "update users set user_token = '$id' where user_email = '$email'";
        $data = $this->db->query($sql);
        if ($this->db->affected_rows()) {
            return $uid;
        } else {
            return false;
        }
    }

    function getDeviceInfo($device_id, $user_email) {
        $array = array('device_id  =' => $device_id, 'user_email  =' => $user_email);
        $query = $this->db->select('user_email')->where($array)->limit(1)->get('deviceinfo');
        return $query->result_array();
    }

    function logTable($activity) {
        $this->db->insert("log_table", $activity);
    }

    function getIp2Location($ipno) {
        $array = array('ip_to >=' => $ipno, 'ip_from  <=' => $ipno);
        $query = $this->db_location->select('city_name, region_name, country_name, latitude, longitude')->where($array)->limit(1)->get('ip2location_db11');
        return $query->result_array();
    }

    function saveLocations($location, $email, $country_name) {
        $locationInfo = array();
        $locationInfo['latitude'] = "";
        $locationInfo['longitude'] = "";
        $locationInfo['user_email'] = $email;
        $locationInfo['country_name'] = $country_name;
        $locationInfo['update_time'] = date("Y-m-d H:i:s");
        if (array_key_exists("latitude", $location)) {
            $locationInfo['latitude'] = $location['latitude'];
        }
        if (array_key_exists("longitude", $location)) {
            $locationInfo['longitude'] = $location['longitude'];
        }
        $this->db->insert("location", $locationInfo);
    }

    function getLastLocation($latitude, $longitude, $user_email) {
        $array = array('latitude =' => $latitude, 'longitude  =' => $longitude, 'user_email  =' => $user_email);
        $query = $this->db->select('user_email')->where($array)->limit(1)->get('location');
        return $query->result_array();
    }

    /*     * description* check if a user is registered or not
     * @params String (Email)
     * @return Array (User info)
     */

    function isAlreadyRegistered($email) {

        $sql = "SELECT user_email FROM users WHERE user_email = ?";
        $data = $this->db->query($sql, array($email));
        return $data->result_array();
    }

    /*     * description* Post request to register a user
     */

    function insertRegisterData($data) {

        $uid = uniqid();
        $id = base64_encode(hash_hmac('sha256', $uid, "authToken"));
        $sql = "insert into users (user_email, user_password, user_country, is_active, user_token) values (?,?,?,?,?)";
        $this->db->query($sql, array($data['email'], $data['password'], $data['country'], 1, $id));

        if ($this->db->affected_rows()) {
            $token['token'] = $uid;
            $token['notify'] = true;
            return $token;
        }
    }

    /*     * description* Post request to insert data
     */

    function addHandyman($insert, $deviceId) {
        $sql = "insert into  handyman(`name`,`phone`,`service_id`,`address`,`experience`,`age`,`profile_photo`, `action`) values (?,?,?,?,?,?,?,?)";
        $this->db->query($sql, $insert);
        $handyanid = $this->db->insert_id();
        //echo $handymanid;
        return $this->addUserHandyman(array($deviceId, $handyanid));
    }

    /*     * description* Post request to insert data
     */

    function addUserHandyman($insert) {
        $sql = "insert into  user_handyman(`device_id`,`handyman_id`) values (?,?)";
        $this->db->query($sql, $insert);
        return $this->db->insert_id();
    }

    /*     * description* Post request to insert data
     */

    function insertData($insert) {


        $sql = "insert into  handyman(`name`,`phone`,`service`,`address`,`experience`,`age`,`profile_photo`,`is_paid`,`passport`,`identity`,`marital_status`,`works_on_weekends`,`work_on_weekdays`,`service_on_call`) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $this->db->query($sql, array($insert['name'], $insert['phone'], $insert['service'], $insert['address'], $insert['experience'], $insert['age'], $insert['profile_photo'], $insert['is_paid'], $insert['passport'], $insert['identity'], $insert['marital_status'], $insert['works_on_weekends'], $insert['work_on_weekdays'], $insert['service_on_call']));
        return $this->db->insert_id();
    }

    /*     * description*  This function is for check userid  is_already  insert or not
     * param : user profile id
     * @return : true
     */

    function checkUserId($userid) {
        $this->db->where('user_id', $userid);
        $data = $this->db->get('users');
        if ($data->result_array()) {
            return true;
        }
    }

    function getSharetext() {
        $query = $this->db->get('sharetext');
        $result = $query->result_array();
        return $result[0]['sharetext'];
    }

    /*     * description*  This function is to insert handymanReview
     * param : array request to insert handymanReview
     * @return : store user detail on handymanReview
     */

    function handymanReview($insert) {
        $sql = "insert into handyman_review(`behaviour`,`expertise`,`review`,`handyman_id`,`user_id`) values (?,?,?,?,?)";
        $this->db->query($sql, array($insert['behaviour'], $insert['expertise'], $insert['review'], $insert['handyman_id'], $insert['user_id']));
        return $this->db->insert_id();
    }

    /*     * description*  This function for get All  user detail from database
     * param :
     * @return :  user detail
     */

    function getuserProfile() {
        $this->db->select('*');
        $query = $this->db->get("users");
        return $query->result_array();
    }

    /*     * description*  This function for update userProfile
     * param :  userProfile id ,array requested input
     * @return :  UserProfile
     */

    function updateUserProfile($deviceId, $updateData) {
        $this->db->where('device_id', $deviceId);
        $this->db->update('users', $updateData);

        $this->db->select('user_id');
        $this->db->where('device_id', $deviceId);
        $query = $this->db->get("users");
        $result = $query->result_array();
        return $result[0]['user_id'];
    }

    function updateCompleteUserProfile($user_id, $updateData) {
        $this->db->where('user_id', $user_id);
        $this->db->update('users', $updateData);
    }
    /*     * description*  This function is for check userid  is_already  insert or not
     * param : user profile id
     * @return : true
     */

    function checkDeviceID($deviceId) {

        $this->db->where('device_id', $deviceId);
        $data = $this->db->get('users');
        if ($data->result_array()) {
            return true;
        }
    }

    /*     * description*  This function get userprofile for requested id
     * param :  userProfile id
     * @return :  userView
     */

    function getuserProfileid($user_id) {
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get("users");
        return $query->result_array();
    }

    /*     * description*  This function get userprofile for requested phone no & device id. verify_code is 4 digit random salt.
     * param :  userProfile id
     * @return :  userView
     */

    function getUserByPhoneNumber($deviceId, $phone_number) {
        $this->db->select('phone_number, user_id, verify_code');
        $this->db->where(array('device_id' => $deviceId, 'phone_number' => $phone_number));
        $query = $this->db->get("users");
        return $query->result_array();
    }

    /*     * description*  This function get userprofile for requested id
     * param :  userProfile id
     * @return :  userView
     */

    function getuserProfileByDeviceID($deviceId, $phone_number) {
        log_message('info', __METHOD__ . "=>DeviceID: " . $deviceId . ", Phone Num: " . $phone_number);

        $this->db->select('*');

        //$this->db->where('device_id',$deviceId);
        $this->db->where(array(
            'device_id' => $deviceId,
            'phone_number' => $phone_number,
            'is_verified' => 1
        ));

        $query = $this->db->get("users");

        $result = (bool) ($this->db->affected_rows() > 0);

        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    /*     * description*  This function get userprofile for requested id
     * param :  userProfile id
     * @return :  userView
     */

    function getUserIdByEmail($user_email) {
        $this->db->select('user_id,user_token');
        $this->db->where('user_email', $user_email);
        $query = $this->db->get("users");
        return $query->result_array();
    }

    /** @description*  This function get userprofile for requested id
     *  @param :  userProfile id
     *  @return :  userView
     */
    function getUserIdByDeviceID($device_id) {
        $this->db->select('user_id,user_token,user_email,phone_number');
        $this->db->where('device_id', $device_id);
        $query = $this->db->get("users");
        return $query->result_array();
    }

    /*     * description*  This function get userprofile for requested id
     * param :  userProfile id
     * @return :  userView
     */

    function getUserByDeviceID($device_id) {
        $this->db->select('*');
        $this->db->where('device_id', $device_id);

        $query = $this->db->get("users");

        return $query->result_array();
    }

    /* @desc : this function check handyman and user review
     * @param : handyman id and user_id
     * @return : review
     */

    function checkreview($handyman_id, $user_id) {
        $this->db->select('review');
        $this->db->where('handyman_id', $handyman_id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('handyman_review');
        return $query->result_array();
    }

    function deleteHandymans($saved_type, $deviceId, $handyman_id) {
        $getHandymanSaveIds = $this->getSaveHandymanIds($saved_type, $deviceId, $handyman_id);

        $updateData = array('is_del' => 1);
        $this->db->where_in('id', $getHandymanSaveIds[0]);
        $this->db->update('save_used_handyman', $updateData);
        return true;
    }

    function getSaveHandymanIds($type, $deviceId, $handyman_id) {
        $this->db->select('id');
        $this->db->where('type', $type);
        $this->db->where('device_id', $deviceId);
        $this->db->where_in('handyman_id', $handyman_id);
        $query = $this->db->get('save_used_handyman');
        return $query->result_array();
    }

    function saveUsedSavedHandyman($saved_type, $deviceId, $handyman_id, $report_msg) {
        $array = array('handyman_id' => $handyman_id,
            'device_id' => $deviceId, 'type' => $saved_type,
            'is_del' => 0, 'comment' => $report_msg);

        $this->db->insert('save_used_handyman', $array);

        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() .
            ", Result: " . $result);

        return $this->db->insert_id();
    }

    function checkUsedSavedHandyman($saved_type, $deviceId, $handyman_id, $report_msg) {
        $array = array('handyman_id' => $handyman_id, 'device_id' => $deviceId,
            'type' => $saved_type);
        $handymanStatus = $this->checkUsedSavedHandymanStatus($array);

        if ($handymanStatus) {
            if ($saved_type != "report") {
                //For Used or Saved cases
                $is_deleted = $handymanStatus[0]['is_del'];
                if ($is_deleted == 1) {
                    $updateData = array('is_del' => 0);
                    $this->db->where_in('id', $handymanStatus[0]['id']);
                    $this->db->update('save_used_handyman', $updateData);
                }
                return $handymanStatus;
            } else {
                //Report handyman case, update with the msg
                $updateData = array('comment' => $report_msg);
                $this->db->where_in('id', $handymanStatus[0]['id']);
                $this->db->update('save_used_handyman', $updateData);
            }
        }
    }

    function checkUsedSavedHandymanStatus($array) {
        $this->db->select('*');
        $this->db->where($array);
        $query = $this->db->get('save_used_handyman');


        return $query->result_array();
    }

    function GetUsedSavedHandymans($saved_type, $deviceId) {
        $search = array('save_used_handyman.type' => $saved_type, 'save_used_handyman.device_id' => $deviceId, 'save_used_handyman.is_del' => 0);
        $this->db->select('handyman_id,services as service_name,service_image,serial_no,name,phone,experience,age,profile_photo,is_paid,address,location,vendors_area_of_operation,Rating_by_Agent,id_proof_name,marital_status,passport,service_on_call,works_on_weekends,work_on_weekdays,extension');
        $this->db->from('save_used_handyman');
        $this->db->where($search);
        $this->db->join('handyman', 'handyman.id = save_used_handyman.handyman_id');
        $this->db->join('services', 'handyman.service_id = services.id');
        $this->db->order_by('save_used_handyman.create_date  desc');
        $query = $this->db->get();
        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        return $query->result_array();
    }

    function getServiceRadius($service) {
        $this->db->select('distance');
        $this->db->like('services', $service);
        $query = $this->db->get('services');
        return $query->result_array();
    }

    /** @description*  This function for api for review
     *  @param : handyman id
     *  @return :  array(review,user_id,user_image,name)
     */
    function getreviewhandyman($handyman_id) {
        $this->db->select('review, handyman_review.user_id as user_id,
            handyman_review.create_date as create_date,
            name as user_name,
            handyman_review.behaviour as rating');
        $this->db->from('handyman_review');
        $this->db->where('handyman_id', $handyman_id);
        $this->db->where('handyman_review.status', 1);
        $this->db->join('users', 'users.user_id = handyman_review.user_id');
        $query = $this->db->get();
        $result = $query->result_array();
        $i = 0;
        foreach ($result as $key => $value) {
            $time_ago = $this->time_elapsed_string($result[$i]['create_date']);
            $user_id = $value['user_id'];
            $result[$i]['create_date'] = $time_ago;
            $i = $i + 1;
        }
        return $result;
    }

    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    function gereviewss($result) {

        $i = 0;
        foreach ($result as $value) {
            $user_id = $value['user_id'];
            $this->db->select('name,user_image');
            $this->db->where('id', $user_id);
            $query = $this->db->get('users');
            $result1 = $query->result_array();
            $user = $this->countuser($user_id);
            if ($result1) {
                $result[$i]['name'] = $result1[0]['name'];
                $result[$i]['user_image'] = $result1[0]['user_image'];
            }
            $result[$i]['total_review_by_user'] = $user;
            $i = $i + 1;
        }
        return $result;
    }

    function countuser($user_id) {

        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results("handyman_review");
    }

    function searchservice($service, $area) {

//        $client = new Elasticsearch\Client();
//        $params['index'] = 'boloaaka';
//        $params['type'] = 'handyman';
//        $array = array();
//
//        for ($i = 0; $i < count($service['hits']['hits']); $i++) {
//
//            if (is_string($area)) {
//
//                $params['body']['query']['bool']['must'] = array(
//                    array('match' => array('service_id' => $service['hits']['hits'][$i]['_id'])),
//                    array('match' => array('address' => $area)),
//                    array('match' => array('action' => 1))
//                );
//            } else {
//                $json = '{
//        "sort" : [
//            {
//          "_geo_distance" : {
//              "location" : {
//                    "lat" : "' . $area['latitude'] . '",
//                    "lon" : "' . $area['longitude'] . '"
//              },
//              "order" : "asc",
//              "unit" : "km"
//          }
//            }
//        ],
//        "query": {
//          "filtered" : {
//
//       "query": {
//        "bool": {
//          "must": [
//            { "match": { "service_id":   "' . $service['hits']['hits'][$i]['_id'] . '"}},
//            { "match": { "action": 1 }}
//
//          ]
//        }
//      },
//        "filter" : {
//            "geo_distance" : {
//                "distance" : "' . $service['hits']['hits'][$i]['_source']['distance'] . "km" . '",
//                "location" : {
//                    "lat" : "' . $area['latitude'] . '",
//                    "lon" : "' . $area['longitude'] . '"
//                }
//            }
//        }
//          }
//        }
//      }';
//
//                $params['body'] = $json;
//            }
//
//
//            $searchApielasticsearch = $client->search($params);
//            array_push($array, $searchApielasticsearch);
//        }
//        //print_r($array);
//        return $array;
        
        return array();
    }

    function searchServiceByService($service, $area) {

//        $client = new Elasticsearch\Client();
//        $params['index'] = 'boloaaka';
//        $params['type'] = 'handyman';
//        $array = array();
//
//        if (is_string($area)) {
//
//            $params['body']['query']['bool']['must'] = array(
//                array('match' => array('service_id' => $service[0]['id'])),
//                array('match' => array('address' => $area)),
//                array('match' => array('action' => 1))
//            );
//        } else {
//            $json = '{
//        "sort" : [
//            {
//          "_geo_distance" : {
//              "location" : {
//                    "lat" : "' . $area['latitude'] . '",
//                    "lon" : "' . $area['longitude'] . '"
//              },
//              "order" : "asc",
//              "unit" : "km"
//          }
//            }
//        ],
//        "query": {
//          "filtered" : {
//
//       "query": {
//        "bool": {
//          "must": [
//            { "match": { "service_id":   "' . $service[0]['id'] . '"}},
//            { "match": { "action": 1 }}
//
//          ]
//        }
//      },
//        "filter" : {
//            "geo_distance" : {
//                "distance" : "' . $service[0]['distance'] . "km" . '",
//                "location" : {
//                    "lat" : "' . $area['latitude'] . '",
//                    "lon" : "' . $area['longitude'] . '"
//                }
//            }
//        }
//          }
//        }
//      }';
//
//            $params['body'] = $json;
//        }
//
//
//        $searchApielasticsearch = $client->search($params);
//        array_push($array, $searchApielasticsearch);
//        return $array;
        
        return array();
    }

    /**  @desc : This functon  for calculate latitude and longitude
     *   @param : area
     *   @return :  latitude and longitude
     */
    function calculateLatlonFromAddress($area) {

        $address = $area;
        $address = str_replace(" ", "+", $address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $area = array();

        if (array_key_exists("results", $response_a)) {
            if (array_key_exists("geometry", $response_a['results'][0])) {
                if (array_key_exists("location", $response_a['results'][0]['geometry'])) {
                    $area['latitude'] = $response_a['results'][0]['geometry']['location']['lat'];
                    $area['longitude'] = $response_a['results'][0]['geometry']['location']['lng'];
                }
            }
        }
        return $area;
    }

    function getArea($area, $userLocation) {
        if ($area == "Current Location" && ($userLocation != "" || $userLocation != "did not get user location" || $userLocation != null)) {
            $userLocation = json_decode($userLocation, true);
            return $userLocation;
        } else if ($area != "" && $area != null) {
            $userLocation = $this->calculateLatlonFromAddress($area);
            return $userLocation;
        }
        $ipaddress = getenv('REMOTE_ADDR');
        $new_area = $this->findLocationByIpNum($ipaddress);
        return $new_area;
    }

    /**
     * @input: IP Address
     * @description: find the location of the user according to the ip address
     */
    function findLocationByIpNum($ipaddress) {

        //convert ip address into ip number
        $ipno = $this->Dot2LongIP($ipaddress);
        //find location according to ip number
        $area = array();
        $getLocationFromIpNo = $this->getIp2Location($ipno);
        if ($getLocationFromIpNo) {
            $area['latitude'] = $getLocationFromIpNo[0]['latitude'];
            $area['longitude'] = $getLocationFromIpNo[0]['longitude'];
        }
        return $area;
    }

    /**
     * @input: Ipaddress
     * @description: Converts ipaddress to ip number
     * @output: Ip number
     */
    function Dot2LongIP($Ipaddress) {
        if ($Ipaddress == "") {
            return 0;
        } else {
            $ips = explode(".", $Ipaddress);
            //print_r($ips);
            return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
        }
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    function searchApiNew($service, $area, $searchkeyword, $deviceId, $userLocation, $user_id) {
        //echo $service. ", ".$area. ", ".$searchkeyword. ", ".$deviceId. ", ".$userLocation. ", ".$user_id;
//        $lat1 = "28.6100";
//        $long1 = "77.2300";
//        $getdistance = "0 km";
//        $area = $this->getArea($area, $userLocation);
//
//        if ($area) {
//            $lat1 = $area['latitude'];
//            $long1 = $area['longitude'];
//            $this->checkLocation($deviceId, $lat1, $long1);
//        } else {
//            $loc = $this->getLocation($deviceId);
//            if ($loc) {
//                for ($i = 0; $i < count($loc); $i++) {
//                    $lat1 = $loc[$i]['latitude'];
//                    $long1 = $loc[$i]['longitude'];
//                }
//            }
//        }
//        $client = new Elasticsearch\Client();
//        $params['index'] = 'boloaaka';
//        $params['type'] = 'services';
//        if ($searchkeyword) {
//            $params['body']['query']['match']['keywords'] = $searchkeyword;
//            $result = $client->search($params);
//
//            $searchApi = $this->searchservice($result, $area);
//        } else {
//            $services = $this->getServiceId($service);
//            if ($services) {
//                $searchApi = $this->searchServiceByService($services, $area);
//            } else {
//                $params['body']['query']['match']['services'] = $service;
//                $result = $client->search($params);
//                $searchApi = $this->searchservice($result, $area);
//            }
//        }
//
//        if ($searchApi) {
//
//            $array = array();
//
//            for ($j = 0; $j < count($searchApi); $j++) {
//                if (array_key_exists("hits", $searchApi[$j])) {
//                    for ($k = 0; $k < count($searchApi[$j]['hits']['hits']); $k++) {
//
//                        $address = $searchApi[$j]['hits']['hits'][$k]['_source']['address'];
//                        $handyman_id = $searchApi[$j]['hits']['hits'][$k]['_id'];
//
//                        //$rating = $this->getrating($handyman_id);
//                        $review = $this->checkReview($handyman_id, $user_id);
//                        $servicess = $this->getServiceById($searchApi[$j]['hits']['hits'][$k]['_source']['service_id']);
//                        //print_r($servicess);
//                        if ($review) {
//                            $searchApi[$j]['hits']['hits'][$k]['_source']['review_by_user'] = "true";
//                        } else {
//                            $searchApi[$j]['hits']['hits'][$k]['_source']['review_by_user'] = "false";
//                        }
//                        if (isset($searchApi[$j]['hits']['hits'][$k]['sort'])) {
//                            $distance = $searchApi[$j]['hits']['hits'][$k]['sort'][0];
//                        } else {
//                            $location = explode(",", $searchApi[$j]['hits']['hits'][$k]['_source']['location']);
//                            $lat2 = $location[0];
//                            $long2 = $location[1];
//                            $distance = $this->distance($lat1, $long1, $lat2, $long2, $unit = 'k');
//                        }
//                        $getdistance = round($distance, 2);
//                        $total_review = $this->gethandymanreview($handyman_id);
//
//                        $searchApi[$j]['hits']['hits'][$k]['_source']['total_review'] = count($total_review) + 1;
//
//                        $rating = $searchApi[$j]['hits']['hits'][$k]['_source']['Rating_by_Agent'];
//                        if ($rating == "Good") {
//                            $rating = 4.0;
//                        } else if ($rating == "Average") {
//                            $rating = 3.0;
//                        } else if ($rating == "Exceptional") {
//                            $rating = 5.0;
//                        } else if ($rating == "Bad") {
//                            $rating = 2.0;
//                        } else if ($rating == "Very Bad") {
//                            $rating = 1.0;
//                        }
//                        if ($total_review > 0) {
//                            $rating = ($rating + $total_review) / 2;
//                        }
//                        $searchApi[$j]['hits']['hits'][$k]['_source']['rating'] = $rating;
//                        $searchApi[$j]['hits']['hits'][$k]['_source']['service_name'] = $servicess[0]['services'];
//                        $searchApi[$j]['hits']['hits'][$k]['_source']['service_image'] = $servicess[0]['service_image'];
//                        $searchApi[$j]['hits']['hits'][$k]['_source']['distance'] = $getdistance . " Km";
//                        $searchApi[$j]['hits']['hits'][$k]['_source']['id'] = $handyman_id;
//                        if (isset($searchApi[$j]['hits']['hits'][$k]['_source'])) {
//                            $search[$j] = $searchApi[$j]['hits']['hits'][$k]['_source'];
//                        }
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['action']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['current_time']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['date_of_collection']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['time_of_data_collection']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['Other_handyman_contact']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['bank_account']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['id_proof_no']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['id_proof_photo']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['handyman_previous_customers']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['updatedate']);
//                        unset($searchApi[$j]['hits']['hits'][$k]['_source']['Agent']);
//                        //$i = $i + 1;
//                        array_push($array, $searchApi[$j]['hits']['hits'][$k]['_source']);
//                        //print_r($search);
//                    }
//                }
//            }
//            //print_r($array);
//            return $array;
//        }
        return array();
    }

    /** @description*  This function for search area,service , startid and lastid
     *  @param :  area,service , startid and lastid
     *  @return : array of handyman information
     */
    function searchApi($area, $service, $startid, $lastid, $searched_keyword) {
        if ($searched_keyword) {
            $sql = "select handyman.id, name,phone,experience, age,profile_photo, is_paid,address,location, vendors_area_of_operation, serial_no,Rating_by_Agent,id_proof_name,marital_status,passport,service_on_call,works_on_weekends,work_on_weekdays from handyman left join services on handyman.service_id = services.id where services.keywords like '%$searched_keyword%' and handyman.action = 1 order by handyman.is_paid desc";
        } else {
            $sql = "select handyman.id, name,phone,experience, age,profile_photo, is_paid,address,location, vendors_area_of_operation, serial_no,Rating_by_Agent,id_proof_name,marital_status,passport,service_on_call,works_on_weekends,work_on_weekdays from handyman left join services on handyman.service_id = services.id where services.services like '%$service%' and handyman.action = 1 order by handyman.is_paid desc";
        }

        $query = $this->db->query($sql);
        return $query->result_array();
        /* $this->db->select('id,name,phone,experience,age,profile_photo,is_paid,address,location,vendors_area_of_operation,serial_no,Rating_by_Agent,id_proof_name');
          $this->db->order_by('is_paid  desc');
          $this->db->like('service',$service);
          if($area != "" && $area != "Enter Area") {
          //$this->db->like('address',$area);
          //echo $area;
          }
          $this->db->where('action',1);
          $this->db->limit($lastid,$startid);
          $query = $this->db->get("handyman");
          //$this->output->enable_profiler(TRUE);
          return $query->result_array(); */
    }

    /* @desc : this function get handyman review
     * @param : handyman id
     * @return : array handyman information
     */

    function gethandymanreview($handyman_id) {
        //log_message ("info", __METHOD__);

        $this->db->select_sum('behaviour');
        $this->db->where(array('handyman_id' => $handyman_id, 'status' => 1));
        $query = $this->db->get('handyman_review');

        $sum = 0;
        $result = $query->result_array();
        $count = $this->counthandymanreview($handyman_id);

        if ($count) {
            foreach ($result as $key => $value) {
                $behaviour = $value['behaviour'] / $count;
                $sum += $behaviour;
            }
        }

        return $sum;
    }

    /* @desc : this function for count total no. handyman review for particular handyman
     * @param : handyman id
     * @return : array handyman information
     */

    function counthandymanreview($handyman_id) {
        $this->db->where(array('handyman_id' => $handyman_id, 'status' => 1));
        $count = $this->db->count_all_results('handyman_review');

        return $count;
    }

    /* @desc : this function get handyman details
     * @param : handyman id
     * @return : array handyman information
     */

    function getshandyman($handymanid) {
        $this->db->select('handyman.*, services.services, services.service_image');
        $this->db->where('handyman.id', $handymanid);
        $this->db->from('handyman');
        $this->db->join('services', 'services.id = handyman.service_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /** @description : get all services from database
     *  @return : array (list of all services)
     *  @params : void
     */
    function GetAllServiceNames() {
        $sql = $this->db->select('services');
        $query = $this->db->get("services");
        return $query->result_array();
    }

    /** @description : get all services from database
     *  @return : array (list of all services)
     *  @params : void
     */
    function getPopularKeywords() {
        $sql = $this->db->select('searchkeyword');
        $query = $this->db->get("popularSearch");
        return $query->result_array();
    }

    /** @description* get all service from database
     *  @return : array (service)
     */
    function getServiceId($services) {
        $this->db->select('*');
        $this->db->where('services', $services);
        $query = $this->db->get("services");
        return $query->result_array();
    }

    /** @description* get all service from database
     *  @return : array (service)
     */
    function getServiceById($id) {
        $this->db->select('service_image,services');
        $this->db->where('id', $id);
        $query = $this->db->get("services");
        return $query->result_array();
    }

    /** @description* get all service from database
     *  @return : array (service)
     */
    function GetAllServices() {
        $this->db->select('*');
        //$this->db->limit(4);
        $this->db->where('action !=', 0);
        $this->db->order_by("priority", "asc");
        $query = $this->db->get("services");

        return $query->result_array();
    }

    /** @description*  This function for search area,service , startid and lastid
     * @param :  area,service , startid and lastid
     * @return : array of handyman information
     */
    function searchHandyman($area, $service, $startid, $lastid) {
        $this->db->select('*');
        $this->db->where('action', 1);
        $this->db->order_by('is_paid  desc');
        $this->db->like('address', $area);
        $this->db->like('service', $service);
        $this->db->limit($lastid, $startid);
        $query = $this->db->get("handyman");
        return $query->result_array();
    }

    /** @description*  This function for get all service name and id
     * @param :  void
     * @return : array(servicename nad id)
     */
    function getAllServiceName() {
        $this->db->select("id,services");
        $query = $this->db->get("services");
        return $query->result_array();
    }

    /** @description*  This function for get all service name and id
     * @param :  service name
     * @return : array(servicename nad id)
     */
    function getservice_id($service_id) {
        $this->db->select('id,services');
        $this->db->where('services', $service_id);
        $query = $this->db->get("services");
        $result = $query->result_array();
        return $result[0]['id'];
    }

    function addCallQualityFeedback($insert) {
        //log_message('info', "Entering: " . __METHOD__);

        $sql = "insert into  call_quality_feedback (`handyman_id`, `user_id`, `handyman_available`, `call_rating`) "
            . "values (?, ?, ?, ?)";

        $this->db->query($sql, $insert);

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $this->db->insert_id();
    }

    function addUserContactsFileInfo($insert) {
        $sql = "INSERT INTO user_contacts_files (`user_id`, `file_name`)" .
            " VALUES (?, ?)";

        $this->db->query($sql, $insert);

        return $this->db->insert_id();
    }

    /** @description* Get appliances types for which bookings are open and user can
     * create wallet
     *  @return :
     */
    function getAppliancesList() {
        //log_message('info', __METHOD__ . "-> " . $id);

        $sql = "SELECT * FROM services WHERE isBookingActive = '1' AND id IN ('46','50','42','37', '28')  order by services";

        $query = $this->db->query($sql);

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        //log_message('info', print_r($query->result_array(), true));

        return $query->result_array();
    }

    /** @description* get all brands from database for a particular service
     *  @return :
     */
    function getBrandsForServiceId($service_id) {
        $this->db->select('brand_name');
        $this->db->where('service_id', $service_id);
        $this->db->where('seo', 1);
        $this->db->order_by('brand_name', "asc");
        $query = $this->db->get("appliance_brands");

        return $query->result_array();
    }


    /** @description* get all brands in a comma seperated string
     * for a particular service name
     *
     * @return : string containing all brand names
     */
    function getBrandsForService($service_name) {
        //echo $service_name;
        $brands_str = "";

        //1. find service id from name
        $service_ids = $this->getServiceId($service_name);
        $service_id = $service_ids[0]['id'];

        //log_message('info', "service id: " . $service_id);
        //2. find brand names from brands table based on the service id
        $brands = $this->getBrandsForServiceId($service_id);

        if (count($brands) > 0) {
            //log_message('info', count($brands) . " brands found");

            foreach ($brands as $brand) {
                //create a comma separated list of all brands
                $brands_str .= $brand['brand_name'];
                $brands_str .= ",";
            }

            //log_message('info', print_r($brands, TRUE));
        } else {
            //log_message('info', "0 brands found");
        }

        //echo $brands_str;
        return $brands_str;
    }

    function getPricingForService($service_name) {
        //log_message('info', __METHOD__ . "-> " . $service_name);
        //1. find service id from name
        $service_ids = $this->getServiceId($service_name);
        $service_id = $service_ids[0]['id'];

        //log_message('info', "service id: " . $service_id);
        //2. find pricing info from table based on the service id
        $this->db->select('category, capacity, service_category, check_box, customer_total');
        $this->db->where(array('service_id' => $service_id, 'active' => '1'));
        //$this->db->group_by(array("category", "capacity"));
        $query = $this->db->get("service_centre_charges");

        return $query->result_array();
    }

    function getPricingForServiceById($service_id, $partner_id) {
        //log_message('info', "service id: " . $service_id);

        $this->db->distinct();
        $this->db->select('service_category,category, capacity,customer_net_payable as customer_total, check_box');
        $this->db->where('service_id',$service_id);
        $this->db->where('active', 1);
        $this->db->where('check_box', 1);
        $this->db->where('partner_id', $partner_id);
        $this->db->where_not_in('service_category', array('Repeat Booking', 'Visit', 'Spare Parts'));
        

        $query = $this->db->get('service_centre_charges');

        return $query->result_array();
    }

    function insertBooking($booking_details) {
        log_message('info', __METHOD__ . "-> " . print_r($booking_details, TRUE));

        $sql = "insert into booking_details "
            . "(`user_id`, `service_id`, `booking_id`, `appliance_id`, `type`,"
            . "`source`, `booking_location`, `booking_date`, `booking_timeslot`,"
        . "`booking_picture_file`, `booking_remarks`,"
            . "`quantity`, `booking_address`, `booking_pincode`, "
            . "`booking_primary_contact_no`, `booking_alternate_contact_no`, "
            . "`discount_coupon`, `discount_amount`, `amount_due`) "
            . "values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $this->db->query($sql, $booking_details);

        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $this->db->insert_id();
    }

    function insertUnitDetails($unit_details) {
        log_message('info', __METHOD__ . "-> " . print_r($unit_details, TRUE));

        $sql = "insert into booking_unit_details "
            . "(`booking_id`, `appliance_brand`, `appliance_category`, `appliance_capacity`,"
            . "`price_tags`, `total_price`, `appliance_tag`) "
            . "values (?,?,?,?,?,?,?)";

        $this->db->query($sql, $unit_details);

        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $this->db->insert_id();
    }

    function getBookingByUser($user_id) {
        log_message('info', __METHOD__ . "-> " . $user_id);

        $this->db->select("booking_id, service_id, booking_address, booking_date, booking_timeslot,"
            . "current_status, amount_due, services.services as service_name,"
            . "services.service_image as service_image");
        $this->db->where("user_id", $user_id);
        $this->db->from("booking_details");
        $this->db->join('services', 'services.id = booking_details.service_id');
        $this->db->order_by('booking_details.create_date', 'DESC');

        $query = $this->db->get();

        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    function getBookingById($booking_details_id) {
        log_message('info', __METHOD__ . "-> " . $booking_details_id);

        $this->db->select("booking_id, booking_address, booking_date, booking_timeslot, booking_remarks,"
            . "current_status, amount_due, services.services as service_name,"
            . "services.service_image as service_image");
        $this->db->where("booking_id", $booking_details_id);
        $this->db->from("booking_details");
        $this->db->join('services', 'services.id = booking_details.service_id');

        $query = $this->db->get();

        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    function getUnitDetailsByBookingId($booking_details_id) {
        log_message('info', __METHOD__ . "-> " . $booking_details_id);

        $this->db->select("*");
        $this->db->where("booking_id", $booking_details_id);
        $this->db->from("booking_unit_details");

        $query = $this->db->get();

        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    function getBookingCountByUser($user_id) {
        //log_message('info', __METHOD__);

        $this->db->where("user_id", $user_id);
        $this->db->from("booking_details");

        //$query = $this->db->get();

        $result = $this->db->count_all_results();
        //log_message('info', __METHOD__ . " -> Result: " . $result);
        //return $query->result_array();
        return $result;
    }

    //Method to fetch various messages like legal policy, about us etc.
    function getAroundMessgaes($tag) {
        //log_message('info', __METHOD__);

        $this->db->select("message");
        $this->db->where("tag", $tag);

        $query = $this->db->get("around_messages");

        $result = (bool) ($this->db->affected_rows() > 0);
//        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    //Method to fetch booking cancellation reasons
    function getCancellationReasons() {
        //log_message('info', __METHOD__);

        $this->db->select("reason");
        //show only reasons which are meant for mobile app users
        $this->db->where("show_on_app", '1');

        $query = $this->db->get("booking_cancellation_reasons");

        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    //Cancel/Reschedule booking
    function updateBooking($booking_id, $data) {
        log_message('info', __METHOD__);

        $this->db->where('booking_id', $booking_id);
        $this->db->update('booking_details', $data);

        $result = (bool) ($this->db->affected_rows() > 0);
//        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        //Return updated booking object
        $this->db->select('*');
        $this->db->where('booking_id', $booking_id);
        $query = $this->db->get("booking_details");
        $result = $query->result_array();
        return $result[0];
    }

    //Method to fetch appliance tag hints
    function getApplianceTagHints($service) {
        //log_message('info', __METHOD__ . " => " . $service);
        //print_r($service);

        $this->db->select("tag1, tag2, tag3, tag4");
        $this->db->where("service_name", $service);

        $query = $this->db->get("appliance_tag_hints");

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        //echo __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result;
        //log_message('info', print_r($query->result_array(), TRUE));

        return $query->result_array();
    }

    function saveFeedback($user_id, $feedback) {
        $sql = "insert into user_feedback (user_id, feedback) values (?, ?)";

        $this->db->query($sql, array($user_id, $feedback));

        $result = (bool) ($this->db->affected_rows() > 0);

        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        //log_message('info', "SQL result: " . $result);
    }

    function getApplianceDetailsById($id) {
        //log_message('info', __METHOD__ . "-> " . $id);

        $sql = "SELECT * FROM appliance_details WHERE id = '$id'";

        $query = $this->db->query($sql);

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        //log_message('info', print_r($query->result_array(), TRUE));

        return $query->result_array();
    }

    function getApplianceByUser($user_id) {
        //log_message('info', __METHOD__ . "-> " . $id);

        $sql = "SELECT appliance_details.*, services.id as service_id,"
            . "services.services FROM appliance_details, services WHERE "
            . "appliance_details.user_id = '$user_id' "
            . "AND appliance_details.is_active = '1' "
            . "AND appliance_details.service_id = services.id";

        $query = $this->db->query($sql);

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        return $query->result_array();
    }

    function getApplianceCountByUser($user_id) {
        log_message('info', __METHOD__ . "=> User ID: " . $user_id);

        $this->db->where(array('user_id' => $user_id, 'is_active' => '1'));
        $this->db->from("appliance_details");

        $result = $this->db->count_all_results();

        //log_message('info', __METHOD__ . " -> Result: " . $result);

        return $result;
    }

    /* Add appliance automatically when user makes a booking with adding
     * appliance first
     */
    function addApplianceFromBooking($appliance_details) {
//        log_message('info', __METHOD__ . "-> " . print_r($appliance_details, TRUE));

        $sql = "INSERT INTO appliance_details "
            . "(`user_id`, `service_id`, `brand`, `category`, `capacity`, `tag`)"
            . "VALUES (?,?,?,?,?,?)";

        $this->db->query($sql, $appliance_details);

        $result = (bool) ($this->db->affected_rows() > 0);
//        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        $id = $this->db->insert_id();

        $sql = "SELECT * FROM appliance_details WHERE id = '$id'";
        $query = $this->db->query($sql);
        //$q = $this->db->get_where('appliance_details', array('id' => $id));

        return $query->result_array();
        //return $this->db->insert_id();
    }

    //Add appliance in the wallet directly
    function addNewAppliance($appliance_details) {
        //log_message('info', __METHOD__ . "-> " . print_r($appliance_details, TRUE));

        $sql = "INSERT INTO appliance_details "
            . "(`user_id`, `service_id`, `brand`, `category`, `capacity`, "
            . "`model_number`, `tag`, `purchase_date`, `rating`,"
            . "`warranty_card_pic`, `invoice_pic`) "
            . "VALUES (?,?,?,?,?,?,?,?,?,?,?)";

        $this->db->query($sql, $appliance_details);

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);

        $id = $this->db->insert_id();
        $q = $this->db->get_where('appliance_details', array('id' => $id));

        return $q->row();
        //return $this->db->insert_id();
    }

    //Edit appliance from wallet
    function updateApplianceDetails($id, $updateData) {
        log_message('info', __METHOD__);

        $this->db->where(array('id' => $id));

        $this->db->update('appliance_details', $updateData);

        $result = (bool) ($this->db->affected_rows() > 0);

        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    }

    //Edit appliance details from Booking path
    function updateApplianceCategoryCapacity($id, $brand, $category, $capacity) {
        log_message('info', __METHOD__);

        $this->db->where(array('id' => $id));
        $updateData = array('brand' => $brand, 'category' => $category, 'capacity' => $capacity);
        $this->db->update('appliance_details', $updateData);
        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    }

    //Add appliance pics
    function addAppliancePics($appliance_id, $picture_tag, $picture_name) {
        log_message('info', __METHOD__);


        $this->db->where(array('id' => $appliance_id));

        switch ($picture_tag) {
            case 'warrantyCardPic':
                $updateData = array('warranty_card_pic' => $picture_name);
                break;

            case 'invoiceCardPic':
                $updateData = array('invoice_pic' => $picture_name);
                break;

            default:
                break;
        }

        $this->db->update('appliance_details', $updateData);
        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    }

    function updateAppliancePics($appliance_id, $picture_tag, $picture_name) {
        log_message('info', __METHOD__);

        $this->db->where(array('id' => $id));
        $updateData = array('brand' => $brand, 'category' => $category, 'capacity' => $capacity);
        $this->db->update('appliance_details', $updateData);
        $result = (bool) ($this->db->affected_rows() > 0);
        log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    }

    function removeAppliance($id) {
        //log_message('info', __METHOD__);
        //Make is_active flag as 0 for this appliance, do not actually delete it
        $updateData = array('is_active' => '0');
        $this->db->where(array('id' => $id));
        $this->db->update('appliance_details', $updateData);

        //$result = (bool) ($this->db->affected_rows() > 0);
        //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
    }

    function addSampleAppliances($user_id, $count) {
//        log_message('info', "Entering: " . __METHOD__);

        $sql1 = "SELECT * FROM sample_appliances";
        $query = $this->db->query($sql1);

        $appl = $query->result_array();

        for ($i = 0; $i < $count; $i++) {
            $appl[$i]['user_id'] = $user_id;
            //log_message('info', "Sample Appl: " . print_r($appl, TRUE));

            $sql2 = "INSERT INTO appliance_details "
                . "(`service_id`, `brand`, `category`, `capacity`, "
                . "`model_number`, `tag`, `purchase_date`, `rating`, `user_id`)"
                . "VALUES (?,?,?,?,?,?,?,?,?)";

            $this->db->query($sql2, $appl[$i]);

            $result = (bool) ($this->db->affected_rows() > 0);
            //log_message('info', __METHOD__ . " => SQL: " . $this->db->last_query() . ", Result: " . $result);
        }
    }
    
    function insertRatingPassthruCall($callDetails){
        $this->db->insert('rating_passthru_misscall_log', $callDetails);
        return $this->db->insert_id();
    }
    /*
     * This Function is used to save fake reschedule miss call data in reschedule miss call log table
     */
    function insertFakeReschedulePassthruCall($data){
         $this->db->insert('fake_reschedule_missed_call_log', $data);
        return $this->db->insert_id();
    }
        /*
     * This Function is used to save fake cancellation miss call data in cancellation miss call log table
     */
    function insertFakeCancellationPassthruCall($data){
         $this->db->insert('fake_cancellation_missed_call_log', $data);
        return $this->db->insert_id();
    }
    
    function getMissedBookingSlots($time_slot = false){
        $hr = "";
        if($time_slot){
            $booking_slot = explode("-", $time_slot);
            $hr = trim($booking_slot[0]);
        }
        else{
           $hr =  date("H");
        }
        if($hr >= "10" && $hr <= "13"){
            return false;
        }
        else if($hr >= "13" && $hr <= "16"){
            $return =  array(TIMESLOT_10AM_TO_1PM);
            return $return;
        }
        else if($hr >= "16" && $hr <= "19"){
            $return =  array(TIMESLOT_10AM_TO_1PM, TIMESLOT_1PM_TO_4PM);
            return $return;
        }
        else{
            return false;
        }
    }
    
    /*This function is used to get tech support numbers for engineer*/
    function techSupportNumberForEngineer($booking_id = false){
        if($booking_id){
            $sql = "SELECT service_centres.primary_contact_phone_1 as service_manager, employee.exotel_phone as account_manager, "._247AROUND_CALLCENTER_NUMBER." as toll_free_number FROM booking_details 
                    JOIN service_centres on service_centres.id = booking_details.assigned_vendor_id 
                    JOIN partners on partners.id = booking_details.partner_id 
                    JOIN employee on employee.id = partners.account_manager_id 
                    WHERE booking_details.booking_id='".$booking_id."'";
            $query = $this->db->query($sql);
            return $query->result_array();
        }
        else{
            return false;
        }
    }
}
