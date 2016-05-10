<?php 


$client = new Elasticsearch\Client();
if(isset($handyman)){
foreach ($handyman as $key => $value) {
 $location = json_decode($value['location'], true);
                   
                   $lat2 = $location['lattitude'];
                   $long2 = $location['longitude'];


  //$location = $lat.",".$lon;

$data = array(
       
       // "serial_no"=> $value['serial_no'],
        "name"=> $value['name'],
        "phone"=> $value['phone'],
        "service_id"=> $value['service_id'],
        "address"=> $value['address'],
        "experience"=> $value['experience'],
        "age"=>  $value['age'],
        "profile_photo"=>  $value['profile_photo'],
        "current_time"=>  $value['current_time'],
        "is_paid"=>  $value['is_paid'],
        "passport"=>  $value['passport'],
        "identity"=>  $value['identity'],
        "action"=>  $value['action'],
        "marital_status"=>  $value['marital_status'],
        "works_on_weekends"=>  $value['works_on_weekends'],
        "work_on_weekdays"=>  $value['work_on_weekdays'],
        "service_on_call"=>  $value['service_on_call'],
        "date_of_collection"=>  $value['date_of_collection'],
        "time_of_data_collection"=>  $value['time_of_data_collection'],
        "is_disabled"=>  $value['is_disabled'],
        "location"=>  $lat2.",".$long2,
        "vendors_area_of_operation"=>  $value['vendors_area_of_operation'],
        "bank_account"=>  $value['bank_account'],
        "bank_ac_no"=>  $value['bank_ac_no'],
        "id_proof_name"=> $value['id_proof_name'],
        "id_proof_no"=>  $value['id_proof_no'],
        "id_proof_photo"=> $value['id_proof_photo'],
        "handyman_previous_customers"=>  $value['handyman_previous_customers'],
        "Other_handyman_contact"=>  $value['Other_handyman_contact'],
        "Rating_by_Agent"=>  $value['Rating_by_Agent'],
        "Agent"=>  $value['Agent'],
        "updatedate"=>  $value['updatedate'],
        "approved" => $value['approved'],
        "approve_by" => $value['approve_date'],
        "verified"=> $value['verify_by'],
        "verify_date" => $value['Android_Phone'],
        "common_charges" => $value['common_charges'],
        "police_verification" => $value['police_verification'],
        "image_processing" => $value['image_processing']
       
);


$indexParams['index'] = "boloaaka";
$indexParams['type'] = "handyman";
$indexParams['id']   =  $value['id'];
$indexParams['body'] = $data;



$result = $client->create($indexParams);
print_r($result);
}

}

else if(isset($service)){
    foreach ($service as $key => $value) {
    $data = array(

          
            "services" => $value['services'],
            "service_image" => $value['service_image'],
            "distance" => $value['distance'],
            "keywords" => $value['keywords'],
            "update_date" => $value['update_date'],
            "create_date" => $value['create_date'],
            "priority" => $value['priority']


    );
    

    $indexParams['index'] = "boloaaka";
    $indexParams['type'] = "services";
    $indexParams['id']   =  $value['id'];
    $indexParams['body'] = $data;



$result = $client->create($indexParams);
print_r($result);

    }

} else if(isset($popular)){
    foreach ($popular as $key => $value) {
    $data = array(

            
            "searchkeyword"=> $value['searchkeyword'],
            "update_date" => $value['update_date'],
            "create_date"=> $value['create_date']

        );

    $indexParams['index'] = "boloaaka";
    $indexParams['type'] = "popularSearch";
    $indexParams['id']   =  $value['id'];
    $indexParams['body'] = $data;
    $result = $client->create($indexParams);
    print_r($result);

}

}





?>


