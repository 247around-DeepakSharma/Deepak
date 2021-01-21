<!--<link rel="stylesheet" href="<?php //echo base_url();?>css/jquery.loading.css">
<script src="<?php //echo base_url();?>js/jquery.loading.js"></script>-->
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <h2>Convert MSL Challan into Invoice</h2>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="success_msg_div" style="display:none;">
                        <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                            <strong><span id="success_msg"></span></strong>
                        </div>
                    </div>
                    <div class="error_msg_div" style="display:none;">
                        <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                            <strong><span id="error_msg"></span></strong>
                        </div>
                    </div>
                    <div class="x_content_header">
                        <section class="fetch_inventory_data">
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-4">
                                        <form class="form-inline" action="#" method="POST">
                                            <label for="partner_id">Select Partner</label>
                                            <select class="form-control" id="partner_id_msl_challan" name="partner_id" required="">
                                                <option value="" disabled="">Select Partner</option>
                                            </select>
                                            <div id="partner_err1"></div>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm col-md-2" id="get_challan_details" style="margin-top: 22px;">Submit</button>                          
                                    </form>                                
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="clearfix"></div>
                    <div class="">
                        <table class="table table-bordered table-hover table-striped" id="msl_challan_data">
                            <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Challan Number</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Taxable Amount</th>
                            <th class="text-center">GST Amount</th>
                            <th class="text-center">From GST Number</th>
                            <th class="text-center">To GST Number</th>
                            <th class="text-center">Action</th>
                           </tr>
                        </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<form class="form-horizontal" id="courier_invoice_model_form" action="javascript:void(0)" method="post" novalidate="novalidate" enctype="multipart/form-data">
