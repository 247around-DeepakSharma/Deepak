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
       $('#loader_gif').attr('src',  "<?php echo base_url(); ?>images/loadring.gif");
       
        $.ajax({
                  type: 'POST',
                  url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + par_ven,
                  data: {vendor_partner_id: vendor_partner_id,invoice_flag:0},
                  success: function (data) {
                      //console.log(data);
                      $("#name").select2().html(data).change();
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
<div id="page-wrapper" >
    <div class="container-fluid" >
        <?php if($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:20;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                    }
                    ?>
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading text-center">
                <h4><?php if(isset($bank_txn_details)){ echo "Update Transaction"; } else { echo "Add New Transaction";} ?></h4>
            </div>
            <form name="myForm1" id="myForm1" class="form-horizontal" action="<?php echo base_url()?>employee/invoice/post_add_new_transaction" method="POST">
                <div class="panel-body">
                    <input type="hidden" name ="bank_txn_id" value="<?php if(isset($bank_txn_details)){ echo $bank_txn_details[0]['id'];} else { echo "";}?>" />
                    <div class="row">
                        <div class="col-md-12">
                            <center><img id="loader_gif" src=""></center>
                        </div>
                        <div class="col-md-12" >
                            <table class="table priceList table-striped table-bordered">
                                <thead >
                                    <th class="text-center">
                                        <div class="form-group ">
                                            <label class="radio-inline">
                                                <input name="partner_vendor" type="radio"  onclick="partner_vendor1(<?php echo $id; ?>);"  name="partner_vendor" <?php if($vendor_partner ==""){ echo "checked"; } else if($vendor_partner == "vendor"){ echo "checked"; }?> value = "vendor" ><b>Service Center</b>
                                            </label>
                                            <label class="radio-inline">
                                            <input type="radio" <?php if($vendor_partner == "partner"){ echo "checked"; } ?> onclick="partner_vendor1(<?php echo $id; ?>);" name="partner_vendor" value = "partner"><b>Partner</b>
                                            </label>
                                        </div>
                                    </th>
                                    <th></th>
                                    <th class="text-center">
                                        <div class="form-group ">
                                            <!--              <label for="name" class="col-md-2">Name</label>-->
                                            <select type="text" class="form-control"  id="name" name="partner_vendor_id"  required></select>
                                        </div>
                                    </th>
                                    <th class="text-center">
                                        <div class="form-group ">
                                            <label class="radio-inline">
                                            <input name="transaction_mode" value = "Cash"  type="radio" 
                                                <?php if(isset($bank_txn_details)){ if($bank_txn_details[0]['transaction_mode'] == "Cash"){ echo "checked";}} else { echo "checked";} ?>>Cash
                                            </label>
                                            <label class="radio-inline">
                                            <input  type="radio"  name="transaction_mode" value = "Cheque"
                                                <?php if(isset($bank_txn_details)){ if($bank_txn_details[0]['transaction_mode'] == "Cheque"){ echo "checked";}} else { echo "checked";} ?>> Cheque
                                            </label>
                                            <label class="radio-inline">
                                            <input type="radio"  name="transaction_mode" value = "Transfer"
                                                <?php if(isset($bank_txn_details)){ if($bank_txn_details[0]['transaction_mode'] == "Transfer"){ echo "checked";}} else { echo "checked";} ?>> Transfer
                                            </label>
                                        </div>
                                    </th>
                                </thead>
                                <thead >
                                    <tr >
                                        <th style="border: 0px solid #fff;"></th>
                                        <th style="border: 0px solid #fff;"></th>
                                        <th style="border: 0px solid #fff;"></th>
                                        <th style="border: 0px solid #fff;"></th>
                                    </tr>
                                    <tr>
                                        <th style="border: 0px solid #fff;"></th>
                                        <th style="border: 0px solid #fff;"></th>
                                        <th style="border: 0px solid #fff;"></th>
                                        <th style="border: 0px solid #fff;"</th>
                                    <tr>
                                </thead>
                                <thead >
                                    <tr >
                                        <th style="border-color: #bce8f1;" class="text-center">Invoice ID</th>
                                        <th style="border-color: #bce8f1;" class="text-center">Credit/Debit</th>
                                        <th style="border-color: #bce8f1;" class="text-center">Credit/Debit Amount(IN 247Around)</th>
                                        <th style="border-color: #bce8f1;" class="text-center">TDS Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $flag =0; if(!empty($invoice_id_array)){ foreach ($invoice_id_array as $key => $invoice_id) { $flag =1;?>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" name="invoice_id[]" value="<?php echo $invoice_id; ?>" readonly required />
                                        </td>
                                        <td>
                                            <select class="form-control" name="credit_debit[]" id="<?php echo "cre_amount_".$key; ?>" onchange="check_price_details()" readonly>
                                                <option value="Credit" <?php if($amount_collected[$invoice_id] > 0){ echo "selected"; } ?>>Credit</option>
                                                <option value="Debit" <?php if($amount_collected[$invoice_id] <= 0){ echo "selected"; } ?> >Debit</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control cal_amount" id="<?php echo "cal_amount_".$key; ?>" name="credit_debit_amount[]" value="<?php echo abs($amount_collected[$invoice_id]); ?>" required/>
                                            <input type="hidden" class="form-control" name="pre_credit_amount[]" value="<?php echo abs($amount_collected[$invoice_id]); ?>" />
                                            
                                        </td>
                                        <td>
                                            <input type="text" class="form-control cal_tds_amount" name="tds_amount[]" value="<?php echo $tds_amount[$invoice_id]; ?>" <?php if($vendor_partner == "vendor") {?>readonly <?php } ?> />
                                        </td>
                                    </tr>
                                    <?php } } else { ?>
                                    <tr>
                                        <td>
                                            <input  type="text" class="form-control" name="invoice_id[]" value="" placeholder="Invoice ID" required />
                                        </td>
                                        <td>
                                            <select class="form-control" name="credit_debit[]" id="cre_amount_0" onchange="check_price_details()">
                                                <option value="Credit" >Credit</option>
                                                <option value="Debit" >Debit</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input  type="text" class="form-control cal_amount" id="cal_amount_0" name="credit_amount[]" value="" placeholder="Amount" required/>
                                        </td>
                                        <td>
                                            <input  type="text" class="form-control cal_tds_amount" name="tds_amount[]" value="" placeholder="TDS Amount" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="text" class="form-control" name="invoice_id[]" value="" placeholder="Invoice ID" />
                                        </td>
                                        <td>
                                            <select class="form-control" name="credit_debit[]" id="cre_amount_1" onchange="check_price_details()">
                                                <option value="Credit" >Credit</option>
                                                <option value="Debit" >Debit</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control cal_amount" id="cal_amount_1" name="credit_amount[]" value="" placeholder="Amount" />
                                        </td>
                                        <td>
                                            <input class="form-control cal_tds_amount" name="tds_amount[]" value="" placeholder="TDS Amount" />
                                        </td>
                                    </tr>
                                    <?php } ?>
                                     <tr >
                                        <td style="border: 0px solid #fff;"></td>
                                        <td style="border: 0px solid #fff;"></td>
                                        <td style="border: 0px solid #fff;">
                                        <td>
                                        <td style="border: 0px solid #fff;"></td>
                                    </tr>
                                    <tr >
                                        <th class="text-center">Final</th>
                                        <th class="text-center"><span id='span_c_d'><?php if($selected_amount_collected > 0){ echo 'Credit';} else { echo 'Debit';} ?></span></th>
                                        <th ><input type="text" readonly id="in_amount" value="<?php echo abs($selected_amount_collected); ?>" class="form-control" ></th>
                                        <th >
                                            <input type="text" readonly id="in_tdsamount" value="<?php echo abs($selected_tds); ?>" class="form-control" >
                                           
                                        <th>
                                        
                                    </tr>
                                    <tr >
                                        <td style="border: 0px solid #fff;"></td>
                                        <td style="border: 0px solid #fff;"></td>
                                        <td style="border: 0px solid #fff;">
                                        <td>
                                        <td style="border: 0px solid #fff;"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 0px solid #fff;"></td>
                                        <td style="border: 0px solid #fff;"></td>
                                        <td style="border: 0px solid #fff;">
                                        <td>
                                        <td style="border: 0px solid #fff;"></td>
                                    <tr>
                                    <tr>
                                        <td class="text-center" style="vertical-align: middle;"> 
                                            <input style="margin-bottom:25px;" type="text" name='bankname'  class="form-control" value="<?php if(isset($bank_txn_details)){ echo $bank_txn_details[0]['bankname'];}?>" placeholder="Please Enter Bank Name">
                                            <div class="input-group input-append date" >
                                                <input style="background-color: #fff;" type="text" id="datepicker" class="form-control" name="tdate" readonly='true' value="<?php if(isset($bank_txn_details)){ echo $bank_txn_details[0]['transaction_date'];} else { echo date('Y-m-d');}?>">
                                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td  class="text-center">
                                            <textarea class="form-control"  name="description" cols="5" rows="5" placeholder="Add transaction remarks"><?php if(isset($bank_txn_details)){ echo $bank_txn_details[0]['description'];}?></textarea>
                                        </td>
                                        <td  class="text-center" style="vertical-align: middle;">
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-md-12" style="text-align: center;">
                                <input onclick="return check_amount(<?php echo $flag;?>)" type= "submit"  class="btn btn-primary btn-lg"  value ="Submit Transaction Details" >
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function check_amount(flag){
    
    if(flag === 1){
        var partner_amount = Number($("#partner_amount").val())+Number($('#selected_tds_amount').val());
        var amount = Number($("#amount").val()) +  Number($("#tds_amount").val());
        
        if(amount >partner_amount ){
            console.log(amount); 
            console.log(partner_amount);
            alert("Do not Allow Advance Transaction");
            return false;
        }
        
    }

}

$(document).on('keyup', '.cal_amount', function (e) {
   check_price_details();
});

function check_price_details(){
     f_amount = 0;
    $('.cal_amount').each(function() {
          var id_key = this.id.split('cal_amount_');
          var credit_debit =  $("#cre_amount_"+id_key[1]).val();
          var amount = $("#cal_amount_"+ id_key[1]).val();
         
          if(amount > 0){
            if(credit_debit === "Debit"){
                f_amount = Number(f_amount) - Number(amount);

            } else {
                 f_amount = Number(f_amount) + Number(amount);

            }
          }
    });
    if(f_amount > 0){
       $("#span_c_d").text("Credit"); 
    } else {
        $("#span_c_d").text("Debit"); 
    }
    $("#in_amount").val(Math.abs(f_amount.toFixed(0)));
}

</script>
<?php if($this->session->userdata('success')) { $this->session->unset_userdata('success'); } ?>