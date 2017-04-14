<?php if($is_ajax){ ?>
<div class="form-container">
    <form role="form" action="<?php echo base_url(); ?>employee/invoice/mapping_challanId_to_InvoiceId" method="post">
        <table class="table table-bordered table-hover table-responsive">
            <thead>
                <tr>
                    <th> S.No.</th>
                    <th> Challan Serial Number</th>
                    <th colspan="2"> Challan Period</th>
                    <th> Payment Date</th>
                    <th> Amount</th>
                    <th>Edit</th>
                    <th> Insert Invoice Id</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th>From</th>
                    <th>To</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                    <?php $sn = 1;foreach($challan_details as $key =>$value){ ?>
            <input type="hidden" id="challanId_<?php echo $sn; ?>" value="<?php echo $value['id'] ; ?>" name="challan_id[]" disabled>
                        <tr> 
                            <td><?php echo $sn; ?></td>
                            <td> <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY ?>/vendor-partner-docs/<?php echo $value['challan_file']; ?>" ><?php echo $value['serial_no'] ?></a></td>
                            <td><?php echo $value['from_date']?></td>
                            <td><?php echo $value['to_date'] ?></td>
                            <td><?php echo $value['challan_tender_date'] ?></td>
                            <td><?php echo round($value['amount']) ?></td>
                            <td>
                                <a target="_blank" href="<?php echo base_url();?>employee/invoice/get_challan_edit_form/<?php echo $value['id'] ; ?>">
                                    <div class="btn btn-primary">Edit</div>
                                </a>
                            </td>
                            <td>
                                <div class="input-group" style="width: 100%">
                                    <textarea class="form-control" id="invoiceId_<?php echo $sn; ?>" name="invoice_id[]" disabled></textarea>
                                    <span class="input-group-addon"><input type="checkbox" id="isCheckedInvoiceId_<?php echo $sn; ?>" onchange="return validate(this.id)"></span>
                                </div>
                            </td>
                        </tr>
                    <?php $sn++;} ?>   
                        <tr> 
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center"><input type="submit" class="btn btn-success"></td>
                        </tr>
            </tbody>
        </table>
    </form>
</div>
<?php } else { ?> 
<style>
    .input-group-addon{
            background-color: #31b0d5;
            border-color: #269abc;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="challan_details_container" style="border: 1px solid #e6e6e6; margin-top: 20px; padding: 10px;">
            <?php
                    if ($this->session->flashdata('success_msg')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('success_msg').'</strong>
                    </div>';
                    }
                    if ($this->session->flashdata('error_msg')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('error_msg').'</strong>
                    </div>';
                    }
                    ?>
            <section class="serach_challan" style="margin: 10px;">
                <div class="row">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="challan_type">Select challan Type</label>
                            <select class="form-control" id="challan_type">
                                <option disabled selected>Select challan type</option>
                                <option value="ST">Service Tax</option>
                                <option value="VAT">VAT</option>
                                <option value="TDS">TDS</option>
                                <option value="ALL">ALL</option>
                            </select>
                        </div>
                    </form>
                </div>
            </section>
            <section class="challan_details_table" style="margin-top: 40px;">
                <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            </section>
        </div>
    </div>        
</div>
<script src="<?php echo base_url();?>js/base_url.js"></script>
<script>
    $('#challan_type').change(function(){
       var type = this.value;
       $('#loader').show();
       $.ajax({
          method:'POST',
          data:{challan_type: type},
          url:'<?php echo base_url(); ?>employee/invoice/fetch_challan_details/',
          success:function(response){
                //console.log(response);
                $('#loader').hide();
                $('.challan_details_table').html(response);
          }
       });
    });
    
    //Adding Validation
    function validate(id) {
           var id = id.split("_")[1];
           if ($('#isCheckedInvoiceId_' + id).is(':checked')) {
               $("#invoiceId_"+id).attr('required', true);
               $("#invoiceId_"+id).attr('disabled', false);
               $("#challanId_"+id).attr('required', true);
               $("#challanId_"+id).attr('disabled', false);
           } else {
               $("#invoiceId_"+id).attr('required', false);
               $("#invoiceId_"+id).attr('disabled', true);
               $("#challanId_"+id).attr('required', false);
               $("#challanId_"+id).attr('disabled', true);
           }
       }
</script>
<?php }?>