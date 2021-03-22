<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<style>
    .modal-body .select2-container{
        width: 100%!important;
    }
</style>
<div class="right_col" role="main">
    <!--        <div class="page-title">
        <div class="title_left">
            <h3>Order Details</h3>
        </div>
        </div>-->
    <div class="clearfix"></div>
    <div class="row" >
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2>
                            <i class="fa fa-bars"></i> CP Shop Address <!--<small>Float left</small>-->
                        </h2>
                        <ul class="nav navbar-right panel_toolbox">
<!--                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>-->
                            <a  href="<?php echo base_url();?>buyback/collection_partner/add_cp_shop_address"><li class="btn btn-sm btn-primary">Add Shop Address
                            </li></a>
                            <a  href="<?php echo base_url();?>buyback/buyback_process/download_bb_shop_address"><li class="btn btn-sm btn-primary">Download Shop Address File
                            </li></a>
                            
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form method="POST" action="<?php echo base_url(); ?>buyback/buyback_process/download_price_list_data" target="_blank" id="download_cp_price">
                            <table id="datatable" class="table table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th>No.</th>

                                        <th>CP Name</th>
                                        <th>CP Owner Name</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        <th>Pincode</th>
                                        <th>City/Region</th>
                                        <th>Action</th>
                                        <th>Last OTP Received</th>
                                        <th>Last OTP Received Date</th>
                                        <th>View All OTP</th>
                                        <th>View History</th>
                                        <th>
                                            <i class="fa fa-download" title="Download Price List"></i>
                                            <input type="checkbox" id="check_all_row_to_download_price">
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="update_form" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="cp_title"></h4>
                </div>
                <div class="modal-body">
                    <div class="x_content">

                        <form class="form-horizontal form-label-left " id="form" novalidate >


                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="name">Contact Person <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input id="primary_id" class="form-control col-md-7 col-xs-12"  name="primary_id"  type="hidden">
                                    <input id="contact_person" class="form-control col-md-7 col-xs-12" data-validate-length-range="6" data-validate-words="2" name="contact_person"  required="required" type="text">
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="contact_email">Contact Email 
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="email" id="contact_email" name="contact_email"  class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="email">Primary Mobile No. <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-4 col-xs-12">
                                    <input type="tel" id="primary_contact_number" name="primary_contact_number" data-validate-length-range="7,20" required="required" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="alt_mobile_no1">Alt Mobile No1 
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="tel" id="alternate_conatct_number" name="alternate_conatct_number" data-validate-length-range="7,20" required="required"  class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="alt_mobile_no2">Alt Mobile No2  
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="tel" id="alternate_conatct_number2" name="alternate_conatct_number2" data-validate-length-range="7,20" class="form-control col-md-7 col-xs-12">
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="tin_number">Tin Number 
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input id="tin_number" type="text" name="tin_number" class="optional form-control col-md-7 col-xs-12">
                                </div>
                            </div>

                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="textarea">Shop Address Line 1 <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea id="shop_address_line1" required="required" rows="3.5" name="shop_address_line1" class="form-control col-md-7 col-xs-12"></textarea>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="textarea">Shop Address Line 2 
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <textarea id="shop_address_line2"  name="shop_address_line2" class="form-control col-md-7 col-xs-12"></textarea>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="Pincode">Shop Address Pincode
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input id="shop_address_pincode" type="text" name="shop_address_pincode" required="required" class="optional form-control col-md-7 col-xs-12">
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="City">Shop Address City
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="shop_address_city" name="shop_address_city" required="required" class="optional form-control col-md-7 col-xs-12">
                                    </select>
                                </div>
                            </div>
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="Region">Shop Region
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select id="shop_address_region" name="shop_address_region[]" required="required" class="optional form-control col-md-7 col-xs-12" multiple="multiple">
                                    </select>
                                </div>
                            </div>
                            
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="State">Shop Address State
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input id="shop_address_state" type="text" name="shop_address_state" required="required" class="optional form-control col-md-7 col-xs-12">
                                </div>
                            </div>
                            
                            <div class="item form-group">
                                <label class="control-label col-md-4 col-sm-3 col-xs-12" for="cp_capacity">CP Capacity
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input id="cp_capacity" type="text" name="cp_capacity"  class="optional form-control col-md-7 col-xs-12">
                                </div>
                            </div>
                            
                            <div class="ln_solid"></div>
                            
                        </form>
                        <div class="form-group" style="margin-bottom:48px;">
                                <div class="col-md-12 col-md-offset-5">

                                    <button id="submit_form" class="btn btn-success" 
                                       data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing....">Submit</button>
                                </div>
                            </div>
                    </div>
                </div>
