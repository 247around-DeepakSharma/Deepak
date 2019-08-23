
<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading">Defective Spare Repair OOW Invoice</div>
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
                                <th>Part Qty</th>
                                <th>Part Type</th>
                                <th>Challan Approx Value</th>
                                <th>Status</th>
                                <th>Purchase Amount</th>
                                <th>Reason</th>
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
                data:{booking_id:booking_id, part_warranty_status: 2, page : "<?php echo BILL_DEFECTIVE_OOW_SPARE_PART_PAGE; ?>"}
            }).done(function(response){
                response = JSON.parse(response);
                var remarks = response.remarks;
                response = response.data;
                if(response.length == '0'){
                   alert("No data found for this booking id.");
                }
                else{
                    
                    var html = "";
                    var index = 1;
                    for(var i=0; i<response.length; i++){
                        var href = "";
                        if(response[i]['partner_challan_file']){
                            href = "href='<?php echo S3_WEBSITE_URL; ?>vendor-partner-docs/"+response[i]['partner_challan_file']+"'";
                        }
                        html += "<tr>";
                            html += "<td>"+ index +"</td>";
                            html += "<td><a href ='<?php echo base_url()?>employee/booking/viewdetails/"+ response[i]['booking_id']+ "' target='_blank'>"+ response[i]['booking_id']+ "</a></td>";
                            html += "<td>"+ response[i]['parts_shipped'] +"</td>";
                            html += "<td><input type='text' class='form-control' id='shipped_qty_"+i+"' value='"+ response[i]['shipped_quantity'] +"' readonly ></td>";
                            html += "<td>"+ response[i]['shipped_parts_type'] +"</td>";
                            html += "<td><a "+href+" target='_blank' id='challan_value_"+i+"' >"+ response[i]['challan_approx_value'] +"</td></a>";
                            html += "<td>"+ response[i]['status'] +"</td>";
                            html += '<td><input type="hidden" id="spare_product_name_'+i+'" value="'+response[i]['parts_shipped']+'"><input type="hidden" id="hsn_code_'+i+'" value="'+response[i]['hsn_code']+'"><input type="hidden" id="gst_rate_'+i+'" value="'+response[i]['gst_rate']+'"><input type="hidden" id="price_val_'+i+'" value="'+response[i]['price']+'"><input type="number" class="form-control part_value" placeholder="Enter Confirm Value" id="confirm_value_'+i+'" onchange="checkPartVal(this.id)"></td>';
                            html += '<td>';
                            html += '<select id="reason_'+i+'"><option  disabled selected>Select Reason</option>';
                            for(var k=0; k<remarks.length; k++){
                                html += '<option value="'+remarks[k]['status']+'">'+remarks[k]['status']+'</option>';
                            }
                            html += '</select>';
                            html += '</td>';
                            html += '<td><input type="checkbox" name="spare_checkbox" id="spareCheck_'+i+'" data-id="'+response[i]['id']+'" service-center-id="'+response[i]['service_center_id']+'"></td>';
                        html += "</tr>";
                        index++;
                    }
                    html += "<tr><td colspan='9'><input type='text' id='remarks' style='height: 50px;' class='form-control' placeholder='Enter Remark for generating defective spare invoice for vendor'></td><td><input type='button' class='btn btn-primary' value='Submit' onclick='generate_spare_invoice()'></td></tr>";
                    $("#defective_spare_detail").css("display", "inline-table");
                    $("#defective_spare_detail tbody").html(html);
                    $("select").select2();
                }

                $('.part_value').each(function(){
                    var id = $(this).attr('id').split('_');
                    var part_val = $('#confirm_value_'+id[2]).val();
                    if($('#price_val_'+id[2]).val() !== 'null') {
                        var invoice_value = parseFloat($('#price_val_'+id[2]).val()) + (parseFloat($('#price_val_'+id[2]).val())*(parseFloat($('#gst_rate_'+id[2]).val())/100));
                        var tot_invoice_value = parseFloat(invoice_value) * parseFloat($('#shipped_qty_'+id[2]).val());
                        $('#confirm_value_'+id[2]).val(tot_invoice_value.toFixed(2));
                    }
                });
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
                if($("#confirm_value_"+id).val() && $("#gst_rate_"+id).val() && $("#hsn_code_"+id).val() && $("#reason_"+id).val()){ 
                    var postData = {
                        spare_detail_ids : $(this).attr("data-id"),
                        service_center_ids : $(this).attr("service-center-id"),
                        gst_rates : $("#gst_rate_"+id).val(),
                        hsn_codes : $("#hsn_code_"+id).val(),
                        confirm_prices : $("#confirm_value_"+id).val(),
                        spare_product_name : $("#spare_product_name_"+id).val(),
                        reasons : $("#reason_"+id).val()
                    };
                    flag = true;
                }
                else{
                    flag = false;
                }
                postDataArray.push(postData);
            });
            if(flag == true){
              if($("#remarks").val()){
                var formData = JSON.stringify(postDataArray);
                $.ajax({
                   url:'<?php echo base_url(); ?>employee/user_invoice/process_repair_oow_spare_invoice',
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
                       alert("Invoice generated successfully");
                   }
                   else{
                       alert("Invoice not generated, Try Again");
                   }
                });
               }
               else{
                alert("Please Enter Remark");
               }
            }
            else{
               alert("Enter input fields for checked spare parts");
            }
        }
        else{
            alert("Please select atleast one checkbox");
        }
    }
    
    
    function checkPartVal(id) {
        var id1 = id.split('_');
        if(parseFloat($('#confirm_value_'+id1[2]).val()) < parseFloat($('#challan_value_'+id1[2]).text())) {
            alert("Purchase Amount is Less Than Challan Approx Value!!");
            $('#confirm_value_'+id1[2]).focus();
        }
    }

</script> 

    