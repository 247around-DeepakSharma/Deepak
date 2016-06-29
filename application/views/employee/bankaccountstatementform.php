<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script>
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
                    vendor_partner: "required",
                    vendor_partner_id: "required",
                    invoice_id: "required",
                    credit_of: "required",
                    debit_of: "required",
                    payment_mode: "required",
                    tdate: "required",

                },
                messages: {
                    vendor_partner: "Please enter Partner/Vendor",
                    vendor_partner_id: "Please enter Partner/Vendor id",
                    invoice_id: "Please enter Invoice id",
                    credit_of: "Please enter credit of",
                    debit_of: "Please enter debit of",
                    payment_mode: "Please select payment mode",
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
    margin: 4px 0 5px 125px;
    padding: 0;
    text-align: left;
    width: 220px;
}
#errmsg,#errmsg1,#errmsg2,#errmsg3,#errmsg4,#errmsg5,#errmsg6
{
color: red;
}
</style>


<div id="page-wrapper">
  <div class="container-fluid">
      <div class="row">
          <form name="myForm1" id="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/invoice/post_bank_account_statement" method="POST">
              <h1>Bank account/statements</h1>
              <div class="form-group ">
            			<label for="name" class="col-md-2">Partner/Vendor</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  name="vendor_partner" placeholder="Enter Partner or Vendor">
                  		</div>
                                &nbsp;<span id="errmsg"></span>
            		 </div>
              <div class="form-group ">
            			<label for="name" class="col-md-2">Partner/Vendor ID</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  name="vendor_partner_id" placeholder="Enter Partner or Vendor ID">
                                        &nbsp;<span id="errmsg1"></span>
                  		</div>
              </div>
              <div class="form-group">
            			<label for="name" class="col-md-2">Invoice ID</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  name="invoice_id" placeholder="Enter Invoice ID">
                                        &nbsp;<span id="errmsg2"></span>
                  		</div>
              </div>
              <div class="form-group">
            			<label for="name" class="col-md-2">Bank Name</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  name="bankname" placeholder="Enter Bank Name">

                  		</div>
              </div>
              <div class="form-group">
            			<label for="name" class="col-md-2">Credit Of</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  id="credit_of" name="credit_of" placeholder="Enter credit of amount">
                                        &nbsp;<span id="errms3"></span>
                  		</div>
              </div>
              <div class="form-group">
            			<label for="name" class="col-md-2">Debit Of</label>
                  		<div class="col-md-6">
                  			<input type="text" class="form-control"  id="dedit_of" name="debit_of" placeholder="Enter debit of amount">
                                        &nbsp;<span id="errms4"></span>
                  		</div>
              </div>
              <div class="form-group">
            			<label for="name" class="col-md-2">Select Payment Mode  </label>
                                <div><input type="radio"  name="payment_mode" value = "Cash">Cash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="payment_mode" value = "Cheque">Cheque &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="payment_mode" value = "Transfer">Transfer
                                </div>

              </div>

              <div class="form-group">
            			<label for="name" class="col-md-2">Transaction Date</label>
                                <div class="col-md-2">
                                <input type="date" name="tdate"></div>
            </div>              &nbsp;<span id="errms5"></span>

              <div class="form-group">
            			<label for="name" class="col-md-2">Remark</label>
                  		<div class="col-md-6">
                                    <textarea class="form-control"  name="comment" cols="5" rows="5" placeholder="Enter Remark."></textarea>

                  		</div>
              </div>
              <div class="form-group">
                  <label for="excel" class="col-md-2">Receipt Image</label>
                  <div class="col-md-6">
                     <input type="file" class="form-control"  name="file">

                  </div>
               </div>
              <input type= "submit"  class="btn btn-danger btn-lg" style="width:150px;" value ="Save">

          </form>
      </div>
  </div>
</div>

<script >

  $(document).ready(function () {
  //called when key is pressed in textbox
  $("#credit_of").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg3").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});

$(document).ready(function () {
  //called when key is pressed in textbox
  $("#debit_of").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg4").html("Digits Only").show().fadeOut("slow");
               return false;
    }
   });
});
</script>
