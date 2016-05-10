<?php $offset = $this->uri->segment(3); ?>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php if($this->session->userdata('error'))  {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                    }
                    ?>
                <?php if($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                    }
                    ?>
                <?php if (isset($handyman_id)) {
                    foreach ($handyman_id as $key => $data) 
                         $handyman = $data['id'];
                        
                    
                    }?>
                </h1>
                <h1 class="page-header">
                    <img src="https://d28hgh2xpunff2.cloudfront.net/vendor-320x252/<?php echo $data['profile_photo'] ; ?>" class="img-circle  "  style="width:80px; height:80px;">  <?php if (isset($data['name'])) {echo ucfirst($data['name']); }?><!-- <small><a href="<?php echo base_url()?>handyman/viewhandyman/<?php echo $off;?>" class="btn btn-success pull-right">Back</a></small> -->
                </h1>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class=" col-md-3 <?php if(isset($_GET['tab']) && $_GET['tab'] == 'home') {echo "active" ;} else if($this->uri->segment(2) == 'updateHandyman') {echo 'active' ;}   ?>"><a href="#home" aria-controls="home" role="tab" data-toggle="tab" style="text-align:center">Home</a></li>
                    <li role="presentation " class="col-md-3 <?php if(isset($_GET['tab']) && $_GET['tab'] == 'upload') {echo "active" ;}?>"><a href="#upload" aria-controls="upload" role="tab" data-toggle="tab" style="text-align:center"> Upload Image</a></li>
                    <li role="presentation" class="col-md-3 <?php if(isset($_GET['tab']) && $_GET['tab'] == 'identity') {echo "active" ;}?>"><a href="#identity" aria-controls="identity" role="tab" data-toggle="tab" style="text-align:center">Identity Proof</a></li>
                    <li role="presentation" class="col-md-3 <?php if(isset($_GET['tab']) && $_GET['tab'] == 'agent') {echo "active" ;}?>"><a href="#agent" aria-controls="agent" role="tab" data-toggle="tab" style="text-align:center">Agent details</a></li>
                    <li role="presentation" class="col-md-3 <?php if(isset($_GET['tab']) && $_GET['tab'] == 'price') {echo "active" ;}?>"><a href="#price" aria-controls="price" role="tab" data-toggle="tab" style="text-align:center">Pricing</a></li>
                    <li role="presentation" class="col-md-3 <?php if(isset($_GET['tab']) && $_GET['tab'] == 'certification') {echo "active" ;}?>"><a href="#certification" aria-controls="certification" role="tab" data-toggle="tab" style="text-align:center">Certification</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'home') {echo 'in active' ;} else if($this->uri->segment(2) == 'updateHandyman') {echo 'in active' ;} ?>" id="home">
                        <div class="container" style="margin-top:5%;">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form class="form-horizontal" action="<?php echo base_url()?>handyman/updateHandyman/<?php echo $handyman ?>/<?php echo $off;?>" method="POST" >
                                        <div class="form-group <?php if( form_error('name') ) { echo 'has-error';} ?>">
                                            <label for="Name" class="col-md-2">Name</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="name"  value = "<?php if (isset($data['name'])) {echo $data['name']; }?>"  >
                                            </div>
                                            <?php echo form_error('name'); ?>
                                        </div>
                                        <div class="form-group <?php if( form_error('phone') ) { echo 'has-error';} ?>">
                                            <label for="Phone" class="col-md-2">phone</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="phone"  value = "<?php if (isset($data['phone'])) {echo $data['phone']; }?>" >
                                                <?php echo form_error('phone'); ?>
                                            </div>
                                        </div>
                                        <!-- <div class="form-group">
                                            <label for="Service" class="col-md-2">Service</label>
                                            <div class="col-md-6">
                                               <select class="js-example-basic-multiple form-control" name ="service[]"   multiple="multiple"  required>
                                                  <?php function isJson($string)
                                                {
                                                  json_decode($string);
                                                
                                                      // check if error occurred
                                                      $isValid = json_last_error() === JSON_ERROR_NONE;
                                                   
                                                      return $isValid ;
                                                     }
                                                  ?>
                                                  <?php 
                                                foreach ($GetAllServicesInfo as $GetAllServices) {     
                                                ?>
                                                  <option <?php 
                                                if(!empty($data['service'])){
                                                 if (isJson($data['service'])) {
                                                
                                                $service = json_decode($data['service'],true);
                                                 foreach ($service as $services) {
                                                        if($GetAllServices['services'] == $services)
                                                          { echo "selected = ''"; }
                                                 }
                                                  } else {
                                                    if($GetAllServices['services'] == $data['service'])
                                                          { echo "selected = ''"; }
                                                
                                                  }
                                                } ?> >
                                                     <?php echo $GetAllServices['services'];?>
                                                  </option>
                                                  <?php } ?>
                                               </select>
                                            </div>
                                            </div>-->
                                        <div class="form-group  <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                                            <label for="Service" class="col-md-2">Service</label>
                                            <div class="col-md-6">
                                                <select class="js-example-basic-multiple form-control" name ="service_id"    placeholder ="Service Required"  >
                                                    <option  disabled> -------------Select Service Any One------------------</option>
                                                    <?php 
                                                        foreach ($GetAllServicesInfo as $GetAllServices) {     
                                                        ?>
                                                    <option value = "<?php echo $GetAllServices['id']?>" <?php 
                                                        if (isset($data['service_id'])) {
                                                        
                                                                 if($GetAllServices['id'] == $data['service_id'])
                                                                   { echo "selected = ''"; }
                                                          }?> >
                                                        <?php echo $GetAllServices['services'];?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                                <?php echo form_error('service_id'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group <?php if( form_error('experience') ) { echo 'has-error';} ?>">
                                            <label for="experience" class="col-md-2">Experience</label>
                                            <div class="col-md-6">
                                                <select  name="experience" class="js-example-basic-multiple form-control">
                                                    <option <?php if($data['experience'] == '0') { echo "selected";}?>>0</option>
                                                    <option <?php if($data['experience'] == '1') { echo "selected";}?>>1</option>
                                                    <option <?php if($data['experience'] == '2') { echo "selected";}?>>2</option>
                                                    <option <?php if($data['experience'] == '3') { echo "selected";}?>>3</option>
                                                    <option <?php if($data['experience'] == '4') { echo "selected";}?>>4</option>
                                                    <option <?php if($data['experience'] == '5') { echo "selected";}?>>5</option>
                                                    <option <?php if($data['experience'] == '6') { echo "selected";}?>>6</option>
                                                    <option <?php if($data['experience'] == '7') { echo "selected";}?>>7</option>
                                                    <option <?php if($data['experience'] == '8') { echo "selected";}?>>8</option>
                                                    <option <?php if($data['experience'] == '9') { echo "selected";}?>>9</option>
                                                    <option <?php if($data['experience'] == '10') { echo "selected";}?>>10</option>
                                                    <option <?php if($data['experience'] == '11') { echo "selected";}?>>11</option>
                                                    <option <?php if($data['experience'] == '12') { echo "selected";}?>>12</option>
                                                    <option <?php if($data['experience'] == '13') { echo "selected";}?>>13</option>
                                                    <option <?php if($data['experience'] == '14') { echo "selected";}?>>14</option>
                                                    <option <?php if($data['experience'] == '15') { echo "selected";}?>>15</option>
                                                    <option <?php if($data['experience'] == '16') { echo "selected";}?>>16</option>
                                                    <option <?php if($data['experience'] == '17') { echo "selected";}?>>17</option>
                                                    <option <?php if($data['experience'] == '18') { echo "selected";}?>>18</option>
                                                    <option <?php if($data['experience'] == '19') { echo "selected";}?>>19</option>
                                                    <option <?php if($data['experience'] == '20') { echo "selected";}?>>20</option>
                                                    <option <?php if($data['experience'] == '21') { echo "selected";}?>>21</option>
                                                    <option <?php if($data['experience'] == '22') { echo "selected";}?>>22</option>
                                                    <option <?php if($data['experience'] == '23') { echo "selected";}?>>23</option>
                                                    <option <?php if($data['experience'] == '24') { echo "selected";}?>>24</option>
                                                    <option <?php if($data['experience'] == '25') { echo "selected";}?>>25</option>
                                                    <option <?php if($data['experience'] == '>25') { echo "selected";}?>>>25</option>
                                                    <option <?php if (isset($data['experience'])) {echo "selected"; }?>> <?php echo $data['experience']; ?></option>
                                                </select>
                                                <?php echo form_error('experience'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group <?php if( form_error('age') ) { echo 'has-error';} ?>">
                                            <label for="Age" class="col-md-2">Age</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="age" value = "<?php if (isset($data['age'])) {echo $data['age']; }?>"  >
                                                <?php echo form_error('age'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="Address" class="col-md-2">Address</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="address"   value = "<?php if (isset($data['address'])) {echo $data['address']; }?>">
                                            </div>
                                        </div>
                                        <div class="form-group <?php if( form_error('location') ) { echo 'has-error';} ?>">
                                            <label for="location" class="col-md-2">location</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="location" value = "<?php if (isset($data['location'])) { $loc = json_decode($data['location'],true);   $string = $loc['lattitude'].'|'.$loc['longitude']; echo preg_replace('/\s+/', '', $string);  }?>" placeholder = "Latittude|Longitude" >
                                                <?php echo form_error('location'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="Vendors Area of Operation" class="col-md-2">Vendors Area of Operation</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"  name="vendors_area_of_operation" value = "<?php if (isset($data['vendors_area_of_operation'])) {echo $data['vendors_area_of_operation']; }?>"  placeholder ="Vendors Area Operation">
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="Rating by Agent" class="col-md-2">Rating by Agent</label>
                                            <div class="col-md-6">
                                                <!-- <input type="text" class="form-control"  name="rating_by_agent" value = "<?php if (isset($data['Rating_by_Agent'])) {echo $data['Rating_by_Agent']; }?>" placeholder ="Rating " > -->
                                                <select  name="rating_by_agent" class="js-example-basic-multiple form-control">
                                                    <option disabled>Select Rating
                                                    <option>
                                                    <option <?php if($data['Rating_by_Agent'] == 'Good') { echo "selected";}?>>Good</option>
                                                    <option <?php if($data['Rating_by_Agent'] == 'Average') { echo "selected";}?>>Average</option>
                                                    <option <?php if($data['Rating_by_Agent'] == 'Exceptional') { echo "selected";}?>>Exceptional</option>
                                                    <option <?php if($data['Rating_by_Agent'] == 'Bad') { echo "selected";}?>>Bad</option>
                                                    <option <?php if($data['Rating_by_Agent'] == 'Very Bad') { echo "selected";}?>>Very Bad</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group <?php if( form_error('bank_account_no') ) { echo 'has-error';} ?> "   >
                                            <label for="Bank Account No" class="col-md-2">Bank Account Number</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"   name="bank_account_no" value = "<?php if (isset($data['bank_ac_no'])) {echo $data['bank_ac_no']; }?>"  > 
                                            </div>
                                            <?php echo form_error('bank_account_no'); ?>
                                        </div>
                                        <!--   <div class="form-group">
                                            <label for="is_bank account" >
                                            <input type="checkbox" name="bank_account"  id="check" <?php if(isset($data['bank_account'])) { if($data['bank_account'] == "1" ){ echo "checked" ; } } ?> value ="1" > Is Bank Account
                                            </label> 
                                            </div> -->
                                        <div class="form-group "   >
                                            <label for="Amount Paid" class="col-md-2">Amount Paid</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control"   name="paid" value = "<?php if (isset($data['is_paid'])) {echo $data['is_paid']; }?>"  placeholder ="XXXXXX"> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="Passport" >
                                            <input type="checkbox" name="passport" <?php if(isset($data['passport'])) { if($data['passport'] == "Yes" ){ echo "checked" ; } } ?> value ="Yes"> Passport
                                            </label>
                                            <label for="marital status" >
                                            <input type="checkbox" name="married" <?php if(isset($data['marital_status'])) { if($data['marital_status'] == "Married" ){ echo "checked" ; } } ?> value ="Married"> Married 
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="when you work prefer" >
                                            when you Prefer work 
                                            <input type="checkbox" name="work_on_weekdays" <?php if(isset($data['work_on_weekdays'])) { if($data['work_on_weekdays'] == "Yes" ){ echo "checked" ; } } ?> value ="Yes">  weekdays
                                            </label>
                                            <input type="checkbox" name="weekends" <?php if(isset($data['works_on_weekends'])) { if($data['works_on_weekends'] == "Yes" ){ echo "checked" ; } } ?> value ="Yes">  weekends
                                        </div>
                                        <div class="form-group">
                                            <label for="Service On call" >
                                            Service On Call
                                            <input type="checkbox" name="service_on_call"  <?php if(isset($data['service_on_call'])) { if($data['service_on_call'] == "Yes" ){ echo "checked" ; } } ?>  value ="Yes">  Yes
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="Service On call" >
                                            Is Disable &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" name="is_disabled"  <?php if(isset($data['is_disabled'])) { if($data['is_disabled'] == "Yes" ){ echo "checked" ; } } ?>  value ="Yes">  Yes
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="Service On call" >
                                            Police Verification &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="checkbox" name="police_verification"  <?php if(isset($data['police_verification'])) { if($data['police_verification'] == "Yes" ){ echo "checked" ; } } ?>  value ="Yes">  Yes
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-10">
                                                <center><a href="<?php echo base_url()?>handyman/viewhandyman/<?php echo $off;?>" class="btn btn-success btn-lg" style="margin: 2%;
                                                    margin-left: -17%;">Back</a><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:17%"></center>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'upload') {echo 'in active' ;}?>" id="upload" style="margin-top:5%">
                        <form class="form-horizontal" action="<?php echo base_url()?>handyman/uploadphoto/<?php if(isset($handyman_id)) {echo $handyman ; }?>/<?php echo $off;?>" method ="POST" enctype="multipart/form-data">
                            <fieldset <?php if(!isset($handyman_id)) { echo 'disabled';}?>>
                                <div class="form-group  ">
                                    <label for="Profile_photo" class="col-md-2">Profile Photo</label>
                                    <div class="col-md-6">
                                        <input  class="form-control"  type="file" name="file" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <center> <input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'identity') {echo 'in active' ;}?>" id="identity" style="margin-top:5%">
                        <form class="form-horizontal" action="<?php echo base_url()?>handyman/identity/<?php if(isset($handyman_id)) {echo $handyman ; } ?>/<?php echo $off;?>"  method ="POST" enctype="multipart/form-data">
                            <fieldset <?php if(!isset($handyman_id)) { echo 'disabled';}?>>
                                <div class="form-group <?php if( form_error('id_proof_name') ) { echo 'has-error';} ?>" >
                                    <label for="ID Proof Name" class="col-md-2">ID Proof Name</label>
                                    <div class="col-md-6">
                                        <select class="js-example-basic-multiple form-control" name ="id_proof_name" required>
                                            <option disabled>------Select Any Identity -----
                                            <option>
                                            <option <?php if (isset($data['id_proof_name'])) {if($data['id_proof_name'] == "Pan Card"){ echo "selected" ;} }?>>Pan Card</option>
                                            <option <?php if (isset($data['id_proof_name'])) {if($data['id_proof_name'] == "Driving Licence"){ echo "selected" ;} }?>>Driving Licence</option>
                                            <option <?php if (isset($data['id_proof_name'])) {if($data['id_proof_name'] == "Passport"){ echo "selected" ;} }?>>Passport</option>
                                            <option <?php if (isset($data['id_proof_name'])) {if($data['id_proof_name'] == "Voter ID Card"){ echo "selected" ;} }?>>Voter ID Card</option>
                                            <option <?php if (isset($data['id_proof_name'])) {if($data['id_proof_name'] == "Govt. Issued Identity Card"){ echo "selected" ;} }?>>Govt. Issued Identity Card</option>
                                            <option <?php if (isset($data['id_proof_name'])) {if($data['id_proof_name'] == "Bank Passbook"){ echo "selected" ;} }?>>Bank Passbook</option>
                                            <option <?php if (isset($data['id_proof_name'])) {if($data['id_proof_name'] == "Aadhar Card"){ echo "selected" ;} }?>>Aadhar Card</option>
                                            <option <?php if (isset($data['id_proof_name'])) {if($data['id_proof_name'] == "Credit Card"){ echo "selected" ;} }?>>Credit Card</option>
                                        </select>
                                        <?php echo form_error('id_proof_name'); ?>
                                    </div>
                                </div>
                                <div class="form-group <?php if( form_error('id_proof_no') ) { echo 'has-error';} ?>">
                                    <label for="ID Proof No" class="col-md-2">ID Proof Number</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="id_proof_no" value = "<?php if (isset($data['id_proof_no'])) {echo $data['id_proof_no']; }?>"  required> 
                                    </div>
                                    <?php echo form_error('id_proof_no'); ?>
                                </div>
                                <div class="form-group <?php if( form_error('file') ) { echo 'has-error';} ?>">
                                    <label for="ID Proof Photo" class="col-md-2">ID Proof Photo</label>
                                    <div class="col-md-6">
                                        <input  class="form-control"  type="file" name="file" >
                                    </div>
                                    <?php if( form_error('file') ) { echo 'File size or file type is not supported.Allowed extentions are "png", "jpg", "jpeg". Maximum file size is 2MB';} ?>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'agent') {echo 'in active' ;}?>" id="agent" style="margin-top:5%">
                        <form class="form-horizontal" action="<?php echo base_url()?>handyman/agent/<?php if(isset($handyman_id)) {echo $handyman ; } ?>/<?php echo $off;?>" method="POST">
                            <fieldset <?php if(!isset($handyman_id)) { echo 'disabled';}?>>
                                <div class="form-group ">
                                    <label for="Agent name" class="col-md-2">Agent Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="agent_name" value = "<?php if (isset($data['Agent'])) {echo $data['Agent']; }?>"  >
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="Date of collection" class="col-md-2">Date of collection</label>
                                    <div class="col-md-6">
                                        <div class='input-group date' >
                                            <input type="text" class="form-control"  name="date_of_collection" value = "<?php if (isset($data['date_of_collection'])) { echo $data['date_of_collection']; }?>" >
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="Time of data collection" class="col-md-2">Time of data collection</label>
                                    <div class="col-md-6">
                                        <div class='input-group date' id='datetime'>
                                            <input type='text' class="form-control" name = "time_of_data_collection" value = "<?php if (isset($data['time_of_data_collection'])) { echo $data['time_of_data_collection']; }?>"  />
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <script type="text/javascript">
                                            $(function () {
                                            $('#datetime').datetimepicker({
                                            
                                            });
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="Handyman's Previous Customers" class="col-md-2">Handyman's Previous Customers</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="handyman_previous_customers" value = "<?php if (isset($data['handyman_previous_customers'])) {echo $data['handyman_previous_customers']; }?>"  >
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="Other Handyman's contact" class="col-md-2">Other Handyman's contact</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="other_handyman_contact" value = "<?php if (isset($data['Other_handyman_contact'])) {echo $data['Other_handyman_contact']; }?>"  >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:30%"></center>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane  <?php if(isset($_GET['tab']) && $_GET['tab'] == 'price') {echo 'in active' ;}?>"  id="price" style="margin-top:5%">
                        <?php $price =  json_decode($data['pricing'],true);?>
                        <form class="form-horizontal"  action="<?php echo base_url()?>handyman/pricing/<?php if(isset($handyman_id)) {echo $handyman ; } ?>/<?php echo $off;?>" method="POST" id="myform" >
                            <?php if(!empty($price['service'])){for($i=0;$i<count($price['service']);$i++){?>
                            <div class="repeatingSection form-group <?php if( form_error('service[]') ) { echo 'has-error';} ?> <?php if( form_error('price[]') ) { echo 'has-error';} ?> ">
                                <label for="service" class="col-md-1">Service</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="service" name="service[]" value="<?php echo $price['service'][$i] ?>" >
                                    <?php echo form_error('service[]'); ?>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="price" name="price[]" value="<?php echo $price['price'][$i] ?>" placeholder="Price"  >
                                    <?php echo form_error('price[]'); ?>
                                </div>
                                <div class="col-md-3  deleteprice">
                                    <button class="btn btn-small btn-info btn-sm">Delete</button>
                                </div>
                            </div>
                            <?php }} else{ ?>
                            <div class="repeatingSection form-group <?php if( form_error('service[]') ) { echo 'has-error';} ?> <?php if( form_error('price[]') ) { echo 'has-error';} ?> ">
                                <label for="service" class="col-md-1">Service</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="service" name="service[]"   >
                                    <?php echo form_error('service[]'); ?>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="price" name="price[]"  placeholder="Price"  >
                                    <?php echo form_error('price[]'); ?>
                                </div>
                                <div class="col-md-3  deleteprice">
                                    <button class="btn btn-small btn-info btn-sm">Delete</button>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="formRow formRowRepeatingSection text-center">
                                <button class="addprice btn btn-small btn-primary btn-sm">Add </button>  <input type="submit" class="btn btn-small btn-default btn-sm" value="Save " />
                            </div>
                        </form>
                    </div>
<div role="tabpanel" class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab'] == 'certification') {echo 'in active' ;}?>" id="certification" style="margin-top:5%">
                       <form class="form_horizontal" action = "<?php echo base_url()?>handyman/addcertification/<?php if(isset($handyman_id)) {echo $handyman ; } ?>/<?php echo $off;?>" method="POST">
                           <fieldset <?php if(!isset($handyman_id)) { echo 'disabled';}?>>
                                 <div class="form-group">
                                    <label class="col-md-2">Add Certification</label>
                                    <div class="col-md-4">
                                        <input type="text" name="certification1" class="form-control" placeholder="Certification1" value="<?php if (isset($data['certification1'])) {echo $data['certification1']; }?>" >
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="certification2" class="form-control" placeholder="Certification2" value="<?php if (isset($data['certification2'])) {echo $data['certification2']; }?>">
                                    </div>
                                 </div>
                                 <br/> <br/><br/>
                                 <div class="form-group">
                                    <div class="col-md-10">
                                        <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                                    </div>
                                </div>
                            </fieldset>
                           </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!--wrapper-->
</div>
<!--wrapper-->
<script>
    $(".js-example-basic-multiple").select2();
</script>
<style>
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
    background-color: #13c5FF;
    color:#fff;
    }
</style>
<link href="<?php echo base_url()?>css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="<?php echo base_url();?>js/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url();?>js/moment.js"></script>
<script src="<?php echo base_url();?>js/moment-with-locales.js"></script>
</body>
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('error'); ?>
</html>
<script>
    // Add a new repeating section
    var attrs = ['for', 'id', 'name'];
    function resetAttributeNames(section) { 
     var tags = section.find('input, label'), idx = section.index();
     tags.each(function() {
       var $this = $(this);
       $.each(attrs, function(i, attr) {
         var attr_val = $this.attr(attr);
         if (attr_val) {
             $this.attr(attr, attr_val.replace(/_\d+$/, '_'+(idx + 1)))
         }
       })
     })
    }
                    
    $('.addprice').click(function(e){
         e.preventDefault();
         var lastRepeatingGroup = $('.repeatingSection').last();
         var cloned = lastRepeatingGroup.clone(true)  
         cloned.insertAfter(lastRepeatingGroup);
         resetAttributeNames(cloned)
     });
                     
    // Delete a repeating section
    $('.deleteprice').click(function(e){
         e.preventDefault();
         var current_fight = $(this).parent('div');
         var other_fights = current_fight.siblings('.repeatingSection');
         if (other_fights.length === 0) {
             alert("You should add atleast one Price");
             return;
         }
         current_fight.slideUp('slow', function() {
             current_fight.remove();
             
             // reset fight indexes
             other_fights.each(function() {
                resetAttributeNames($(this)); 
             })  
             
         })
         
             
     });
    
    
    
    
</script>
<style>
    
    .repeatingSection { 
    padding: 20px;
    margin: 20px;
    border-bottom: 1px solid #ddd  
    }
    .addprice{
    margin-left: -688px;
    margin-right: 36px;
    }
    .formRow {
    padding: 20px;
    background: #ddd; 
    margin: 20px 0 0 0;    
    }
</style>
