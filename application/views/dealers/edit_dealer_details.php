<div id="page-wrapper">
    <div class="row">
        
        <div  class = "panel panel-info">
            <div class="panel-heading"> Edit Dealer</div>
            <div class="panel-body">
                <?php
                    if ($this->session->flashdata('success_msg')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('success_msg').'</strong>
                    </div>';
                    }
                    if ($this->session->flashdata('error_msg')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('error_msg').'</strong>
                    </div>';
                    }
                    ?>
                <form class="form-horizontal form-label-left" action="<?php echo base_url();?>employee/dealers/process_edit_dealer/<?php echo $dealer_details[0]['dealer_id']?>" name="dealerForm" method="post" novalidate autocomplete="off">
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="dealer_name">Dealer Name *</label>
                                <input type="text" placeholder="Enter Dealer Name" class="form-control" id="dealer_name" name="dealer_name" value = "<?php if(isset($dealer_details[0]['dealer_name'])){ echo $dealer_details[0]['dealer_name']; }  ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="dealer_phone_number_1">Dealer Mobile No *</label>
                                <input type="text" placeholder="Enter Dealer Mobile Number" class="form-control" id="dealer_phone_number_1" name="dealer_phone_number_1" value = "<?php if(isset($dealer_details[0]['dealer_phone_number_1'])){ echo $dealer_details[0]['dealer_phone_number_1']; }  ?>" required>
                            </div>
                        </div>
                         <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="dealer_email">Dealer Email </label>
                                <input type="email" placeholder="Enter Dealer Email" class="form-control" id="dealer_email" name="dealer_email" value = "<?php if(isset($dealer_details[0]['dealer_email'])){ echo $dealer_details[0]['dealer_email']; }  ?>" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="owner_name">Owner Name </label>
                                <input type="text" placeholder="Enter Owner Name" class="form-control" id="owner_name" name="owner_name" value = "<?php if(isset($dealer_details[0]['owner_name'])){ echo $dealer_details[0]['owner_name']; }  ?>" >
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="owner_phone_number_1">Owner Mobile No </label>
                                <input type="text" placeholder="Enter Owner Mobile Number" class="form-control" pattern="/^[6-9]{1}[0-9]{9}$/" id="owner_phone_number_1" name="owner_phone_number_1" value = "<?php if(isset($dealer_details[0]['owner_phone_number_1'])){ echo $dealer_details[0]['owner_phone_number_1']; }  ?>" >
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="owner_email">Owner Email </label>
                                <input type="email" placeholder="Enter Dealer Email" class="form-control" id="owner_email" name="owner_email" value = "<?php if(isset($dealer_details[0]['owner_email'])){ echo $dealer_details[0]['owner_email']; }  ?>" >
                            </div>
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="state">State *</label>
                                   <select name="state" id="state" class="form-control" required>
                                       <option selected disabled>Select State</option>
                                        <?php foreach ($state as $value) { ?> 
                                       <option value="<?php echo $value['state'];?>" <?php if($value['state'] === $dealer_details[0]['state']) { echo "selected";}?>><?php echo $value['state'];?></option>
                                        <?php } ?>   
                                 </select>
                            </div>
                        </div>  
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="city">City *</label>
                                 <select name="city" id="city" class="form-control" required>
                                     <option selected disabled>Select City</option>
                                        <?php foreach ($dealer_city_source['city'] as $value) { ?> 
                                       <option value="<?php echo $value['district'];?>" <?php if($value['district'] === $dealer_details[0]['city']) { echo "selected";}?>><?php echo $value['district'];?></option>
                                        <?php } ?>   
                                 </select>
                            </div>
                        </div>  
                        <div class="col-md-4 ">
                            <div class="form-group col-md-12">
                                <label for="partner_id">Partner *</label>
                                <select name="partner_id[]" id="partner_id" class="form-control" multiple required >
                                    <?php foreach ($dealer_city_source['sources'] as $value) { ?> 
                                        <option value="<?php echo $value['partner_id'];?>" <?php if(in_array($value['partner_id'], $dealer_partner_mapping_id)) { echo "selected";}?>><?php echo $value['source'];?></option>
                                    <?php } ?>  
                                </select>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-md-offset-5">
                            <input type="hidden" name="ini_delaer_partner_mapping" value="<?php echo implode(",", $dealer_partner_mapping_id) ;?>">
                            <button class="btn btn-success">Edit</button>
                        </div>
                        
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>
<?php if($this->session->userdata('success_msg')){$this->session->unset_userdata('success_msg');} ?>
<?php if($this->session->userdata('error_msg')){$this->session->unset_userdata('error_msg');} ?>
<script>
    $('#state').select2();
    $('#city').select2();
    $('#partner_id').select2();
</script>