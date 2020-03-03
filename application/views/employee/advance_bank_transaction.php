<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<style>
    #last_three_transactions_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }
    
    #last_three_transactions_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
    
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<script>
    $(function() {
       partner_vendor1(<?php echo $id; ?>);
       $( "#datepicker" ).datepicker({  maxDate: new Date });
    
    });
    
    function get_third_party_list(){
        var par_ven = $('input[name=partner_vendor]:checked', '#myForm1').val();
        var type = $('input[name=advance_type]:checked', '#myForm1').val();
        var partner_vendor_id = $("#name").val();
        var hide = true;
        if(par_ven === "vendor"){
            if(type === '<?php echo MICRO_WAREHOUSE_CHARGES_TYPE;?>'){
                
                hide = false;
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/spare_parts/get_micro_partner_list/' + partner_vendor_id,
                    data: {},
                    success: function (data) {
                      //  console.log(data);
                        $("#third_party").html(data).change();
                        $('#loader_gif').attr('src',  "");
                        $('#loader_gif').css("display", "none");

                }
              });
            }
        }
        
        if(hide){
            $("#third_party_div").css('display',"none");
        } else {
           $("#third_party_div").css('display',"block");
        }
        
        //Vendor redio button is selected
        if(par_ven === "vendor"){
        //Vendor selected from dropdown and advance type selected from radio button
            if(partner_vendor_id && type){
                get_last_three_bank_transaction_data();
            }
        }else{
        //Partner redio button is selected
            if(partner_vendor_id){
            //Partner selected from dropdown
                get_last_three_bank_transaction_data();
            }
        }   
    }
    
    //this function is used to show last 3 bank transactions of vendor/ partner
    var table_created = 0;
    var last_three_transaction_table = '';
    function get_last_three_bank_transaction_data(){
        if(table_created == 0){
            //datatable is not initialised yet
            table_created = 1;
            last_three_transaction_table = $('#last_three_transactions').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [],
            "bFilter": false,
            "bPaginate": false,
            "bLengthChange": false,
            "bInfo" : false,
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            select: {
                style: 'multi'
            },
            "order": [], 
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            "ajax": {
                "url": '<?php echo base_url(); ?>employee/invoice/get_vendor_partner_bank_transaction',
                "type": "POST",
                data: function(d){
                    d.partner_vendor = $('input[name=partner_vendor]:checked', '#myForm1').val();
                    d.partner_vendor_id = $('#name').val();
                }
            },
            "deferRender": true       
        });
        }else{
        //datatable is already initialised
            last_three_transaction_table.ajax.reload(null, false);
        }
    }
    
    function partner_vendor1(vendor_partner_id){
       var par_ven = $('input[name=partner_vendor]:checked', '#myForm1').val();
       var type = undefined;
       var ajax_call = false;
       if(par_ven === "partner"){
           ajax_call = true
           $("#advance_tag").css("display","none");
       } else {
            var type = $('input[name=advance_type]:checked', '#myForm1').val();
       
            if(type === undefined){
                ajax_call = false;
            } else {
                ajax_call = true;
            }
           $("#advance_tag").css("display","block");
       }
       if(ajax_call){
           $('#loader_gif').css("display", "inline-block");
           $('#loader_gif').attr('src',  "<?php echo base_url(); ?>images/loader.gif");
           $.ajax({
                  type: 'POST',
                  url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + par_ven,
                  data: {vendor_partner_id: vendor_partner_id,invoice_flag:0, type: type},
                  success: function (data) {
                      
                      $("#name").html(data).change();
                      $('#loader_gif').attr('src',  "");
                      $('#loader_gif').css("display", "none");
    
              }
          });
       }
        
    }
    
    var confirm_payment  = true;
          (function($,W,D)
    {
      var JQUERY4U = {};
    
      JQUERY4U.UTIL =
      {
          setupFormValidation: function()
          {
              //form validation rules
              $("#myForm1").validate({
                  rules: {
                      //partner_vendor: "required",
                      credit_debit: "required",
                      amount: {
                          digits: true,
                          required:true,
                          minlength:1
                          },
                      tdate: "required",
                      tds_amount: "required",
                      description: "required"
                  },
                  messages: {
                     // partner_vendor: "Please select Partner/Vendor",
                      credit_debit: "Please select credit/debit.",
                      amount: "Please enter credit/debit amount.",
                      tdate: "Please enter transaction date",
                      tds_amount: "Please enter TDS",
                      description: "Please enter description"
                  },
                  submitHandler: function(form) {
                       $('#submitform').val("Please wait.....");
                       document.getElementById('submitform').disabled=true;
                       var par_ven = $('input[name=partner_vendor]:checked', '#myForm1').val();
                       if(par_ven === "vendor"){
                           var type =  $('input[name=advance_type]:checked', '#myForm1').val();
                            if(type === undefined){
                                document.getElementById('submitform').disabled=false;
                                $('#submitform').val("Save");
                                alert("Please Select Advance Type");
                                $('#submitform').val("Save");
                                return false;
                            } else {
                                if(type === '<?php echo MICRO_WAREHOUSE_CHARGES_TYPE;?>'){

                                    var third_party = $("#third_party").val();
                                    if(third_party === null){
                                        document.getElementById('submitform').disabled=false;
                                        $('#submitform').val("Save");
                                        alert("Please Select Third Party Name");
                                        return false;
                                    }
                                }
                            }
                        } 
                        
                        var amount = $("#amount").val();
                        if(amount < 1){
                            document.getElementById('submitform').disabled=false;
                            $('#submitform').val("Save");
                            alert("Please Enter Amount");
                            return false;
                        }

                        var name = $("#name").val();
                        var datepicker = $("#datepicker").val();
                        $.ajax({
                                type: 'POST',
                                url: '<?php echo base_url(); ?>employee/invoice/check_if_payment_already_done',
                                data: {amount : amount, vendor_partner_id : name, vendor_partner : par_ven, date : datepicker},
                                success: function (data) {
                                    if(data == 1){
                                        //this type of payment has already been done so we are asking user to confirm
                                        confirm_payment = confirm(amount + " amount of transaction for this " + par_ven + " for date " + datepicker + " has already been added. Do you still want to continue?");
                                    }
                                    //user confirmed for the payment
                                    if(confirm_payment === true){
                                        form.submit();
                                    }
                                    else{
                                        //user denied for the payment
                                        document.getElementById('submitform').disabled=false;
                                        $('#submitform').val("Save");
                                    }
                            }
                          });

                  }
    
              });
          }
      };
    
    
      //when the dom has loaded setup form validation rules
      $(D).ready(function($) {
          
          JQUERY4U.UTIL.setupFormValidation();
      });
    
    })(jQuery, window, document);
    
