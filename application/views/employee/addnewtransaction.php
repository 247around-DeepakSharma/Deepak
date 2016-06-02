<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  
<script>
  $(function() {
     partner_vendor1(<?php echo $id; ?>);
     $( "#datepicker" ).datepicker({  maxDate: new Date });

  });

function partner_vendor1(vendor_partner_id){
     var par_ven = $('input[name=partner_vendor]:checked', '#myForm1').val();
     $('#loader_gif').css("display", "inline-block");
     $('#loader_gif').attr('src',  "<?php echo base_url(); ?>images/loader.gif");
     
      $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + par_ven,
                data: {vendor_partner_id: vendor_partner_id},
                success: function (data) {
                    console.log(data);
                    $("#name").html(data);
                    $('#loader_gif').attr('src',  "");
                    $('#loader_gif').css("display", "none");

            }
        });
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
                    amount: "required",
                    tdate: "required",
                },
                messages: {
                   // partner_vendor: "Please select Partner/Vendor",
                    credit_debit: "Please select credit/debit.",
                    amount: "Please enter credit/debit amount.",
                    tdate: "Please enter transaction date",
                },
                submitHandler: function(form) {
                    form.submit();
                }

            });
        }
    }


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
          <form name="myForm1" id="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/invoice/post_add_new_transaction" method="POST">
              <h1>Add New Transaction</h1>
	      <br>
	      <div class="form-group ">
                  <label class="col-md-2">Select Party<span class="red">*</span></label>
		  <div class="col-md-6">
		      <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="partner_vendor" <?php if($vendor_partner ==""){ echo "checked"; } else if($vendor_partner == "vendor"){ echo "checked"; }?> value = "vendor">    Service Centre &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type="radio" <?php if($vendor_partner == "partner"){ echo "checked"; } ?>onclick="partner_vendor1(<?php echo $id; ?>);" name="partner_vendor" value = "partner" >    Partner &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
              </div>
              <center><img id="loader_gif" src=""></center>

             <div class="form-group ">
              <label for="name" class="col-md-2">Name</label>
                <div class="col-md-6">
                  <select type="text" class="form-control"  id="name" name="partner_vendor_id"  required></select>
                </div>
             </div>

              <div class="form-group">
                <label for="invoice_id" class="col-md-2">Invoice ID</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="invoice_id" placeholder="Enter Invoice ID">
                  <span id="errmsg"></span>
                </div>
              </div>

              <div class="form-group ">
		  <label for="name" class="col-md-2">Credit / Debit in 247Around <span class="red">*</span></label>
		  <div class="col-md-6">
		      <input type="radio" onclick="cre_deb_validation1()"  name="credit_debit" value = "Credit" checked>   Credit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type="radio" onclick="cre_deb_validation1()"  name="credit_debit" value = "Debit">    Debit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                </div>
            <span id="errmsg1"></span>
              </div>

              <div class="form-group">
                <label for="name" class="col-md-2">Amount <span class="red">*</span></label>
                <div class="col-md-6">
		                <input type="text" class="form-control" id="amount" name="amount" required>
                </div>
                <span id="errmsg4"></span>
              </div>

              <div class="form-group">
		  <label for="name" class="col-md-2">Transaction Mode<span class="red">*</span></label>
		  <div>
		    <input type="radio" onclick="cre_deb_validation1()" name="transaction_mode" value = "Cash" checked>    Cash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <input type="radio" onclick="cre_deb_validation1()"  name="transaction_mode" value = "Cheque">    Cheque &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <input type="radio" onclick="cre_deb_validation1()" name="transaction_mode" value = "Transfer">    Transfer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		    <input type="radio" onclick="cre_deb_validation1()" name="transaction_mode" value = "Other">    Other
                </div>

              </div>

              <div class="form-group">
		  <label for="name" class="col-md-2">Party Bank Name</label>
		  <div class="col-md-6">
                  <input type="text" class="form-control"  name="bankname">
                </div>
              </div>

              <div class="form-group">
            	<label for="name" class="col-md-2">Transaction Date <span class="red">*</span></label>
                <div class="col-md-2">
                <div class="input-group input-append date" >
                    <input type="text" id="datepicker" class="form-control" name="tdate" >
                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
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
            <input type= "submit"  class="btn btn-danger btn-lg"  value ="Save" >
        </div>
          </form>

  </div>
</div>

<script type="text/javascript">
   $(document).ready(function () {
  //called when key is pressed in textbox
  $("#amount").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg4").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});


</script>

<?php if($this->session->userdata('success')) { $this->session->unset_userdata('success'); } ?>
 

