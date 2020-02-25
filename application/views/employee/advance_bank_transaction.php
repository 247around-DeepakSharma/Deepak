<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script>
    $(function() {
       partner_vendor1(<?php echo $id; ?>);
       $( "#datepicker" ).datepicker({  maxDate: new Date });
    
    });
    
    function get_third_party_list(){
        var par_ven = $('input[name=partner_vendor]:checked', '#myForm1').val();
        var hide = true;
        if(par_ven === "vendor"){
            var type = $('input[name=advance_type]:checked', '#myForm1').val();
            if(type === '<?php echo MICRO_WAREHOUSE_CHARGES_TYPE;?>'){
                var vendor_id = $("#name").val();
                hide = false;
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/spare_parts/get_micro_partner_list/' + vendor_id,
                    data: {},
                    success: function (data) {
                        console.log(data);
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
                      tds_amount: "required"
                  },
                  messages: {
                     // partner_vendor: "Please select Partner/Vendor",
                      credit_debit: "Please select credit/debit.",
                      amount: "Please enter credit/debit amount.",
                      tdate: "Please enter transaction date",
                      tds_amount: "Please enter TDS"
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

                        form.submit();
                      
                      
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
            
            <div class="form-group ">
                <label class="col-md-2">Select Party<span class="red">*</span></label>
                <div class="col-md-6">
                    <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="partner_vendor" <?php if($vendor_partner ==""){ echo "checked"; } else if($vendor_partner == "vendor"){ echo "checked"; }?> value = "vendor">    Service Center &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" <?php if($vendor_partner == "partner"){ echo "checked"; } ?> onclick="partner_vendor1(<?php echo $id; ?>);" name="partner_vendor" value = "partner" >    Partner &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            </div>
            <img id="loader_gif" src="<?php echo base_url(); ?>images/loader.gif" style="width:50px; display:none" class="col-md-offset-3">
            <div class="form-group " id="advance_tag">
                <label for="name" class="col-md-2">Advance Type <span class="red">*</span></label>
                <div class="col-md-6">
                    <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="advance_type" value = "<?php echo BUYBACKTYPE;?>">   Buyback &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="advance_type" value = "<?php echo MICRO_WAREHOUSE_CHARGES_TYPE;?>" >    Micro Warehouse &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio"  onclick="partner_vendor1(<?php echo $id; ?>);" name="advance_type" value = "<?php echo SECURITY; ?>">   Advance Security &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio"  onclick="partner_vendor1(<?php echo $id; ?>);" name="advance_type" value = "<?php echo FNF; ?>">   FNF Security &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
                <span id="errmsg1"></span>
            </div>
            <div class="form-group " >
                <label for="name" class="col-md-2">Name<span class="red">*</span></label>
                <div class="col-md-6">
                    <select  onchange="get_third_party_list()" class="form-control"  id="name" name="partner_vendor_id"  required></select>
                </div>
            </div>
            
            <div class="form-group " id="third_party_div" style="display:none">
                <label for="third party" class="col-md-2">Third Party Name<span class="red">*</span></label>
                <div class="col-md-6">
                    <select  class="form-control" id="third_party" name="third_party"  ></select>
                </div>
            </div>
            
            <div class="form-group ">
                <label for="name" class="col-md-2">Credit / Debit in 247Around <span class="red">*</span></label>
                <div class="col-md-6">
                    <label for="credit_debit" generated="true" class="error"></label>
                    <input type="radio"   name="credit_debit" value = "Credit">   Credit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio"   name="credit_debit" value = "Debit" >    Debit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
                <span id="errmsg1"></span>
            </div>
            
            <div class="form-group">
                <label for="name" class="col-md-2">Amount (With TDS) <span class="red">*</span></label>
                <div class="col-md-6">
                    <input type="number" class="form-control" id="amount" name="amount" min="0" value="0" step="0.01" value="" required>
                </div>
                <span id="errmsg4"></span>
            </div>
            <div class="form-group">
                <label for="name" class="col-md-2">TDS <span class="red">*</span></label>
                <div class="col-md-6">
                    <input type="number" class="form-control" id="tds_amount" min="0" value="0" step="0.01" name="tds_amount" value="" required >
                </div>
                <span id="errmsg4"></span>
            </div>
            <div class="form-group">
                <label for="name" class="col-md-2">Transaction Mode<span class="red">*</span></label>
                <div>
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
            <div class="form-group">
                <label for="name" class="col-md-2">Party Bank Name</label>
                <div class="col-md-6">
                    <input type="text" class="form-control"  name="bankname" value="">
                </div>
            </div>
            <div class="form-group">
                <label for="name" class="col-md-2">Transaction Date <span class="red">*</span></label>
                <div class="col-md-2">
                    <div class="input-group input-append date" >
                        <input type="text" id="datepicker" class="form-control" style = "background-color:#fff;" name="tdate" readonly='true' value="<?php echo date('Y-m-d');?>">
                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="transaction_id" class="col-md-2">Transaction Id</label>
                <div class="col-md-6">
                    <input type="text" class="form-control"  name="transaction_id" value = "" placeholder="Transaction Id">
                </div>
            </div>
            <span id="errms5"></span>
            <div class="form-group">
                <label for="name" class="col-md-2">Description</label>
                <div class="col-md-6">
                    <textarea class="form-control"  name="description" cols="5" rows="5" placeholder="Add transaction remarks"></textarea>
                </div>
            </div>
            <div class="col-md-12" style="text-align: center;">
                <input type= "submit" id="submitform"  class="btn btn-danger btn-lg"  value ="Save" >
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