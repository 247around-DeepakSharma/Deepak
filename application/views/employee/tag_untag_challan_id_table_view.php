<div class="form-container">
        <?php if($is_tag) 
            { 
                $url = 'mapping_challanId_to_InvoiceId';
                $submit_value = "Tag";
            } 
            else
            {
                $url = 'untag_challan_invoice_id';
                $submit_value = "Untag";
            }
        ?>    
        <form role="form" action="<?php echo base_url(); ?>employee/accounting/<?php echo $url; ?>" method="post">
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
                    <?php $sn = 1;
                    foreach ($challan_details as $key => $value) { ?>
                    <input type="hidden" id="challanId_<?php echo $sn; ?>" value="<?php echo $value['id']; ?>" name="challan_id[]" disabled>
                    <tr> 
                        <td><?php echo $sn; ?></td>
                        <td> <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY ?>/vendor-partner-docs/<?php echo $value['challan_file']; ?>" ><?php echo $value['serial_no'] ?></a></td>
                        <td><?php echo $value['from_date'] ?></td>
                        <td><?php echo $value['to_date'] ?></td>
                        <td><?php echo $value['challan_tender_date'] ?></td>
                        <td><?php echo round($value['amount']) ?></td>
                        <td>
                            <a target="_blank" href="<?php echo base_url(); ?>employee/accounting/get_challan_edit_form/<?php echo $value['id']; ?>">
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
        <?php $sn++;
    } ?>   
                <tr> 
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center"><input type="submit" class="btn btn-success" value="<?php echo $submit_value; ?>"></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>