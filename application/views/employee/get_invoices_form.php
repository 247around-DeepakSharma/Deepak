<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<script>
  $(function() {
     partner_vendor1(<?php echo $id; ?>);


  });

function partner_vendor1(vendor_partner_id){
    var par_ven = $('input[name=partner_vendor]:checked', '#myForm1').val();
     $('#loader_gif').css("display", "inline-block");
     $('#loader_gif').attr('src',  "<?php echo base_url(); ?>images/loader.gif");
     if(par_ven === "partner"){
         document.getElementById("myRadio1").disabled = true;
         document.getElementById("myRadio2").disabled = true;
         document.getElementById("myRadio3").disabled = true;
         document.getElementById("brackets").style.display = "none";
         document.getElementById("brackets").disabled = true;
     } else {
        document.getElementById("myRadio1").disabled = false;
         document.getElementById("myRadio2").disabled = false;
         document.getElementById("myRadio3").disabled = false;
         document.getElementById("brackets").style.display = "block";
         document.getElementById("brackets").disabled = false;
     }


      $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + par_ven,
                data: {vendor_partner_id: vendor_partner_id,invoice_flag: 1},
                success: function (data) {

                    $("#name").html(data);
                    $("#name").val("All").change();
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
                    partner_vendor_id: "required",
                    invoice_month: "required"

                },
                messages: {
                   // partner_vendor: "Please select Partner/Vendor",
                    partner_vendor_id: "Please select .",
                    invoice_month: "Please Select Month."

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
 <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

    <script type="text/javascript">
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            locale: {
               format: 'YYYY/MM/DD'
            },
            startDate: '<?php echo date("Y/m/01", strtotime("-1 month")) ?>',
            endDate: '<?php echo date("Y/m/01") ?>'
        });
//        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
//          $(this).val(picker.startDate.format('YYYY/MM/DD') + '-' + picker.endDate.format('YYYY/MM/DD'));
//        });

        });
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
          <form name="myForm1" id="myForm1" class="form-horizontal" action="<?php echo base_url() ?>employee/invoice/process_invoices_form" method="POST">
              <h1>Generate Invoices</h1>
	      <br>
	      <div class="form-group ">
                  <label class="col-md-2">Entity Type<span class="red">*</span></label>
		  <div class="col-md-6">
		      <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="partner_vendor"  checked="checked" value = "vendor" >    Service Centre &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);" name="partner_vendor" value = "partner" >    Partner &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  </div>
              </div>
              <center><img id="loader_gif" src=""></center>

             <div class="form-group ">
              <label for="name" class="col-md-2">Company Name</label>
                <div class="col-md-3">
                  <select type="text" class="form-control"  id="name" name="partner_vendor_id"  required></select>
                </div>
             </div>



              <div class="form-group ">
		  <label for="name" class="col-md-2">Invoice Version <span class="red">*</span></label>
		  <div class="col-md-6">
		      <input type="radio"  name="invoice_version" value = "draft" checked>   Draft &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type="radio"  name="invoice_version" value = "final">    Final &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                </div>
            <span id="errmsg1"></span>
              </div>

               <div class="form-group ">
		  <label for="name" class="col-md-2">Invoice Type<span class="red">*</span></label>
                  <div class="col-md-6" style="margin-left:-15px;">
		     <span class="col-md-2"><input type="radio"  id="myRadio1" name="vendor_invoice_type" value = "all" checked >All </span>
                      <span class="col-md-2"><input type="radio"  id="myRadio2" name="vendor_invoice_type" value = "foc" >FOC</span>
                      <span class="col-md-2"><input type="radio"  id="myRadio3" name="vendor_invoice_type" value = "cash" >CASH</span>
                      <span class="col-md-4" id="brackets"><input type="radio"  id="myRadio4" name="vendor_invoice_type" value = "brackets" >BRACKETS</span>


                </div>
            <span id="errmsg2"></span>
              </div>

              <div class="form-group">
            	<label for="name" class="col-md-2">Month<span class="red">*</span></label>
                <div class="col-md-2">
                   <input type="text" class="form-control" name="daterange"  />

                </div>

              </div>
              <span id="errms5"></span>

              <div class="col-md-12 col-md-offset-1" style="margin-top:20px;" >
                  <input type= "submit"  class="btn btn-danger btn-lg"  value ="Generate Invoice" value="2017-01-01-2017-01-31" >
        </div>
          </form>

  </div>
</div>

<?php if($this->session->userdata('success')) { $this->session->unset_userdata('success'); } ?>
<?php if($this->session->userdata('error')) { $this->session->unset_userdata('error'); } ?>
    <script type="text/javascript">
     $("#name").select2();
     
    </script>
   


