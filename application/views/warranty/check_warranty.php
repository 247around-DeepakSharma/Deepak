<?php 
$is_partner = FALSE;
if(($this->session->userdata('userType') == 'partner' && !empty($this->session->userdata('partner_id')) && $this->session->userdata('loggedIn') == TRUE)){
    $is_partner = TRUE;
}
?>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">
<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
<html>
    <body>
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Check Warranty   
                            <input type="text" value="" name="booking_id" id="booking_id" placeholder="Enter Booking Id" style="float:right;font-size:20px;padding:5px;"/><br/>
                            <span style="float:right;font-size:12px;color:blue;">* Press Enter to continue with Booking Id.</span>
                        </h1>
                        <form name="myForm" class="form-horizontal" method='post'> 
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="partner">Partner</label>
                                    <select class="form-control" name="partner" id="partner" required onchange='get_appliance()' <?php if($is_partner){ echo 'disabled';}?>>
                                        <option selected disabled value="">Select Partner</option>
                                        <?php
                                        foreach ($partnerArray as $partnerID => $partnerName) {
                                            $selected = "";
                                            if($partner_id == $partnerID)
                                            {
                                                $selected = "selected";
                                            }
                                            echo ' <option value="' . $partnerID . '" '.$selected.'>' . $partnerName . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="service_id">Product</label>
                                    <select class="form-control" id="service_id" required name="service_id" onchange='get_brand_model()'>
                                        <option selected disabled value="">Select Product</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="brand">Brand</label>
                                    <select class="form-control" id="brand" required name="brand">
                                        <option selected disabled value="">Select Brand</option>
                                    </select> 
                                </div>
                                <div class="col-md-3">
                                    <label for="model">Model</label>
                                    <select class="form-control" id="model" required="required" name="model">
                                        <option selected disabled value="">Select Model</option>
                                    </select>
                                </div>                                
                                <div class="col-md-3 pull-right">
                                    <button type="button" name='show' id='show' class='btn btn-primary'>Show</button>
                                </div>
                            </div>
                            <div class="row" style="padding-top: 10px;">
                                <div class="col-md-3">
                                    <label for="purchase_date">Purchase Date</label>
                                    <div class="input-group input-append date" >                                        
                                        <input type="text" class="form-control purchase_date"  name="purchase_date"  id="purchase_date" required readonly placeholder="Purchase Date"  onfocus="(this.type='date')">
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div> 
                                </div>
                                <div class="col-md-3">
                                    <label for="create_date">Booking Create Date</label>
                                    <div class="input-group input-append date" >                                        
                                        <input type="text" class="form-control create_date"  name="create_date"  id="create_date" required readonly placeholder="Booking Create Date"  onfocus="(this.type='date')" value="<?php echo date('d-m-Y')?>">
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div> 
                                </div>
                            </div>
                            <hr/>
                        </form>
                    </div>
                </div>
                <div class="x_panel" style="height: auto;overflow: auto;">
                    <table id="warranty_details" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Plan</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>States</th>
                                <th>Free Part Types</th>
                                <th>Warranty Type</th>
                                <th>Warranty Period</th>
                                <th>Warranty Grace Period</th>
                                <th>Warranty End Period</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
<script>
    $(document).ready(function(){
        var partner_id = $("#partner").val();
        if(partner_id !== null)
        {
            get_appliance();
        }
    });

    function validateform() {
        var partner = $("#partner option:selected").val();
        var service = $("#service_id option:selected").val();
        var brand = $("#brand option:selected").val();
        var model = $("#model option:selected").val();
        var purchase_date = $('#purchase_date').val();
        if ((partner == "") || (partner === 'option_holder') || (partner == 'undefined'))
        {
            alert("Please Select Partner ");
            return false;
        }
        else if ((service == "") || (service === 'option_holder') || (service == 'undefined'))
        {
            alert("Please Select Product ");
            return false;
        }
        else if ((brand == "") || (brand === 'option_holder') || (brand == 'undefined'))
        {
            alert("Please Select Brand ");
            return false;
        }
        else if ((model == "") || (model === 'option_holder') || (model == 'undefined'))
        {
            alert("Please Select Model ");
            return false;
        }
        else if (purchase_date == '')
        {
            alert("Please Select Purchase Date ");
            return false;
        }
        else {
            $(".x_panel").css("display", "block");
            ad_table.ajax.reload(function (json) {
            });
        }
    }

    function get_appliance() {
        var partner_id = $("#partner").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/booking/get_appliances/0',
            data: {'partner_id': partner_id},
            success: function (response) {
                response = JSON.parse(response);
                $('#service_id').html("");
                if (response.services) {
                    $('#service_id').html(response.services);
                    $('#service_id').val('<?php echo $service_id; ?>');                    
                }
                $('#service_id').trigger("change");
            }
        });

    }
    //This function is used to get Distinct Brands for selected service for Logged Partner
    function get_brand_model() {
        var partner_id = $("#partner").val();
        var service_id = $("#service_id").val();

        $('#model').html("<option disabled selected value=''>Select Model</option>");
        $('#brand').html("<option disabled selected value=''>Select Brand</option>");

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_brands_from_service',
            async: false,
            data: {service_id: service_id, partner_id: partner_id},
            success: function (data) {
                //First Resetting Options values present if any   
                $('#brand').html("");
                $('#brand').append(data);
                $('#brand').val('<?php echo $brand; ?>');
                $('#brand').trigger("change");
            }
        });

        //This function is used to get Distinct models for selected service for Logged Partner
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_model_for_partner_service_wise',
            data: {service_id: service_id, partner_id: partner_id},
            success: function (data) {
                //First Resetting Options values present if any                
                $('#model').append(data);
                $('#model').trigger("change");
            }
        });
    }

    var ad_table;
    $('#warranty_details').append('<caption style="caption-side: top;color:#f30;font-weight:bold;text-align:center;font-size:16px;" id="warranty_status"></caption>');
    ad_table = $('#warranty_details').DataTable({
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "ordering": false, //Initial no order.
        "pageLength": 50,
        "deferLoading": 0,
        "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<span class="fa fa-file-excel-o"></span> Export',
                pageSize: 'LEGAL',
                title: 'warranty_details',
                exportOptions: {
//                       columns: [0,1,2,3,4,5,6,7],
                    modifier: {
                        // DataTables core
                        order: 'index', // 'current', 'applied', 'index',  'original'
                        page: 'current', // 'all',     'current'
                        search: 'none'     // 'none',    'applied', 'removed'
                    }
                }

            }
        ],
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": baseUrl + "/employee/warranty/get_warranty_list_data",
            "type": "POST",
            "data": function (d) {
                d.partner = $("#partner option:selected").val();
                d.service_id = $("#service_id option:selected").val();
                d.model = $("#model option:selected").val();
                d.brand = $("#brand option:selected").val();
                d.purchase_date = $("#purchase_date").val();
                d.create_date = $("#create_date").val();
            },
            "dataSrc" : function (json) {
                if(json.activeInWarrantyPlans > 0)
                {
                    $("#warranty_status").html("<span style='color:green;'>In Warranty</span>");
                }
                else if((json.activeExtendedWarrantyPlans > 0) && (json.activeInWarrantyPlans == 0))
                {
                    $("#warranty_status").html("<span style='color:green;'>In Warranty has expired, Product lies in extended warranty</span>");
                }
                else if((json.activeExtendedWarrantyPlans == 0) && (json.activeInWarrantyPlans == 0))
                {
                    $("#warranty_status").html("Out of Warranty");
                }
                else
                {
                    $("#warranty_status").html("");
                }
                return json.data;
            } 
        },
        "columnDefs": [
            {
                "targets": [0, 5, 6, 7], //first column / numbering column
                "orderable": false //set not orderable
            }
        ],
        "createdRow": function( row, data, dataIndex){
                var curDate = new Date();
                var arrDate = data[9].split("-");
                var warranty_end_period = new Date(arrDate[2], arrDate[1]-1, arrDate[0]);
                if(+warranty_end_period.getTime() < +curDate.getTime()){
                    $(row).addClass('deactive');              
                }
        }
    });
     <?php if(!$is_partner){ ?> 
        $("#partner").select2();
     <?php  }?>
    $("#service_id,#brand,#model").select2();
    $("#purchase_date").datepicker({dateFormat: 'yy-mm-dd', maxDate: new Date(), changeMonth: true, changeYear: true});
    $("#create_date").datepicker({dateFormat: 'yy-mm-dd', maxDate: new Date(), changeMonth: true, changeYear: true});

    $('#show').click(function () {
        validateform();
    });
    
    // Autofill Warranty Relevant data on entering Booking Id
    $("#booking_id").keyup(function(event) {
        if (event.keyCode === 13 && $("#booking_id").val() != "") {
            fill_warranty_related_data();
        }        
    });;
    
    function fill_warranty_related_data()
    {
        $.ajax({
            method:'POST',
            url:"<?php echo base_url(); ?>employee/warranty/get_warranty_specific_data_from_booking_id",
            data:{
                booking_id : $("#booking_id").val(),
                partner_id : $("#partner").val()
            },
            success:function(response){
                var warrantyData = JSON.parse(response);
                if(warrantyData['error'] == 1){
                    alert(warrantyData['err_msg']);
                    return false;
                }
                $("#partner").val(warrantyData[0]['partner_id']);
                $('#partner').select2().trigger('change');
                setTimeout(function(){ 
                    $("#service_id").val(warrantyData[0]['service_id']);
                    $('#service_id').select2().trigger('change'); 
                    setTimeout(function(){ 
                        $("#brand").val(warrantyData[0]['appliance_brand']); 
                        $('#brand').select2().trigger('change');
                        $("#model").val(warrantyData[0]['model_id']);
                        $('#model').select2().trigger('change'); 
                    }, 700);
                }, 700);
                $('#purchase_date').datepicker('setDate', warrantyData[0]['purchase_date']); 
                $('#create_date').datepicker('setDate', warrantyData[0]['booking_create_date']); 
            }                            
        });
    }
    
</script>


<style>
    #warranty_details_filter{
        float: right;
    }
    
    .form-control[readonly]
    {
        background : #fff !important;
        cursor: pointer !important;
    }
    .deactive
    {
        background : #ffdead !important;
    }
    
    .page-header
    {
        padding-bottom : 20px !important;
    }

</style>
    <?php if($is_partner){ ?>
<style>
    #page-wrapper{
        background: rgb(248, 248, 248);
        width: 98%;
        margin-top: 40px;
        margin-left: 40px;
        margin-bottom: 9px;
    }
</style>
    <?php } ?>
