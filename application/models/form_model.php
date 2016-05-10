<?php
class Form_model extends CI_Model{
  /**
  * @desc load both db
  */
  function __construct(){
  parent::__Construct();
  $this->db_location = $this->load->database('default1', TRUE,TRUE);
  $this->db = $this->load->database('default', TRUE,TRUE);
  }

  /* @desc : this function get handyman id name service address
   * @param : handyman id
   * @return : array handyman information
   */

  function gethandyman($handymanid){
    $this->db->select('id, name, service, address');
    $this->db->where('id', $handymanid);
      $query = $this->db->get('handyman');
      return $query->result_array();

  }


  /**@description*  This function for get rating by agent in handyman
    * @param :  handyman id
    * @return : array (rating_by_agent)
    */
    function rate($handyman_id){
      $this->db->select('Rating_by_Agent');
      $this->db->where('id',$handyman_id);
      $query = $this->db->get("handyman");
      return $query->result_array();

    }


  

   /** @description*  this function for  get all handyman Information for requested handyman address
    *  @param request search address
    *  @return : data from handyman 
    */
    function searchAddress($search){
      $this->db->select('id,name,phone,experience,age,profile_photo,is_paid,address');
      $this->db->like('address',$search);
      $query=$this->db->get("handyman");
      return $query->result_array();
    }

   /** @description*   This function is for search handyman information  between two request id
    *  @param : limit request firstid and last id 
    *  @return : data from handyman
    */
    function getallhandyman($firstid,$lastid){
      $this->db->select('id,name,phone,service,experience,age,profile_photo,is_paid,address');
      $this->db->limit($lastid,$firstid);
      $query=$this->db->get("handyman");
      return $query->result_array();
     
      }
  /* @desc : this function get handyman details
   * @param : handyman id
   * @return : array handyman information
   */

     function getshandyman($handymanid){
      $this->db->select('id,name,phone,address,service,experience,age,profile_photo,is_paid,passport,identity,marital_status,works_on_weekends,work_on_weekdays,service_on_call,action');
      $this->db->where('id',$handymanid);
      $query = $this->db->get('handyman');
        return $query->result_array();
     }
  /* @desc : this function get handyman review
   * @param : handyman id
   * @return : array handyman information
   */

     function gethandymanreview($handyman_id){
      $this->db->select_sum('behaviour');
      $this->db->select_sum('expertise');
      $this->db->where('handyman_id',$handyman_id);
      $query = $this->db->get('handyman_review');
      $result = $query->result_array();
      $count = $this->counthandymanreview($handyman_id);
      foreach ($result as $key => $value) 
          $behaviour = $value['behaviour']/$count;
          $expertise = $value['expertise']/$count;
          $sum = ($behaviour + $expertise)/2;
          return $sum;
     }
  /* @desc : this function for count total no. handyman review for particular handyman
   * @param : handyman id
   * @return : array handyman information
   */

     function counthandymanreview($handyman_id){
        $this->db->where('handyman_id',$handyman_id);
        $this->db->where('status', '0');
        $count = $this->db->count_all_results('handyman_review');
        if($count){
          return $count;
        } else {
          return '1';
        }
     }

  /* @desc : this function check handyman and user review
   * @param : handyman id and user_id
   * @return : review
   */

    function checkreview($handyman_id,$user_id){
      $this->db->select('review');
      $this->db->where('handyman_id',$handyman_id);
      $this->db->where('user_id',$user_id);
      $query = $this->db->get('handyman_review');
      return $query->result_array();

    }

    function getdistance($service){
 
      $this->db->select('distance');
      $this->db->where('services',$service);
      $query = $this->db->get('services');
      $result = $query->result_array();
    foreach ($result as $key => $value) 
     return $distance = $value['distance'];
    
      
    }

    function gethandymanbyservice($service){
      $service = json_encode(array($service));
      $this->db->select('location');
      $this->db->where('service',$service);
      $query = $this->db->get('handyman');
      return $query->result_array();
     

    }

 /**description*  This function for search handyman name & services from requested services 
  * param : request input for serach service
  * @return : name and service
  */

  

  function getexperience(){
    $this->db->select('id,experience');
    $query = $this->db->get('handyman');
    return $query->result_array();
  }
  function updateexp($id,$sum){
   $this->db->where('id', $id);
   $this->db->update('handyman',$sum);

  }

 /**description*  This function for insert savehandyman
  * param : device id ,handyman id ,type
  * @return : 
  */
  