<!-- courier Information when warehouse Shipped defective parts to partner -->
<div class="convert_invoice_model">
    <div id="convert_invoice_model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="width:100% !important;">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Please Provide Courier Details</h4>
                </div>
                <div class="modal-body">
                    
                        <div class='row'>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="awb_by_wh" class="col-md-4">AWB *</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="con_awb_by_wh" name="awb_by_wh" placeholder="Please Enter AWB" required>
                                        <input type="hidden" class="form-control"  id="convert_challan_id" name="challan_id" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <?php  if (form_error('courier_name')) {echo 'has-error';} ?>
                                    <label for="courier_name_by_wh" class="col-md-4">Courier Name *</label>
                                    <div class="col-md-8">
                                        <!--                                        <input type="text"  class="form-control"  id="courier_name_by_wh" name="courier_name_by_wh" placeholder="Please Enter Courier Name" required>-->
                                        <select class="form-control"  name="courier_name_by_wh" id="con_courier_name_by_wh" required="">
                                            <option selected="" disabled="" value="">Select Courier Name</option>
                                            <?php foreach ($courier_details as $value1) { ?> 
                                            <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('courier_name'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="courier_price_by_wh" class="col-md-4">Courier Price *</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control" id="con_courier_price_by_wh"  name="courier_price_by_wh" placeholder="Please Enter Courier Price" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_courier_pic_by_wh" class="col-md-4">Courier Pic *</label>
                                    <div class="col-md-8">
                                        
                                        <input type="file" class="form-control" id="con_defective_parts_shippped_courier_pic_by_wh"  name="file" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_courier_pic_by_wh" class="col-md-4">Large Box Count</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="shipped_spare_parts_boxes_count" id="con_large_box">
                                            <option selected="" value="">Select Boxes</option>
                                            <?php for ($i = 1; $i < 31; $i++) { ?>
                                            <option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="shipped_spare_parts_boxes_count" class="col-md-4">Small Box Count</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="shipped_spare_parts_small_boxes_count"  id="con_small_box">
                                            <option selected="" value="">Select Small Boxes</option>
                                            <?php for ($i = 1; $i < 31; $i++) { ?>
                                            <option value="<?php echo $i; ?>" ><?php echo $i; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_courier_pic_by_wh" class="col-md-4">Weight *</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" style="width: 25%; display: inline-block;" id="con_shipped_spare_parts_weight_in_kg" name="spare_parts_shipped_kg" value="" placeholder="Weight" required=""> <strong> in KG</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="text" class="form-control" style="width: 25%; display: inline-block;" id="con_shipped_spare_parts_weight_in_gram"   value=""   name="spare_parts_shipped_gram" placeholder="Weight" required="">&nbsp;<strong>in Gram </strong> 
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <h4 class="modal-title">E-Way Bill Details</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="eway_vehicle_number" class="col-md-4">Vehicle Number</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control"  id="con_eway_vehicle_number" name="eway_vehicle_number" placeholder="Please Enter Vehicle Number">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="eway_bill_by_wh" class="col-md-4">E-Way Bill Number </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control"  id="con_eway_bill_by_wh" name="eway_bill_by_wh" placeholder="Please Enter E-Way Bill Number">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class='form-group'>
                                    <label for="defective_parts_shippped_ewaybill_pic_by_wh" class="col-md-4">E-Way Bill File </label>
                                    <div class="col-md-8">
                                        
                                        <input type="file" class="form-control"  id="con_defective_parts_shippped_ewaybill_pic_by_wh" name="defective_parts_shippped_ewaybill_pic_by_wh" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                   
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-success" onclick="process_convert_challan_invoice()" id="process_convert_challan_into_invoice" value="Submit">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script>
    var msl_def_challan;
    $('#partner_id_msl_challan').select2({
            placeholder:'Select Partner',
            allowClear:true
        });
        get_partner_ack_list();
        function get_partner_ack_list(){
            $.ajax({
                type:'POST',
                url:'<?php echo base_url();?>employee/service_centers/warehouse_ack_partner_list',
                data:{is_wh:true},
                success:function(response){
                    if(response === 'Error'){
                        
                    } else {
                        $('#partner_id_msl_challan').html(response);
                        var option_length = $('#partner_id_msl_challan').children('option').length;
                        if(option_length == 2){
                            $("#partner_id_msl_challan").change();   
                        }
                        
                       $('#partner_id_msl_challan').val($('#partner_id_msl_challan option:eq(1)').val()).trigger('change')
                       $("#partner_id_msl_challan").change();
                       msl_datatable_load();
                    }
                    
                   
                }
            });
        }
        
        $("#get_challan_details").click(function(){         
             var partner_id = $("#partner_id_msl_challan").val();
             if(partner_id==null){
                $("#partner_err1").html('Please Select Partner.').css({'color':'red'});
                return false;
             }else{
                 $("#partner_err1").html('');
                 msl_def_challan.ajax.reload(null, false);
                 
             }
             
         });
         
        function msl_datatable_load(){
            msl_def_challan = $('#msl_challan_data').DataTable({
                    processing: true, //Feature control the processing indicator.
                    serverSide: true, //Feature control DataTables' server-side processing mode.
                    pageLength: 50,
                    dom: 'Blfrtip',
                    lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export',
                            
                            title: 'Challan'
                        }
                    ],
                    // Load data for the table's content from an Ajax source
                    ajax: {
                        url: "<?php echo base_url(); ?>employee/spare_parts/get_challan_msl_summary",
                        type: "POST",
                        data: function(d){
                            d.partner_id = $("#partner_id_msl_challan").val();
                            d.warehouse_id = '<?php echo $warehouse_id;?>';
                        }
                    },
                    //Set column definition initialisation properties.
                    
                    "fnInitComplete": function (oSettings, response) {

//                        $(".dataTables_filter").addClass("pull-right");
//                        $("#total_req_quote").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
                    }
                });
        }
        
    function convert_challan_into_invoice(challan_id){
        $("#convert_challan_id").val(challan_id);
        $('#convert_invoice_model').modal('toggle');
    }
    
    function process_convert_challan_invoice(){
        var awb = $("#con_awb_by_wh").val();
        var courier_name = $("#con_courier_name_by_wh").val();
        var courier_price = parseFloat($("#con_courier_price_by_wh").val());
        var kgWeight = $("#con_shipped_spare_parts_weight_in_kg").val();
        var gramWeight = $("#con_shipped_spare_parts_weight_in_gram").val();
        var large_box_count = $("#con_large_box").val() || 0;
        var small_box_count = $("#con_small_box").val() || 0;
        var e_vehicle = $("#con_eway_vehicle_number").val();
        var e_bill_number= $("#con_eway_bill_by_wh").val();
        var e_bill_pic = $("#con_defective_parts_shippped_ewaybill_pic_by_wh").val();
        var files = $("#con_defective_parts_shippped_courier_pic_by_wh").val();
        var convert_challan_id = $("#convert_challan_id").val();
        if(convert_challan_id == ""){
            alert("Please refresh page & try again");
            return false
        }

        if(awb.trim() == ""){
            alert("Please Enter Awb Number");
            return false;
        }
        if(courier_name.trim() == ""){
            alert("Please Select Courier Name");
            return false;
        }
        
        if(!/^\d+(\.\d+)?$/g.test(courier_price)){              //should be number only with one decimal 
            alert("Courier price should be numerical and should not contain alphabets and special characters except decimal.")
            return false;
        }

        if(courier_price<0 || courier_price>2000){                              //should be in between 0 and 2000
            alert('Courier price should be in between 0 and 2000.');
            return false;
        }
        
        
        if(kgWeight.trim() == ""){
            alert("Please Enter Weight in KG");
            return false;
        }
        if(gramWeight.trim() == ""){
             alert("Please Enter Weight in Gram");
            return false;
        }
        
        let total = parseInt(kgWeight)+parseInt(gramWeight);
        if(!total){
            alert("Sum of weight in KG and GM must be greater than 0");
            return false;
        }
        
        if((small_box_count + large_box_count) <= 0){
            alert("Please Select Box Count");
            return false;
        }

        var formData = new FormData(document.getElementById("courier_invoice_model_form"));
        
        
        $.ajax({
            method:'POST',
            url:'<?php echo base_url(); ?>employee/spare_parts/convert_challan_into_invoice/'+convert_challan_id,
            data:formData,
            contentType: false,
            beforeSend: function(){
                        $('body').loadingModal({
                            position: 'auto',
                            text: 'Loading Please Wait...',
                            color: '#fff',
                            opacity: '0.7',
                            backgroundColor: 'rgb(0,0,0)',
                            animation: 'wave'
                    });

                     var btn = document.getElementById('process_convert_challan_into_invoice');
                        btn.disabled = true;
                        btn.innerText = 'Please wait...';
                    $("#process_convert_challan_into_invoice").attr('disabled',true);
                $('#courier_invoice_model_form').html('Submit').attr('disabled',true);

                        },
            processData: false,
            success:function(response){
                console.log(response);
                $("#process_convert_challan_into_invoice").attr('disabled',false);
                $('#courier_invoice_model_form').html('Submit').attr('disabled',false);
                $('#convert_invoice_model').modal('toggle');
                $('body').loadingModal('destroy');
                obj = JSON.parse(response);
                if(obj.status){
                    $('.success_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".success_msg_div").slideUp(1000);});   
                    $('#success_msg').html(obj.message);
                    alert(obj.message);
                    window.location.reload();
                }else{
                    alert(obj.message);
                    $('.error_msg_div').fadeTo(8000, 500).slideUp(500, function(){$(".error_msg_div").slideUp(1000);});
                    $('#error_msg').html(obj.message);
                }
            }
        });
    }
</script>