<!--                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>-->
            </div>

        </div>
    </div>
    
    <!--CP History Details Modal-->
    <div id="cpHistoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal_cp_name"> </h4>
                </div>
                <div class="modal-body">
                    <div id="open_cpHistoryModal"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end CP History Modal -->

    <script type="text/javascript">

        var table;

        $(document).ready(function () {
            table = $('#datatable').DataTable({
                "processing": true, //Feature control the processing indicator.
                "serverSide": true, //Feature control DataTables' server-side processing mode.
                "order": [], //Initial no order.
                "pageLength": 50,
                "ordering": false,
                // Load data for the table's content from an Ajax source
                "ajax": {
                    "url": "<?php echo base_url(); ?>buyback/collection_partner/get_cp_shop_address_data",
                    "type": "POST"

                },
                //Set column definition initialisation properties.
                "columnDefs": [
                    {
                        "targets": [0], //first column / numbering column
                        "orderable": false, //set not orderable
                    },
                ],
            });

        });

        function activate_deactivate(shop_id, is_acitve) {

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>buyback/collection_partner/activate_deactivate_cp/' + shop_id + "/" + is_acitve,
                success: function (data) {
                    if (data === "Success") {
                        table.ajax.reload(null, false);
                        $("cp_price_download_"+shop_id).show();
                        if(is_acitve){
                            $("cp_price_download_"+shop_id).addClass('download_single_cp_price');
                        }else{
                            $("cp_price_download_"+shop_id).removeClass('download_single_cp_price');
                        }
                        
                    } else {
                        alert("There is some issues to Activate/De-Activate. Please Contact Developer Team");
                    }

                }
            });
        }
        
        $(document).on("click", ".open-AddBookDialog", function () {
            var form_data = $(this).data('id');
            $('#primary_id').val(form_data.id);
            $("#cp_title").text( form_data.name );
            $(".modal-body #contact_person").val( form_data.contact_person );
            $(".modal-body #contact_email").val( form_data.contact_email );
            $(".modal-body #primary_contact_number").val( form_data.primary_contact_number );
            $(".modal-body #alternate_conatct_number").val( form_data.alternate_conatct_number );
            $(".modal-body #alternate_conatct_number2").val( form_data.alternate_conatct_number2 );
            $(".modal-body #tin_number").val( form_data.tin_number );
            $(".modal-body #shop_address_line1").val( form_data.shop_address_line1 );
            $(".modal-body #shop_address_line2").val( form_data.shop_address_line2 );
            var s_html = '<option selected value="'+form_data.shop_address_city+'">'+form_data.shop_address_city+'</option>';
            $(".modal-body #shop_address_city").html( s_html );
            $(".modal-body #shop_address_pincode").val( form_data.shop_address_pincode );
            $(".modal-body #shop_address_state").val( form_data.shop_address_state );
            $(".modal-body #cp_capacity").val( form_data.cp_capacity );
            
            var region = form_data.shop_address_region.split(',');
            var region_val = "";
            $(region).each(function(key, val ) {
                var r_html = '<option selected value="'+val+'">'+val+'</option>';
                region_val +=  r_html;
            });
            $(".modal-body #shop_address_region").html( region_val );
            $(".modal-body #shop_address_region").select2({
                tags: true
            });
           
       });
       
      
       $('#submit_form').on('click', function() {
            var $this = $(this);
            $this.button('loading');
            var fd = new FormData(document.getElementById("form"));
            fd.append("label", "WEBUPLOAD");
             $.ajax({
                url: "<?php echo base_url()?>buyback/collection_partner/update_cp_shop_address",
                type: "POST",
                data: fd,
                processData: false,  // tell jQuery not to process the data
                contentType: false   // tell jQuery not to set contentType
            }).done(function( data ) {
                if(data === "Success"){
                    $this.button('reset');
                    table.ajax.reload(null, false);
                    $('#update_form').modal('toggle');
                    //alert("Updated Successfully"); 
                } else {
                    $this.button('reset');
                    table.ajax.reload(null, false);
                    $('#update_form').modal('toggle');
                    alert("Updation Faled! Please Try Again"); 
                    
                }
            });

        });
        
         $("#shop_address_pincode").keyup(function(event) {
       
            check_pincode();
          
        });
        
        function check_pincode(){
        var pincode = $("#shop_address_pincode").val();
        var city = $("#shop_address_city").val();
        var region = $("#shop_address_region").val();
        
        if(pincode.length === 6){
            
            $.ajax({
                type: 'POST',
                beforeSend: function(){
                  
                    $('#submit_form').attr('disabled', true); 
                },
                url:  '<?php echo base_url(); ?>buyback/collection_partner/get_city_for_cp/',
                data:{city:city, pincode:pincode, region:region},
                success: function (data) {
                   
                    if(data === "Not Exist"){
                        $('#submit_form').attr('disabled', true); 
                        alert("Please check Pincode. It is not exist in the System.");
                        return false;
                    }  else {
                        var data1 = JSON.parse(data);
                        $("#shop_address_city").html(data1.city);
                        $("#shop_address_region").html(data1.region);
                        $('#submit_form').attr('disabled', false); 
                    } 
                }
                 
            }); 
        }
    }
    
    function get_cp_history(shop_id,cp_name){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>buyback/collection_partner/get_cp_history/' + shop_id,
            success: function (response) {
                //console.log(response);
                $('#modal_cp_name').html(cp_name);
                $('#open_cpHistoryModal').html(response);
                $('#cpHistoryModal').modal('toggle');
            }
        });
    }
    
    function check_validation() {
        var valid_form = false;
        var selected = [];
        $("input[type=checkbox]").each(function () {
            if ($(this).is(":checked")) {
                if($(this).attr('data-cp_id')){
                    var input = "<input type='hidden' class='cp_price_form_dynamic_field' name='cp_id["+$(this).attr('data-cp_id')+"]' value='"+$(this).attr('data-cp_name')+ "'>";
                    var input_field = $(input);
                    $('#download_cp_price').append(input_field);
                    selected.push($(this).attr('data-cp_id'));
                }
            }
        });
        
        if(selected.length <= 0){
            alert("Please select at least one CP");
        }else{
            valid_form = true;
        }
        
        return valid_form;
    };
    
    $('#check_all_row_to_download_price').on('click', function () {
        if ($(this).is(':checked', true))
        {
            $(".download_single_cp_price").prop('checked', true);
        }
        else
        {
            $(".download_single_cp_price").prop('checked', false);
        }
    });

    </script>