</script>
<style type="text/css">
    #myForm1 .form-group label.error {
    color: #FB3A3A;
    display: inline-block;
    padding: 0;
    text-align: left;
    width: 100%;
    }
    #errmsg,#errmsg1,#errmsg2,#errmsg3,#errmsg4,#errmsg5,#errmsg6
    {
    color: red;
    }
    .red{
    color:red;
    font-size: 20px;
    }
</style>
<div id="page-wrapper">
<div class="container-fluid">
    <div class="row">
        <?php if($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:20;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('success') . '</strong>
            </div>';
            }
            ?>
        
            <?php if($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:20;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('error') . '</strong>
            </div>';
            }
            ?>
        <form name="myForm1" id="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/invoice/process_advance_payment" method="POST">
            <h1>Add Advance Payment Transaction</h1>
            
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group ">
                        <label class="col-md-4">Select Party<span class="red">*</span></label>
                        <div class="col-md-6">
                            <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="partner_vendor" <?php if($vendor_partner ==""){ echo "checked"; } else if($vendor_partner == "vendor"){ echo "checked"; }?> value = "vendor">    Service Center &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" <?php if($vendor_partner == "partner"){ echo "checked"; } ?> onclick="partner_vendor1(<?php echo $id; ?>);" name="partner_vendor" value = "partner" >    Partner &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <img id="loader_gif" src="<?php echo base_url(); ?>images/loader.gif" style="width:50px; display:none" class="col-md-offset-3">
                    <div class="form-group " id="advance_tag">
                        <label for="name" class="col-md-4">Advance Type <span class="red">*</span></label>
                        <div class="col-md-6">   
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="advance_type" value = "<?php echo BUYBACKTYPE;?>">   Buyback
                                </div>    
                                <div class="col-md-6">
                                    <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="advance_type" value = "<?php echo MICRO_WAREHOUSE_CHARGES_TYPE;?>" >   Micro Warehouse
                                </div>    
                            </div>  
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="radio"  onclick="partner_vendor1(<?php echo $id; ?>);" name="advance_type" value = "<?php echo SECURITY; ?>">   Advance Security
                                </div>    
                                <div class="col-md-6">
                                    <input type="radio"  onclick="partner_vendor1(<?php echo $id; ?>);" name="advance_type" value = "<?php echo FNF; ?>">   FNF Security
                                </div>    
                            </div>  
                        </div>
                        <span id="errmsg1"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group " >
                        <label for="name" class="col-md-4">Name<span class="red">*</span></label>
                        <div class="col-md-6">
                            <select  onchange="get_third_party_list()" class="form-control"  id="name" name="partner_vendor_id"  required></select>
                        </div>
                    </div>
                </div>    
                <div class="col-md-6">
                    <div class="form-group " id="third_party_div" style="display:none">
                        <label for="third party" class="col-md-4">Third Party Name<span class="red">*</span></label>
                        <div class="col-md-6">
                            <select  class="form-control" id="third_party" name="third_party"  ></select>
                        </div>
                    </div>
                </div>    
            </div>    
            
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group ">
                        <label for="name" class="col-md-4">Credit / Debit in 247Around <span class="red">*</span></label>
                        <div class="col-md-6">
                            <input type="radio"   name="credit_debit" value = "Credit">   Credit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio"   name="credit_debit" value = "Debit" >    Debit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label for="credit_debit" generated="true" class="error"></label>
                        </div>
                        <span id="errmsg1"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-md-4">Amount (With TDS) <span class="red">*</span></label>
                        <div class="col-md-6">
                            <input type="number" class="form-control" id="amount" name="amount" min="0" value="0" step="0.01" value="" required>
                        </div>
                        <span id="errmsg4"></span>
                    </div>
                </div>    
            </div>    
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-md-4">TDS <span class="red">*</span></label>
                        <div class="col-md-6">
                            <input type="number" class="form-control" id="tds_amount" min="0" value="0" step="0.01" name="tds_amount" value="" required >
                        </div>
                        <span id="errmsg4"></span>
                    </div>
                </div>    
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-md-4">Transaction Mode<span class="red">*</span></label>
                        <div class="col-md-6">
                            <input type="radio"  name="transaction_mode" value = "Cash" 
                                >    Cash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio"   name="transaction_mode" value = "Cheque"
                               >    Cheque &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio"  name="transaction_mode" value = "Transfer"
                                   checked >    Transfer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio"  name="transaction_mode" value = "Other"
                               >    Other
                        </div>
                    </div>
                </div>    
            </div>    
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-md-4">Party Bank Name</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="bankname" value="">
                        </div>
                    </div>
                </div>    
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-md-4">Transaction Date <span class="red">*</span></label>
                        <div class="col-md-6">
                            <div class="input-group input-append date" >
                                <input type="text" id="datepicker" class="form-control" style = "background-color:#fff;" name="tdate" readonly='true' value="<?php echo date('Y-m-d');?>">
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>   
            <div class="col-md-12">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="transaction_id" class="col-md-4">Transaction Id</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control"  name="transaction_id" value = "" placeholder="Transaction Id">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <span id="errms5"></span>
                    <div class="form-group">
                        <label for="name" class="col-md-4">Description<span class="red">*</span></label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="description" name="description" cols="5" rows="5" placeholder="Add transaction remarks"></textarea>
                            <label for="description" generated="true" class="error"></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="text-align: center;margin-bottom: 30px;">
                <input type= "submit" id="submitform"  class="btn btn-danger btn-lg"  value ="Save" >
            </div>
    
            <div class="model-table">
                <table class="table table-bordered table-hover table-striped" id="last_three_transactions">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Partner/Vendor Name</th>
                            <th>Transaction Date</th>
                            <th>Description</th>
                            <th>Credit/debit</th>
                            <th>Amount</th>
                            <th>TDS Amount</th>
                            <th>Invoice ID</th>
                            <th>Transaction Mode</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $("#name").select2();
    
    //   $(document).ready(function () {
    //  //called when key is pressed in textbox
    //  $("#amount").keypress(function (e) {
    //     //if the letter is not digit then display error and don't type anything
    //     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
    //        //display error message
    //        $("#errmsg4").html("Digits Only").show().fadeOut("slow");
    //               return false;
    //    }
    //   });
    //});
    
</script>
<?php if($this->session->userdata('success')) { $this->session->unset_userdata('success'); } ?>
<?php if($this->session->userdata('error')) { $this->session->unset_userdata('error'); } ?>
