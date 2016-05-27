<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script>
    $(function(){
        partner_vendor1();
    });

function partner_vendor1(){
     var par_ven = $('input[name=partner_vendor]:checked', '#myForm1').val();

      $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + par_ven,
                success: function (data) {
                    //console.log(data);
                    $("#name").html(data);

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
    margin: 4px 0 5px 125px;
    padding: 0;
    text-align: left;
    width: 400px;
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
          <form name="myForm1" id="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/invoice/post_add_new_transaction" method="POST">
              <h1>Add New Transaction</h1>
	      <br>
	      <div class="form-group ">
                  <label class="col-md-2">Select Party<span class="red">*</span></label>
		  <div class="col-md-6">
		      <input type="radio" onclick="partner_vendor1();" name="partner_vendor" checked="checked" value = "vendor">    Service Centre &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type="radio" onclick="partner_vendor1();" name="partner_vendor" value = "partner">    Partner &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
              </div>

             <div class="form-group ">
              <label for="name" class="col-md-2">Name</label>
                <div class="col-md-6">
                  <select type="text" class="form-control"  id="name" name="partner_vendor_id" required></select>
                </div>
             </div>

              <div class="form-group">
                <label for="invoice_id" class="col-md-2">Invoice ID</label>
                <div class="col-md-6">
                  <input type="text" class="form-control"  name="invoice_id" placeholder="Enter Invoice ID">
                  &nbsp;<br><span id="errmsg"></span>
                </div>
              </div>

              <div class="form-group ">
		  <label for="name" class="col-md-2">Credit / Debit in 247Around <span class="red">*</span></label>
		  <div class="col-md-6">
		      <input type="radio" onclick="cre_deb_validation1()"  name="credit_debit" value = "Credit" required>   Credit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type="radio" onclick="cre_deb_validation1()"  name="credit_debit" value = "Debit">    Debit &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                </div>
                &nbsp;<span id="errmsg1"></span>
              </div>

              <div class="form-group">
                <label for="name" class="col-md-2">Amount <span class="red">*</span></label>
                <div class="col-md-6">
		    <input type="text" class="form-control"  name="amount" required>
                </div>
              </div>&nbsp;<span id="errmsg4"></span>

              <div class="form-group">
		  <label for="name" class="col-md-2">Transaction Mode<span class="red">*</span></label>
		  <div>
		    <input type="radio" onclick="cre_deb_validation1()" name="transaction_mode" value = "Cash" required>    Cash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                    <input type="date" name="tdate" required>
                </div>
              </div>
              &nbsp;<span id="errms5"></span>
            <div class="form-group">
              <label for="name" class="col-md-2">Description</label>
              <div class="col-md-6">
		  <textarea class="form-control"  name="description" cols="5" rows="5" placeholder="Add transaction remarks"></textarea>
              </div>
            </div>

<!--            <div class="form-group">
            <label for="excel" class="col-md-2">Receipt Image</label>
            <div class="col-md-6">
              <input type="file" class="form-control"  name="file">
            </div>
            </div>-->
        <div>
            <input type= "submit"  class="btn btn-danger btn-lg" style="width:150px;margin-left:400px;" value ="Save" >
        </div>
          </form>

  </div>
</div>


