<table class="table table-bordered table-hover table-responsive">
                <thead>
                    <tr>
                        <th> S.No.</th>
                        <th> Challan Serial Number</th>
                        <th colspan="2"> Challan Period</th>
                        <th> Payment Date</th>
                        <th> Amount</th>
                        <th>Edit</th>
                        <th>View Tagged Invoices</th>
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
                        <td><a target="_blank" href="<?php echo base_url(); ?>employee/accounting/get_tagged_incoice_challan_data/<?php echo $value['id']; ?>"><div class="btn btn-info">View</div></a></td>
                    </tr>
        <?php $sn++;
    } ?>   
                </tbody>
            </table>