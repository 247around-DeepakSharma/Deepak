
<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading">Defective Spare Invoice</div>
            <div class="panel-body">
            <div class="row">
                 <?php
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                }
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                }
                ?>
                <form name="myForm" class="form-horizontal" id ="spare_form" novalidate="novalidate" action="<?php echo base_url()?>employee/spare_parts/process_defective_spare_invoice"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                         <div class="col-md-12">
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('bank_name')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="name" class="col-md-4">Booking Id * </label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" id="booking_id" name="booking_id" value = "" placeholder="Enter Booking Id" pattern="^[a-zA-Z0-9]+$">
                                        <?php echo form_error('charges_type'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="form-group">
                                    <input type="button" id="submit_btn" onclick="return get_spare_parts()" name="submit_btn" class="btn btn-info" value="Submit"/>
                                </div>
                            </div>
                        </div>
                    </div>   
                </form>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" id="defective_spare_detail" style="display: none">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Booking Id</th>
                                <th>Part Name</th>
                                <th>Part Type</th>
                                <th>Challan Approx Value</th>
                                <th>Status</th>
                                <th>Confirm Value</th>
                                <th>GST Rate</th>
                                <th>HSN Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div> 
           </div>
        </div>
    </div>
</div>
<?php if($this->session->userdata('error')){ $this->session->unset_userdata('error'); } ?>
<?php if($this->session->userdata('success')){ $this->session->unset_userdata('success');  } ?>
<script type="text/javascript">

    function get_spare_parts(){
        var booking_id = $("#booking_id").val();
        if(booking_id){
            $.ajax({
                url:'<?php echo base_url(); ?>employee/spare_parts/get_defective_spare_parts',
                type:'POST',
                data:{booking_id:booking_id}
            }).done(function(response){
                if(response == '[]'){
                   alert("No data found for this booking id.");
                }
                else{
                    response = JSON.parse(response);
                    var html = "";
                    var index = 1;
                    for(var i=0; i<response.length; i++){
                        html += "<tr>";
                            html += "<td>"+ index +"</td>";
                            html += "<td>"+ response[i]['booking_id'] +"</td>";
                            html += "<td>"+ response[i]['parts_shipped'] +"</td>";
                            html += "<td>"+ response[i]['shipped_parts_type'] +"</td>";
                            html += "<td>"+ response[i]['challan_approx_value'] +"</td>";
                            html += "<td>"+ response[i]['status'] +"</td>";
                            html += '<td><input type="hidden" id="spare_product_name_'+i+'" value="'+response[i]['parts_shipped']+'"><input type="number" class="form-control" placeholder="Enter Confirm Value" id="confirm_value_'+i+'"></td>';
                            html += '<td><input type="number" class="form-control" placeholder="Enter GST Rate" id="gst_rate_'+i+'"></td>';
                            html += '<td><input type="number" class="form-control" placeholder="Enter HSN Code" id="hsn_code_'+i+'"></td>';
                            html += '<td><input type="checkbox" name="spare_checkbox" id="spareCheck_'+i+'" data-id="'+response[i]['id']+'" service-center-id="'+response[i]['service_center_id']+'"></td>';
                        html += "</tr>";
                        index++;
                    }
                    html += "<tr><td colspan='9'><input type='test' id='remarks' class='form-control' placeholder='Enter reason for generating defective spare invoice for vendor'></td><td><input type='button' class='btn btn-primary' value='Submit' onclick='generate_spare_invoice()'></td></tr>";
                    $("#defective_spare_detail").css("display", "inline-table");
                    $("#defective_spare_detail tbody").html(html);
                }
               
            });
        }
        else{
            alert("Enter Booking Id");
        }
    }
    
    function generate_spare_invoice(){
        if($("input:checkbox[name=spare_checkbox]:checked").length !== 0){
            var booking_id = $("#booking_id").val();
            var flag = true;
            var postDataArray = [];
            $("input:checkbox[name=spare_checkbox]:checked").each(function(){
                var checkbox_id = $(this).attr("id");
                var id = checkbox_id.split("_")[1];
                if($("#confirm_value_"+id).val() && $("#gst_rate_"+id).val() && $("#hsn_code_"+id).val()){
                    var postData = {
                        spare_detail_ids : $(this).attr("data-id"),
                        service_center_ids : $(this).attr("service-center-id"),
                        gst_rates : $("#gst_rate_"+id).val(),
                        hsn_codes : $("#hsn_code_"+id).val(),
                        confirm_prices : $("#confirm_value_"+id).val(),
                        spare_product_name : $("#spare_product_name_"+id).val()
                    };
                    flag = true;
                }
                else{
                    flag = false;
                }
                postDataArray.push(postData);
            });
            if(flag == true){
                var formData = JSON.stringify(postDataArray);
                $.ajax({
                   url:'<?php echo base_url(); ?>employee/user_invoice/process_spare_invoice',
                   type:'POST',
                   beforeSend: function(){
                        $('body').loadingModal({
                        position: 'auto',
                        text: 'Loading Please Wait...',
                        color: '#fff',
                        opacity: '0.7',
                        backgroundColor: 'rgb(0,0,0)',
                        animation: 'wave'
                        });
                    },
                   data:{
                       postData:formData,
                       booking_id : booking_id,
                       remarks : $("#remarks").val()
                    }
                }).done(function(response){ 
                    $("#defective_spare_detail").css("display", "none");
                    $("#defective_spare_detail tbody").html("");
                    $("#booking_id").val(null);
                    $('body').loadingModal('destroy');
                   if(response == true){
                       alert("Invoice generated sunccessfully");
                   }
                   else{
                       alert("Invoice not generated, Try Again");
                   }
                });
            }
            else{
               alert("Enter input fields for checked spare parts");
            }
        }
        else{
            alert("Please select atleast one checkbox");
        }
    }

</script> 

    