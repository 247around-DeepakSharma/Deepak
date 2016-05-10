<?php 


$client = new Elasticsearch\Client();
//foreach ($handyman as $key => $value) {
  
/*
$data = array(
    
        "serial_no"=> $value['serial_no'],
        "name"=> $value['name'],
        "phone"=> $value['name'],
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
        "location"=>  $value['location'],
        "vendors_area_of_operation"=>  $value['vendors_area_of_operation'],
        "bank_account"=>  $value['bank_account'],
        "bank_ac_no"=>  $value['bank_ac_no'],
        "id_proof_name"=> $value['id_proof_name'],
        "id_proof_no"=>  $value['id_proof_no'],
        "id_proof_photo"=> $value['id_proof_photo'],
        "handyman_previous_customers"=>  $value['handyman_previous_customers'],
        "Other_handyman_contact"=>  $value['experience'],
        "Rating_by_Agent"=>  $value['Other_handyman_contact'],
        "Agent"=>  $value['Agent'],
        "updatedate"=>  $value['updatedate']
       
);
/*
$data = array(


            "services" => $value['services'],
            "service_image" => $value['service_image'],
            "distance" => $value['distance'],
            "keywords" => $value['keywords'],
            "update_date" => $value['update_date'],
            "create_date" => $value['create_date'],
            "priority" => $value['priority']


    );*/

//$indexParams['index'] = "boloaaka";
/*$indexParams['type'] = "handyman";

$filter = array();
$filter['term']['service_id'] = '25';

$query = array();
$query['match']['address'] = 'bhangel';

$indexParams['body']['query']['filtered'] = array(
    "filter" => $filter,
    "query"  => $query
   

);

$result = $client->search($indexParams);

    echo '<br/>';
  print_r(json_encode($result));
   echo '<br/>';
*/
  /* $params['index'] = 'boloaaka';
   $params['type']  = 'handyman';
   $params['body']['query']['bool']['must'] = array(
    array('match' => array('service_id' => 6)),
    array('match' => array('address' => 'noida')),
);

$result = $client->search($params);
  print_r($result);*/


//}
    $service_id['service_id']       = '30';
    $updateParams['index']          = 'boloaaka';
    $updateParams['type']           = 'handyman';
    $updateParams['id']             = '193';
    $updateParams['body']['doc']    = $service_id;
    
    //$updateParams['body']['doc']    = array('keywords' => 'test');

$retUpdate = $client->update($updateParams);
print_r($retUpdate);


/*
curl -XPUT http=>//localhost=>9200/abhay234/test8/11 -d '
{
    "type" => "jdbc",
    "jdbc" => {
        "strategy" => "simple",
        "poll" => "5s",
        "scale" => 0,
        "autocommit" => false,
        "fetchsize" => 10,
        "max_rows" => 0,
        "max_retries" => 3,
        "max_retries_wait" => "10s",
        "driver" => "com.mysql.jdbc.Driver",
        "url" => "jdbc=>mysql=>//mysql-server=>3306/boloaaka",
        "user" => "root",
        "password" => "paras",
        "sql" => "select * from handyman"
    },
    "index" => {
        "index" => "mainIndex",
        "type" => "category",
        "bulk_size" => 30,
        "max_bulk_requests" => 100,
        "index_settings" => null,
        "type_mapping" => null,
        "versioning" => false,
        "acknowledge" => false
    }'
    curl -XPUT http=>//localhost=>9200/boloaaka1/test2/1 -d '
{
    "type" => "jdbc",
    "jdbc" => {
        "strategy" => "simple",
        "poll" => "5s",
        "scale" => 0,
        "autocommit" => false,
        "fetchsize" => 10,
        "max_rows" => 0,
        "max_retries" => 3,
        "max_retries_wait" => "10s",
        "driver" => "com.mysql.jdbc.Driver",
        "url" => "jdbc=>mysql=>//mysql-server=>3306/boloaaka",
        "user" => "root",
        "password" => "paras",
        "sql" => "select * from handyman"
    },
    "index" => {
        "index" => "boloaaka1",
        "type" => "test2",
        "bulk_size" => 30,
        "max_bulk_requests" => 100,
        "index_settings" => null,
        "type_mapping" => null,
        "versioning" => false,
        "acknowledge" => false
    }
}'

curl -XPUT 'localhost=>9200/abhay23456/my_jdbc_river/_meta' -d '{
     "index.routing.allocation.include.tag" => "value1,value2",
    "type" => "jdbc",
    "jdbc" => {

        "url" => "jdbc=>mysql=>//localhost=>3306/boloaaka",
        "user" => "root",
        "password" => "paras",
        "sql" => "select * from handyman"
    }
}'

    curl "localhost=>9200/_nodes?pretty=true&settings=true"
    ./bin/plugin -DproxyHost=localhost -DproxyPort=9200 --install jdbc --url http=>//xbib.org/repository/org/xbib/elasticsearch/plugin/elasticsearch-river-jdbc/1.3.4.4/elasticsearch-river-jdbc-1.3.4.4-plugin.zip
}'*/
/*
curl -XPUT localhost=>9200/_river/boloaaka/2 -d '
  {
    "type"=> "jdbc",
    "jdbc"=> {
       "driver"=> "com.mysql.jdbc.Driver",
      "url"=> "jdbc-1.5.0.0-197ef2e=>sqlserver=>//localhost;databaseName=boloaaka",
      "user"=> "root",
      "password"=> "paras",
      "sql"=> "SELECT * FROM handyman",
      "index"=> "example",
      "type"=> "product",
      "schedule"=> "00 00 01 * * ?"
    }
  }'*/

?>

<!--curl -XPUT localhost=>9200/_river/service/1 -d 
           '{
                "type" => "jdbc",
                      "jdbc" => 
                       {
                        "driver" => "com.mysql.jdbc.Driver",
                        "url" => "jdbc=>mysql=>//mysql-server=>3306/boloaaka",
                        "user" => "root",
                        "password" => "paras",
                        "sql" => "select * from handyman",
                        "index" => "services", 
                        "type" => "test" 
                       }
           }'curl -XDELETE 'http =>//localhost =>9200/boloaaka/'


           curl -XPUT localhost=>9200/_river/service/1 -d '
  {
    "type"=> "jdbc",
    "jdbc"=> {
       "driver"=> "com.mysql.jdbc.Driver",
      "url"=> "jdbc=>mysql=>//mysql-server=>3306/boloaaka",
      "user"=> "root",
      "password"=> "paras",
      "sql"=> "select * from handyman",
      "index"=> "service",
      "type"=> "test"
      
    }
  }'