   function saveHandyman($insert){
    $this->db->insert('save_handyman',$insert);
    return $this->db->insert_id();

  }
 /**description*  This function for search handyman id from requested input and get handymaninfo from handyman table
  * param : device id  ,type
  * @return : array(handyman)
  */
 function searchhandyman($search){
  $this->db->select('handyman_id,serial_no,name,phone,experience,age,profile_photo,is_paid,address,location,vendors_area_of_operation,Rating_by_Agent,id_proof_name,marital_status,passport,service_on_call,works_on_weekends,work_on_weekdays');
  $this->db->from('save_handyman');
  $this->db->where($search);
  $this->db->join('handyman','handyman.id = save_handyman.handyman_id');
  $this->db->order_by('create_date  desc'); 
  $query = $this->db->get();
  return $query->result_array();
    
 }
  /**description*  This function for get all handyman for elasticsearch
  * param :
  * @return : array(handyman)
  */
 function elastichandyman(){
  $this->db->select('*');
  $query = $this->db->get('handyman');
  return $query->result_array();

  }
  /**description*  This function for get all service for elasticsearch
  * param :
  * @return :  array(service)
  */

  function elasticsearvice(){
    $this->db->select('*');
    $query = $this->db->get('services');
    return $query->result_array();
  }
  /**description*  This function for get all  popularseach for elasticsearch
  * param :
  * @return :  array(popularSearch)
  */
  function elasticpopular(){
     $this->db->select('*');
     $query = $this->db->get('popularSearch');
     return $query->result_array();
  }


  function countuser($user_id){

  $this->db->where('user_id',$user_id);
  return  $this->db->count_all_results("handyman_review");
 
}

/**description*  This function for api for review
  * param : handyman id
  * @return :  array(review,user_id,user_image,name)
  */

function getreviewhandyman($handyman_id){
  $this->db->select('review,user_id,name,user_image');
  $this->db->from('handyman_review');
  $this->db->where('handyman_id',$handyman_id);
  $this->db->where('status', '0');
  $this->db->join('user_profile','user_profile.id = handyman_review.user_id');
  $query = $this->db->get();
  $result = $query->result_array();
 $i =0;
  foreach ($result as $key => $value) {
    $user_id = $value['user_id'];
    $result[$i]['total_review_by_user'] = $this->countuser($user_id);
    if(empty($value['user_image'])){
      $result[$i]['user_image'] = 'user-31-03-2015-05-26-55pm-6ea9e5d18ea5839dae16a06d76c1fcf1.jpg';
    }
    $i =$i+1;
}
return $result;
}



/**description*  This function for get handyman id  from save_used handyman table
  * param : handyman id
  * @return :  array(handyman)
  */

function GetUsedSavedHandymans($saved_type,$deviceId) {
      $search = array('save_used_handyman.type' => $saved_type,'save_used_handyman.device_id' => $deviceId, 'save_used_handyman.is_del' => 0);
      $this->db->select('handyman_id');
      $this->db->where($search);
      $this->db->order_by('save_used_handyman.create_date  desc'); 
      $query = $this->db->get('save_used_handyman');
      $result =  $query->result_array();
      $handy = $this->GetSavedHandymans($result);
      return $handy;

    }


/**description*  This function for get handyman  from elasticsearch
  * param : handyman id
  * @return :  array(handyman)
  */
    

    function  GetSavedHandymans($result){
    $client = new Elasticsearch\Client();
    $indexParams['index'] = "boloaaka";
    $indexParams['type'] = "handyman";
    for ($i=0; $i <count($result) ; $i++) { 
      $indexParams['id'] = $result[$i]['handyman_id'];    
       $handy[$i] = $client->get($indexParams);
      unset($handy[$i]['_source']['action']);unset($handy[$i]['_source']['current_time']);unset($handy[$i]['_source']['date_of_collection']);unset($handy[$i]['_source']['time_of_data_collection']);unset($handy[$i]['_source']['Other_handyman_contact']);
       unset($handy[$i]['_source']['bank_account']);unset($handy[$i]['_source']['id_proof_no']);unset($handy[$i]['_source']['id_proof_photo']);unset($handy[$i]['_source']['handyman_previous_customers']);unset($handy[$i]['_source']['updatedate']);        
               
    
    }

    return $handy;

  
    }

