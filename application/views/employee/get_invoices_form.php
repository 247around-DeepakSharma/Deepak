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
         //document.getElementById("myRadio1").disabled = true;
         document.getElementById("myRadio2").disabled = true;
         document.getElementById("myRadio3").disabled = true;
         document.getElementById("brackets").style.display = "none";
         document.getElementById("brackets").disabled = true;
         document.getElementById("myRadio4").disabled = true;
         document.getElementById("myRadio5").disabled = true;
     } else {
      //  document.getElementById("myRadio1").disabled = false;
         document.getElementById("myRadio2").disabled = false;
         document.getElementById("myRadio3").disabled = false;
         document.getElementById("brackets").style.display = "block";
         document.getElementById("brackets").disabled = false;
         document.getElementById("myRadio4").disabled = false;
         document.getElementById("myRadio5").disabled = false;
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
                    var partner_invoice_id  = $("#name").val();
                    if((partner_invoice_id[0] === "All" && partner_invoice_id.length >= 1) || partner_invoice_id.length > 1){
                        var vendor_partner = $("input[name='partner_vendor']:checked").val();
                        
                        if(vendor_partner === "vendor"){
                        var fd = new FormData(document.getElementById("myForm1"));
                            fd.append("label", "WEBUPLOAD");
                            $('#submitform').attr('disabled', true);
                            $('#submitform').val("Please wait.....");
                            $.ajax({
                                    type: 'POST',
                                    url: '<?php echo base_url() ?>employee/invoice/process_invoices_form',
                                    data: fd,
                                    processData: false,
                                    contentType: false,
                                    success: function (data) {
                                      console.log(data);
                                      $('#submitform').attr('disabled', false);
                                      $('#submitform').val("Generate Invoice");

                                    }
                                  });
                                  alert("Process submitted. Please Wait..");
                            return false;
                        } else {
                         alert("We can not generate partner All invoice");
                         return false;
                        }
                      
                        
                    } else {
                        
                        form.submit();
                        $('#submitform').attr('disabled', true);
                        $('#submitform').val("Please wait.....");
                    }
                    
                    
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
               format: 'DD/MM/YYYY'
            },
            startDate: '<?php echo date("01/m/Y", strtotime("-1 month")) ?>',
            endDate: '<?php echo date('d/m/Y', strtotime('last day of previous month')); ?>'
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
		      <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);"  name="partner_vendor"  checked="checked" value = "vendor" >    Service Center &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type="radio" onclick="partner_vendor1(<?php echo $id; ?>);" name="partner_vendor" value = "partner" >    Partner &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		  </div>
              </div>
              <img id="loader_gif" src="" class="col-md-offset-2" style="width:50px;">

             <div class="form-group ">
              <label for="name" class="col-md-2">Company Name</label>
                <div class="col-md-3">
                    <select class="form-control"  id="name" name="partner_vendor_id[]"  required multiple=""></select>
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
<!--		     <span class="col-md-2"><input type="radio"  id="myRadio1" name="vendor_invoice_type" value = "all" checked >All </span>-->
                      <span class="col-md-2"><input type="radio"  id="myRadio2" name="vendor_invoice_type" value = "foc" checked>FOC</span>
                      <span class="col-md-2"><input type="radio"  id="myRadio3" name="vendor_invoice_type" value = "cash" >CASH</span>
                      <span class="col-md-4" id="brackets"><input type="radio"  id="myRadio4" name="vendor_invoice_type" value = "brackets" >BRACKETS</span>
                      <span class="col-md-2" ><input type="radio"  id="myRadio5" name="vendor_invoice_type" value = "buyback" >Buyback</span>
                      


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
                  <input type= "submit" id="submitform"  class="btn btn-danger btn-lg" value ="Generate Invoice"  >
        </div>
          </form>
 
  </div>
</div>

<?php if($this->session->userdata('success')) { $this->session->unset_userdata('success'); } ?>
<?php if($this->session->userdata('error')) { $this->session->unset_userdata('error'); } ?>
    <script type="text/javascript">
     $("#name").select2();
    
    </script>
   


