<style>
    #hsn_code_list_table_filter{
        text-align: right;
    }
    
    #edit_section{
        display: none;
    }
    
    #edit_service_id {
        background: #eee;
        pointer-events: none;
        touch-action: none;
    }
    
</style>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<div id="page-wrapper" >
    <div class="container-fluid" >
        <?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:20;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('success') . '</strong>
                        </div>';
            }
       
        if ($this->session->userdata('error')) { ?>
            <div class="alert alert-danger">
                <strong><?php echo $this->session->userdata('error'); ?></strong>  
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php }  ?>
        <span id="add_details_status" style="margin-left: 550px; font-size: 15px;font-weight: bold;"></span>
        <div class="panel panel-info" style="margin-top:20px;">            
            <div class="panel-heading">
                <h4>HSN Code Details</h4>
            </div>
                <div class="panel-body">   
                    <div class="row"> 
                    <div id="add_section" style="display: <?php if(!empty($action_flag)){ echo 'none'; } ?>">
                        <form name="add_hsn_code" method="post" class="form-horizontal" action="<?php echo base_url() ?>employee/invoice/process_add_new_hsncode">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><b>Add HSN Code</b></div>
                                    <div class="panel-body">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label for="service_id" class="col-md-4">Appliance *</label>
                                                <div class="col-md-8">
                                                    <select name="service_id" id="service_id" class="form-control" readonly="" tabindex="-1">
                                                        <option disabled="" selected="">Select Appliance</option>
                                                        <?php foreach ($services_list as $val) { ?>
                                                            <option value="<?php echo trim($val['id']); ?>"><?php echo trim($val['services']); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="public_name" class="col-md-4">GST Rate *</label>
                                                <div class="col-md-8">
                                                   <!-- <input type="text" class="form-control" name="gst_rate" id="gst_rate" placeholder="Enter GST Rate"> -->
                                                    <select class="form-control" id="gst_rate" name="gst_rate" required="">
                                                        <option disabled="" selected="">GST Rate</option>
                                                        <?php foreach( GST_NUMBERS_LIST as $gstrate => $gstval) { ?>
                                                            <option value="<?php echo $gstrate; ?>"><?php echo $gstval; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div style="margin-bottom: 25px;" class="form-group ">
                                                <label for="state" class="col-md-4">HSN Code *</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="hsn_code" id="hsn_code" placeholder="Enter HSN Code"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required="">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div style="text-align: center;">
                                        <input type="hidden" name="agent_id" value="<?php echo $this->session->userdata('id'); ?>" />
                                        <input  id="form_submit" type= "submit"  class="btn btn-primary btn-lg"  value ="Submit" >
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="edit_section" style="display: <?php  if(!empty($action_flag)){ echo 'block'; } ?>">
                        <?php if(!empty($hsn_code_list)){ ?>
                        <form name="add_hsn_code" method="post" class="form-horizontal" action="<?php echo base_url() ?>employee/invoice/process_edit_new_hsncode">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><b>Edit HSN Code</b></div>
                                    <div class="panel-body">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label for="service_id" class="col-md-4">Appliance *</label>
                                                <div class="col-md-8">
                                                    <select name="service_id" id="edit_service_id" class="form-control" readonly="" tabindex="-1">
                                                        <option disabled="" selected="">Select Appliance</option>
                                                        <?php foreach ($services_list as $val) { ?>
                                                        <option <?php if($val['id'] == $hsn_code_list['service_id']){  echo 'selected'; } ?> value="<?php echo trim($val['id']); ?>"><?php echo trim($val['services']); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <label for="public_name" class="col-md-4">GST Rate *</label>
                                                <div class="col-md-8">
                                                    <!--<input type="text" class="form-control" name="gst_rate" id="edit_gst_rate" value="<?php if(!empty($hsn_code_list['gst_rate'])){ echo $hsn_code_list['gst_rate']; } ?>" placeholder="Enter GST Rate">-->
                                                    <select class="form-control" id="gst_rate" name="gst_rate" required="">
                                                        <option disabled="" selected="">GST Rate</option>
                                                        <?php foreach( GST_NUMBERS_LIST as $gstrate => $gstval) { ?>
                                                        <option value="<?php echo $gstrate; ?>" <?php if($hsn_code_list['gst_rate'] == $gstrate){ echo 'selected'; } ?>><?php echo $gstval; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div style="margin-bottom: 25px;" class="form-group ">
                                                <label for="state" class="col-md-4">HSN Code *</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="hsn_code" id="edit_hsn_code" value="<?php if(!empty($hsn_code_list['hsn_code'])){ echo $hsn_code_list['hsn_code']; } ?>" placeholder="Enter HSN Code"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" required="">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div style="text-align: center;">
                                        <input type="hidden" name="agent_id" value="<?php echo $this->session->userdata('id'); ?>" />
                                        <input type="hidden" name="edit_id" value="<?php echo $hsn_code_list['id']; ?>" />
                                        <input  id="edit_form_submit" type= "submit"  class="btn btn-primary btn-lg"  value ="Submit" >
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </form>
                        <?php } ?>
                        </div>
                        <div class="col-md-12" >
                            <h5><strong>HSN Code List</strong></h5>
                            <hr>
                            <table class="table priceList table-striped table-bordered" id="hsn_code_list_table">                       
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Services</th>   
                                        <th>HSN Code</th>                                        
                                        <th>GST Rate</th>
                                        <th>Created Date</th>  
                                        <th>Edit</th>
                                        <th width="100">Action</th>
                                    </tr>
                                </thead>
                            </table>                            
                        </div>
                    </div>
                </div>
           
        </div>
    </div>
</div>
<?php 
    $this->session->unset_userdata('success');
    $this->session->unset_userdata('error');
?>
<script>
    
    $('#service_id').select2({      
           placeholder:'Select Appliance',      
           allowClear: true ,
           width: '100%'
    });    
    
    
    $("#form_submit").click(function(){
        var textVal = $("#gst_rate").val();        
        var hsnTextVal = $("#hsn_code").val();
        var service_id = $("#service_id").val();
        
        if(service_id == '' || service_id == null){
            alert("Please Select Appliance."); 
            return false;
        }
        
        
         if(hsnTextVal == '' || hsnTextVal == null){
            alert("Please Enter HSN code."); 
            return false;
        }
        
        if(hsnTextVal.length > 8){
            $("#hsn_code").val('');
            alert("HSN code length should not be greater than eight digit."); 
            return false;
        }
            
        var regex = /^[0-9\s]*$/;
        if(!regex.test(hsnTextVal)){
            alert("HSN Code Should Be Numeric.");
            return false;
         } 
    });
    
    
    
    
     $("#edit_form_submit").click(function(){
        var textVal = $("#edit_gst_rate").val();        
        var hsnTextVal = $("#edit_hsn_code").val();
        var service_id = $("#edit_service_id").val();
        
        if(service_id == '' || service_id == null){
            alert("Please Select Appliance."); 
            return false;
        }
        
         if(hsnTextVal == '' || hsnTextVal == null){
            alert("Please Enter HSN code."); 
            return false;
        }
        
        if(hsnTextVal.length > 8){
            $("#edit_hsn_code").val('');
            alert("HSN code length should not be greater than eight digit."); 
            return false;
        }
            
        var regex = /^[0-9\s]*$/;
        if(!regex.test(hsnTextVal)){
            alert("HSN Code Should Be Numeric.");
            return false;
         }
          
    });
    
    
    function process_to_manage_status(hsncode_id){
            $.ajax({
                url: '<?php echo base_url() ?>employee/invoice/process_manage_hsncode_status',
                type: 'post',
                dataType: 'json',
                data: {hsncode_id : hsncode_id},
                success: function (data) {
                    if(data['status']=='success'){
                       $("#add_details_status").html("Successfully").css({'color':'green'});
                       location.reload();
                    }else{
                       $("#add_details_status").html(data['status']).css({'color':'red'}); 
                       $('#add_hsn_code').trigger("reset");
                    }
                }
            });
    }
        
    var time = moment().format('D-MMM-YYYY');
    
    get_hsn_code_details();
    
    function get_hsn_code_details(){
    
      hsn_code_list_table = $('#hsn_code_list_table').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'Blfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4]
                    },
                    title: 'hsn_code_details_'+time,
                    action: newExportAction
                },
            ],
            "language": {
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "emptyTable":     "No Data Found"
            },
            
            "order": [[ 4, "desc" ]],
            "pageLength": 50,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ajax": {
                url: "<?php echo base_url(); ?>employee/inventory/get_hsn_code_details",
                type: "POST",
                data:{ satus :'HSN CODE DETAIL'}
            }
            ,
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3, 6, 5], //first column / numbering column
                    "orderable": false //set not orderable
                },
                 {
                  "targets": [4], //first column / numbering column
                    "orderable": true //set not orderable
                }
            ],
        });
        
      }
        
    /* Export Records That Is Showing In View Pages */
    
    var newExportAction = function (e, hsn_code_list_table, button, config) {
        var self = this;
        var oldStart = hsn_code_list_table.settings()[0]._iDisplayStart;
        hsn_code_list_table.one('preXhr', function (e, s, data) {
            data.start = 0;
            data.length = hsn_code_list_table.page.info().recordsTotal;
            hsn_code_list_table.one('preDraw', function (e, settings) {
                oldExportAction(self, e, hsn_code_list_table, button, config);
                hsn_code_list_table.one('preXhr', function (e, s, data) {
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                setTimeout(hsn_code_list_table.ajax.reload, 0);

                return false;
            });
        });
    };
    
</script>
