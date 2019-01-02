
<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading">Partner Refuse to Pay</div>
            <div class="panel-body">
            <div class="row">
                <form name="myForm" class="form-horizontal" id ="partner_refuse_form" novalidate="novalidate" action=""  method="POST" enctype="multipart/form-data">
                    <div class="row">
                         <div class="col-md-12">
                            <div class="col-md-6">
                                <div  class="form-group">
                                    <label  for="name" class="col-md-4">Booking Id * </label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" id="booking_id" name="booking_id" value = "" placeholder="Enter Booking Id" pattern="^[a-zA-Z0-9]+$">
                                        <?php echo form_error('charges_type'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="form-group">
                                    <input type="button" id="submit_btn" onclick="return get_booking_detail()" name="submit_btn" class="btn btn-info" value="Submit"/>
                                </div>
                            </div>
                        </div>
                    </div>   
                </form>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table id="booking_table" class="table table-striped table-bordered" style="display: none">
                        <thead>
                            <tr>
                                <th>S No.</th>
                                <th>Booking ID</th>
                                <th>Order ID</th>
                                <th>Partner</th>
                                <th>Vendor</th>
                                <th>Appliance</th>
                                <th>Brand</th>
                                <th>Capacity</th>
                                <th>Category</th>
                                <th>Current Status</th>
                                <th>Internal Status</th>
                                <th>Request Type</th>
                                <th>Partner basic Charge</th>
                                <th>Vendor basic Charge</th>
                                <th>Refuse Reason</th>
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
<script type="text/javascript">

    function get_booking_detail(){
        var booking_id = $("#booking_id").val();
        if(booking_id){
            $.ajax({
                url:'<?php echo base_url(); ?>employee/user_invoice/get_refuse_booking_detail',
                type:'POST',
                data:{booking_id:booking_id}
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
                        var check_disable = "";
                        if(response[i]['partner_refuse_to_pay'] == '1'){
                            check_disable = "disabled";
                        }
                        html += "<tr>";
                            html += "<td>"+ index +"</td>";
                            html += "<td>"+ response[i]['booking_id'] +"</td>";
                            html += "<td>"+ response[i]['order_id'] +"</td>";
                            html += "<td>"+ response[i]['source'] +"</td>";
                            html += "<td>"+ response[i]['company_name'] +"</td>";
                            html += "<td>"+response[i]['services']+"</td>";
                            html += "<td>"+response[i]['appliance_brand']+"</td>";
                            html += "<td>"+response[i]['appliance_capacity']+"</td>";
                            html += "<td>"+response[i]['appliance_category']+"</td>";
                            html += "<td>"+response[i]['current_status']+"</td>";
                            html += "<td>"+response[i]['internal_status']+"</td>";
                            html += "<td>"+response[i]['price_tags']+"</td>";
                            html += "<td>"+response[i]['partner_net_payable']+"</td>";
                            html += "<td>"+response[i]['vendor_basic_charges']+"</td>";
                            html += "<td><select id='reason_"+i+"'><option selected disabled>Select Reason</option>";
                            for(var k=0; k<remarks.length; k++){
                                html += '<option value="'+remarks[k]['status']+'">'+remarks[k]['status']+'</option>';
                            }
                            html += "</select></td>";
                            html += '<td><input type="checkbox" id="bookingCheck_'+i+'" name="booking_checkbox" data-id="'+response[i]['id']+'" '+check_disable+'></td>';
                        html += "</tr>";
                        index++;
                    }
                    html += "<tr><td colspan='15'><input type='text' placeholder='Enter Remarks...' class='form-control' id='remarks'></td><td><input type='button' class='btn btn-primary' value='Refuse To Pay' onclick='process_refuse_to_pay()'></td></tr>";
                    $("#booking_table").css("display", "inline-table");
                    $("#booking_table tbody").html(html);
                    $("select").select2();
                }
               
            });
        }
        else{
            alert("Enter Booking Id");
        }
    }
    
    function process_refuse_to_pay(){
        if($("input:checkbox[name=booking_checkbox]:checked").length !== 0){
            var booking_id = $("#booking_id").val();
            var postdata = [];
            $("input:checkbox[name=booking_checkbox]:checked").each(function(){
                var checkbox_id = $(this).attr("id");
                var id = checkbox_id.split("_")[1];
                if($("#reason_"+id).val()){
                    var data = {
                      booking_unit_ids : $(this).attr("data-id"),
                      reasons : $("#reason_"+id).val()
                    };
                    flag = true;
                }
                else{
                    flag = false;
                }
                postdata.push(data);
            });
            if(flag == true){
               if($("#remarks").val()){
                var formData = JSON.stringify(postdata);
                $.ajax({
                   url:'<?php echo base_url(); ?>employee/user_invoice/process_refuse_to_pay',
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
                       booking_id:booking_id,
                       remarks : $("#remarks").val()
                    }
                }).done(function(response){ 
                    console.log(response);
                    $("#booking_table").css("display", "none");
                    $("#booking_table tbody").html("");
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
                alert("Please Enter Remark");
               }
            }
            else{
               alert("Enter input fields for checked booking id");
            }
        }
        else{
            alert("Please select atleast one checkbox");
        }
    }

</script> 

    