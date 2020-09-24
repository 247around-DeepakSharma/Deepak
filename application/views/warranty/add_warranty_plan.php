<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<!--<script src="<?php echo base_url() ?>js/custom_js.js"></script>-->
<style type="text/css">
    .btn-group-sm>.btn, .btn-sm {padding:1px 5px !important}
    #tabs ul{
    margin:0px;
    padding:0px;
    
    }
    #tabs li{
    list-style: none;
    float: left;
    position: relative;
    top: 0;
    margin: 1px .2em 0 0;
    border-bottom-width: 0;
    padding: 0;
    white-space: nowrap;
    border: 1px solid #2c9d9c;
    background: #d9edf7 url(images/ui-bg_glass_75_e6e6e6_1x400.png) 50% 50% repeat-x;
    font-weight: normal;
    color: #555555;
    border-top-right-radius: 4px;
    border-top-left-radius: 4px;
    border-bottom: 0px;
    background-color: white;
    }
    #tabs button{
        
        align:center;
        font-weight: bold
    }
    #tabs a{
    float: left;
    padding: .5em 1em;
    text-decoration: none;
    }
    .col-md-12 {
    padding: 10px;
    }
    
    /* example styles for validation form demo */
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
    .err1{
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
    .vertical-align{
        vertical-align: middle;
        padding-top: 1%;
    }
    
    .error_message{
        display:none;
    }
</style>
<div id="page-wrapper">
    <div class="row">

        <?php
        if($this->session->flashdata('error')){
            echo "<p style ='text-align: center;background: #d9534f;'>".$this->session->flashdata('error')."</p>";
        }
        else if($this->session->flashdata('success')){
            echo "<p style ='text-align: center;background: #66ff66;'>".$this->session->flashdata('success')."</p>";
        }
        ?>
        <div id="container-1" class="panel-body form_container" style="display:block;padding-top: 0px;">
            <form name="myForm" class="form-horizontal" id ="booking_form" novalidate="novalidate" action="<?php echo base_url() ?>employee/warranty/save_warranty_plan" method="POST">
                <div  class = "panel panel-info">
                    <div class="panel-heading" style="background-color:#ECF0F1"><center><b>Add Warranty Plan</b></center></div>
                        <div class="panel-body">
                            
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('plan_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="plan_name" class="col-md-3">Plan Name*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" value="<?php echo set_value('plan_name');?>" id="plan_name" name="plan_name" placeholder="Plan Name">
                                            <?php echo form_error('plan_name'); ?>
                                            <p class="alert alert-danger error_message" id="plan_name_error">
                                                
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('partner')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="partner" class="col-md-3 vertical-align">Partner*</label>
                                        <div class="col-md-8">
                                            <select id="partner" class="form-control" name ="partner">
                                                <option value="0" selected>Select</option>
                                            </select>
                                            <?php echo form_error('partner'); ?>
                                            <p class="alert alert-danger error_message" id="partner_error">
                                                
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="col-md-12">
                                
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('warranty_type')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="state" class="col-md-3 vertical-align">Warranty Type*</label>
                                        <div class="col-md-8">
                                            <select id="warranty_type" class="warranty_type form-control" name ="warranty_type">
                                                <option value="1">In Warranty</option>
                                                <option value="2">Extended Warranty</option>
                                            </select>
                                            
                                            <?php echo form_error('warranty_type'); ?>
                                            <p class="alert alert-danger error_message" id="warranty_type_error">
                                                
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('service')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="service" class="col-md-3 vertical-align">Product*</label>
                                        <div class="col-md-8">
                                            <select id="service" class="form-control" name ="service" data-toggle="tooltip" data-placement="top" data-html="true">
                                                <option value="0" selected>Select</option>
                                            </select>
                                            <p>Please select product after selecting partner.</p>
                                            <?php echo form_error('service'); ?>
                                            <p class="alert alert-danger error_message" id="service_error">
                                                
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            
                            <div class="col-md-12">
                                <div class="col-md-6">
                                 <div  class="form-group <?php
                                    if (form_error('start_date')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="start_date" class="col-md-3 vertical-align">Start Date*</label>
                                    <div class="col-md-8 calender-input">
                                        <input id="start_date" class="form-control" value="<?php echo set_value('start_date');?>" name="start_date" type="text" value = "" max="<?=date('Y-m-d', strtotime('+ 10 years'));?>" autocomplete='off' onkeydown="return false">
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                        <?php echo form_error('start_date'); ?>
                                        <p class="alert alert-danger error_message" id="start_date_error">

                                        </p>
                                    </div>
                                </div>
                                </div>


                                <div class="col-md-6">
                                 <div  class="form-group <?php
                                    if (form_error('end_date')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="end_date" class="col-md-3 vertical-align">End Date*</label>
                                    <div class="col-md-8 calender-input">
                                        <input id="end_date" class="form-control" value="<?php echo set_value('end_date');?>" name="end_date" type="text" value = "" max="<?=date('Y-m-d', strtotime('+ 10 years'));?>" autocomplete='off' onkeydown="return false">
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                        <?php echo form_error('end_date'); ?>
                                        <p class="alert alert-danger error_message" id="end_date_error">

                                        </p>
                                    </div>
                                </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('warranty_period')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="warranty_period" class="col-md-3">Warranty Period in Months*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" value="<?php echo set_value('warranty_period');?>" id="warranty_period" name="warranty_period" placeholder="Warranty Period">
                                            <?php echo form_error('warranty_period'); ?>
                                            <p class="alert alert-danger error_message" id="warranty_period_error">

                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('warranty_grace_period')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="warranty_grace_period" class="col-md-3 vertical-align">Warranty Grace Period In Days*</label>
                                        <div class="col-md-8"> 
                                            <input  type="text" class="form-control blockspacialchar" value="<?php echo set_value('warranty_grace_period');?>" id="warranty_grace_period" name="warranty_grace_period" placeholder="Warranty Grace Period">
                                            <?php echo form_error('warranty_grace_period'); ?>
                                            <p class="alert alert-danger error_message" id="warranty_grace_period_error">

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-12">
                                
                                 
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('inc_svc_charge')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="service_charge" class="col-md-3 vertical-align">Inclusive Service Charge</label>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="service_charge" id="service_charge" value="1">
                                            
                                            <?php echo form_error('service_charge'); ?>
                                           
                                        </div>
                                    </div>
                                </div>
                                
                                 <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('inc_gas_charge')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="gas_charge" class="col-md-3 vertical-align">Inclusive Gas Charge</label>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="gas_charge" id="gas_charge" value="1">
                                            
                                            <?php echo form_error('gas_charge'); ?>
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('inc_transport_charge')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="transport_charge" class="col-md-3 vertical-align">Inclusive Transport Charge</label>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="transport_charge" id="transport_charge" value="1">
                                            
                                            <?php echo form_error('transport_charge'); ?>
                                           
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                            
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('state')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="state" class="col-md-3 vertical-align">State*</label>
                                        <div class="col-md-8">
                                            <select id="state" class="form-control" name ="state[]" multiple>
                                                <option value="0" selected>All</option>
                                            </select>
                                            <?php echo form_error('state'); ?>
                                            <p class="alert alert-danger error_message" id="state_error">
                                                
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('description')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="description" class="col-md-3 vertical-align">Description</label>
                                        <div class="col-md-8"> 
                                            <textarea class="form-control blockspacialchar" rows = "5" cols = "50" value="<?php echo set_value('description');?>" name = "description" id="description"></textarea>
                                            <?php echo form_error('description'); ?>
                                            <p class="alert alert-danger error_message" id="description_error">
                                                
                                            </p>
                                        </div>
                                    </div>
                                </div>


                                 <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('plan_depends_on')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="state" class="col-md-3 vertical-align">Plan Depends On*</label>
                                        <div class="col-md-8">
                                            <select id="plan_depends_on" class="plan_depends_on form-control" name ="plan_depends_on">
                                                <option value="1" <?php if($details[0]['plan_depends_on'] == 1) {echo "selected";} ?> >Model</option>
                                                <option value="2" <?php if($details[0]['plan_depends_on'] == 2) {echo "selected";} ?> >Product</option>
                                            </select>

                                            <?php echo form_error('plan_depends_on'); ?>
                                            <p class="alert alert-danger error_message" id="plan_depends_on">
                                            </p>
                            </div>

                            
                        </div>
                </div>
                
                    
                
                    <div class="clear clear_bottom">
                        <br>
                        <center><input type="submit" value="Save Plan Details" class="btn btn-primary" id="submit_btn">
                        <?php echo "<a class='btn btn-small btn-primary cancel' href=" . base_url() . "employee/warranty/warranty_plan_list>Cancel</a>"; ?>
                        </center>
<!--                    </div>-->
                </div>
            
        </form>
     </div>      
</div>
</div>
<!--Validations here-->
<?php if($this->session->userdata('checkbox')){$this->session->unset_userdata('checkbox');}?>
<!--Validation for page1-->
<script type="text/javascript">
   
       //Adding select 2 in Dropdowns
    $("#warranty_type").select2();
    $("#plan_depends_on").select2();
    $("#partner").select2();
    $("#service").select2();
    $("#state").select2();
    $("#start_date").datepicker({dateFormat: 'yy-mm-dd', maxDate: "<?=date('Y-m-d', strtotime('+ 10 years'));?>", changeYear: true, changeMonth: true});
    $("#end_date").datepicker({dateFormat: 'yy-mm-dd', maxDate: "<?=date('Y-m-d', strtotime('+ 10 years'));?>", changeYear: true, changeMonth: true});
    
    
    $(document).ready(function(){
       // $('#service').tooltip({'trigger':'focus', 'title': 'Please select product after selecting partner.'});
        //loading partner dropdown
        get_partner_list();
        
        //loading state dropdown
        get_state_list();
        
        //form validation starts here
        $('#submit_btn').click(function(){
            var error_found = 0;
            if(!$('#plan_name').val())
            {
                display_error("plan_name_error", "Please Enter Plan Name");
            }
            else
            {
                hide_error("plan_name_error");
            }   
            
            if(!$('#warranty_type').val())
            {
                display_error("warranty_type_error", "Please Select Warranty Type");
            }
            else
            {
                hide_error("warranty_type_error");
            }    
            if(!$('#plan_depends_on').val())
            {
                display_error("plan_depends_on_error", "Please Select Warranty Type");
            }
            else
            {
                hide_error("plan_depends_on_error");
            }    
            
            if(!$('#service').val() || $('#service').val() == 0)
            {
                display_error("service_error", "Please Select Service");
            }
            else
            {
                hide_error("service_error");
            }  
            
            if(!$('#partner').val() || $('#partner').val() == 0)
            {
                display_error("partner_error", "Please Select Partner");
            }
            else
            {
                hide_error("partner_error");
            } 
            
            if(!$('#state').val())
            {
                display_error("state_error", "Please Select State");
            }
            else
            {
                 hide_error("state_error");    
            }
            
            
            if(!$('#start_date').val())
            {
                display_error("start_date_error", "Please Select Start Date");
            }
            else
            {
                hide_error("start_date_error");
            } 
            
            if(!$('#end_date').val())
            {
                display_error("end_date_error", "Please Select End Date");
            }
            else
            {
                hide_error("end_date_error");
            } 
            
            //check whether start date is less than end date or not
            if($('#start_date').val() && $('#end_date').val())
            {
                if($('#end_date').val() < $('#start_date').val())
                {
                    display_error("end_date_error", "Start Date Cannot Be Greater Than End Date");
                }
            }
            
            
            if(!$('#warranty_period').val())
            {
                display_error("warranty_period_error", "Please Enter Warranty Period");
            }
            else
            {
                if(!isNaN($('#warranty_period').val()) && $('#warranty_period').val() >= 0)
                {
                    hide_error("warranty_period_error");
                }
                else
                {
                    display_error("warranty_period_error", "Please Enter Proper Warranty Period");
                }
                
            } 
            
            
            if(!$('#warranty_grace_period').val())
            {
                $(this).val(0);
            }
            else
            {
                if(!isNaN($('#warranty_grace_period').val()) && $('#warranty_grace_period').val() >= 0)
                {
                    hide_error("warranty_grace_period_error");
                }
                else
                {
                    display_error("warranty_grace_period_error", "Please Enter Proper Warranty Grace Period");
                }
                
            } 
            
            
            if(error_found == 1)
            {
                return false;
            }
            
            //function to show validation message
            function display_error(id, message)
            {
                $('#'+id).html(message);
                $('#'+id).css("display", "block");
                error_found = 1;
            }


            //function to hide validation message
            function hide_error(id)
            {
                $('#'+id).css("display", "none");
            }

                
        });
       
    });

//form validation ends here


//function to get partrner list in dropdown
function get_partner_list()
{
    $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/warranty/get_partner_list_dropdown',
       data: {}
     })
     .done (function(data) {
         $('#partner').append(data);
     })
     .fail(function(jqXHR, textStatus, errorThrown){
         alert("Something went wrong while loading partner list!");
      })
}

//function to load product list in dropdown on basis of partner selected
$('#partner').change(function(){
    var partner_id = $(this).val();
    $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/warranty/get_partner_service_list_dropdown',
       data: {partner_id : partner_id}
     })
     .done (function(data) {
         $('#service').html(data);
     })
     .fail(function(jqXHR, textStatus, errorThrown){
         alert("Something went wrong while loading partner service list!");
      })
})


//function to get state list in dropdown
function get_state_list()
{
    $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/warranty/get_state_list_dropdown',
       data: {}
     })
     .done (function(data) {
         $('#state').append(data);
     })
     .fail(function(jqXHR, textStatus, errorThrown){
         alert("Something went wrong while loading state list!");
      })
}

    
</script>