     /** @description* this function  is for to calculate Latitude and  Longitude
    *  @return :  Latitude and  Longitude
    */

function distance($lat1, $lon1, $lat2, $lon2,$unit) {

  $theta = $lon1 - $lon2;
  $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist  = acos($dist);
  $dist  = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit  = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}
 function getArea($area, $userLocation) {
       
        
        if($area == "" || $area == "area" || $area == "Current Location") {
        if($area == "Current Location" && ($userLocation != "" || $userLocation != null)) {
            $userLocation = json_decode($userLocation, true);
            return $userLocation;
        }
            $ipaddress = getenv('REMOTE_ADDR');
            $new_area = $this->findLocationByIpNum($ipaddress);
            return $new_area;
        }
        else {
            return $area;
        }
    }

  function searchApi($service,$area,$searchkeyword,$deviceId,$userLocation,$user_id){

    $area = $this->getArea($area,$userLocation);
    $unit = "K";
    $client = new Elasticsearch\Client();
    $params['index'] = 'boloaaka';
    $params['type']  = 'services';
    if($searchkeyword){
      $params['body']['query']['match']['keywords'] = $searchkeyword;

    } else {
      $params['body']['query']['match']['services'] = $service;

    }
     $result = $client->search($params);
     $searchApi = $this->searchservice($result,$area);
     $lat1 = "28.6100";$long1="77.2300";


    
            $loc = $this->apis->getLocation($deviceId);
            if($loc) {
              for ($i=0; $i <count($loc) ; $i++) { 
                $lat1 = $loc[$i]['latitude'];
               $long1 = $loc[$i]['longitude'];
             }
                }      
            
                   
            


     if($searchApi){
            $array = array();
          
            for ($j=0; $j <count($searchApi) ; $j++) {
              for ($k=0; $k <count($searchApi[$j]['hits']['hits']) ; $k++) { 
              
              

              $address     = $searchApi[$j]['hits']['hits'][$k]['_source']['address'];
            

                $handyman_id = $searchApi[$j]['hits']['hits'][$k]['_id'];
            
               //$rating = $this->getrating($handyman_id);
              $review      = $this->checkReview($handyman_id,$user_id);
               if($review) {
                   $searchApi[$j]['hits']['hits'][$k]['_source']['review_by_user'] = "true";
                  } else {
                    $searchApi[$j]['hits']['hits'][$k]['_source']['review_by_user'] ="false";
                  }
             
                  // $location = json_decode($searchApi[$j]['hits']['hits'][$k]['_source']['location'], true);
                   
                 //  $lat2 = $location['longitude'];
                 // $long2 = $location['lattitude'];
                    
                   //$distance = $this->distance($lat1, $long1, $lat2, $long2, $unit ='k');
      
                   if(isset($searchApi[$j]['hits']['hits'][$k]['sort'])){
                   $distance      = $searchApi[$j]['hits']['hits'][$k]['sort'][0];
                   } else {
                         $location = explode(",",$searchApi[$j]['hits']['hits'][$k]['_source']['location']);
                         $lat2 = $location[0];
                         $long2 = $location[1];
                         $distance = $this->distance($lat1, $long1, $lat2, $long2,$unit);
                          }
                   $getdistance   = round($distance,2);
                   
                             
               $total_review   = $this->gethandymanreview($handyman_id);
               
               $searchApi[$j]['hits']['hits'][$k]['_source']['total_review'] = count($total_review);
            
               $rating = $searchApi[$j]['hits']['hits'][$k]['_source']['Rating_by_Agent'];
               if($rating == "Good"){ $rating = 4.0;} 
               else if($rating == "Average"){ $rating = 3.0;} 
               else if($rating == "Exceptional"){ $rating = 5.0;} 
               else if($rating == "Bad"){ $rating = 2.0;} 
               else if($rating == "Very Bad"){ $rating = 1.0;}
               if(empty($searchApi[$j]['hits']['hits'][$k]['_source']['profile_photo'])){
                $searchApi[$j]['hits']['hits'][$k]['_source']['profile_photo'] = "user-31-03-2015-05-26-55pm-6ea9e5d18ea5839dae16a06d76c1fcf1.jpg";
               }
               $searchApi[$j]['hits']['hits'][$k]['_source']['rating'] = $rating; 
               $searchApi[$j]['hits']['hits'][$k]['_source']['distance'] = $getdistance." "."km";
               $searchApi[$j]['hits']['hits'][$k]['_source']['id'] = $handyman_id;
               if(isset($searchApi[$j]['hits']['hits'][$k]['_source']))  {
               $search[$j] = $searchApi[$j]['hits']['hits'][$k]['_source'];
                }
               unset($searchApi[$j]['hits']['hits'][$k]['_source']['action']);unset($searchApi[$j]['hits']['hits'][$k]['_source']['current_time']);unset($searchApi[$j]['hits']['hits'][$k]['_source']['date_of_collection']);unset($searchApi[$j]['hits']['hits'][$k]['_source']['time_of_data_collection']);unset($searchApi[$j]['hits']['hits'][$k]['_source']['Other_handyman_contact']);
               unset($searchApi[$j]['hits']['hits'][$k]['_source']['bank_account']);unset($searchApi[$j]['hits']['hits'][$k]['_source']['id_proof_no']);unset($searchApi[$j]['hits']['hits'][$k]['_source']['id_proof_photo']);unset($searchApi[$j]['hits']['hits'][$k]['_source']['handyman_previous_customers']);unset($searchApi[$j]['hits']['hits'][$k]['_source']['updatedate']);        
               unset($searchApi[$j]['hits']['hits'][$k]['_source']['Agent']);
               //$i = $i + 1;
               array_push($array, $searchApi[$j]['hits']['hits'][$k]['_source']);
              //print_r($search);
            }
          }
          // print_r($array);
            return $array;
            
  
  }
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
       $getLocationFromIpNo = $this->apis->getIp2Location($ipno);
       if($getLocationFromIpNo) {
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
    function Dot2LongIP ($Ipaddress) {
        if ($Ipaddress == "") {
            return 0;
        }
        else {
            $ips = explode(".", $Ipaddress);
            //print_r($ips);
            return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
       }
    }


    function searchservice($service,$area) {
print_r($area);
print_r($service);


    $client = new Elasticsearch\Client();
    $params['index'] = 'boloaaka';
    $params['type']  = 'handyman';
   $array = array();

    for ($i=0; $i <count($service['hits']['hits']) ; $i++) {
   
   
    
   if(is_string($area)){

    $params['body']['query']['bool']['must'] = array(
    array('match' => array('service_id' => $service['hits']['hits'][$i]['_id'])),
    array('match' => array('address' => $area)),
    array('match' => array('action' => 1)),
    );
    } else {
    $json =    '{
    "sort" : [
        {
      "_geo_distance" : {
          "location" : {
                "lat" : "'.$area['latitude'].'",
                "lon" : "'.$area['longitude'].'"
          }, 
          "order" : "asc",
          "unit" : "km"
      }
        }
    ],
    "query": {
      "filtered" : {

   "query": {
    "bool": {
      "must": [
        { "match": { "service_id":   "'.$service['hits']['hits'][$i]['_id'].'"}},
        { "match": { "action": 1   }}
      ]
    }
  },
    "filter" : {
        "geo_distance" : {
            "distance" : "'.$service['hits']['hits'][$i]['_source']['distance']."km".'",
            "location" : {
                "lat" : "'.$area['latitude'].'",
                "lon" : "'.$area['longitude'].'"
            }
        }
    }
      }
    }
  }';

    $params['body'] = $json;
    } 
//else {
  //$params['body']['query']['match']['service_id'] = $service['hits']['hits'][$i]['_id'];
//}

  
  $searchApielasticsearch = $client->search($params);
   array_push($array, $searchApielasticsearch);
  

}
print_r($array);
  return $array;

  }

  /* @desc : this function for add booking 
   * @param : array(category,time,description)
   * @return :  id
   */
   function booking($insert){
     $this->db->insert('booking',$insert);
     return $this->db->insert_id();
   }

function showhandyman(){
 $query = $this->db->get('user_handyman');
return $query->result_array();
   }

function deletehandymams(){
 $query = $this->db->get('handyman');
 return  $query->result_array();

 }

function updatelocation($location,$id){
      
      $this->db->where('id',$id);
      $this->db->update('handyman',$location);

   foreach ($location as $value) {
           $locations  = json_decode($value, true);
           $lat  = $locations['longitude'];
           $long = $locations['lattitude'];
           $location['location'] = $lat.",".$long;
	   print_r($locations['lattitude']);

    
    }

     $client = new Elasticsearch\Client();
     $indexParams['index']  = "boloaaka";
     $indexParams['type']   = "handyman";
     $indexParams['id']     = $id;
     $indexParams['body']['doc']   = $location;
     $sucess = $client->update($indexParams);
    print_r($sucess);

}
}
