<div id="page-wrapper">
    <div class="container-fluid">
        <div class="page_title">
            <h3>Bank Details</h3>
        </div>
        <hr>
        <div class="page_content ">
            <table class="table table-condensed table-hover table-bordered text-center">
                <thead>
                    <th class="text-center">S.No.</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Bank Account No.</th>
                    <th class="text-center">Bank Name</th>
                    <th class="text-center">IFSC Code</th>
                    <th class="text-center">Payee Name</th>
                    <th class="text-center">Canceled Check file</th>
                    <th class="text-center">Verified</th>
                    <th class="text-center"colspan="2">Action</th>
                </thead>
                <tbody>
                    <?php $sn = 1; foreach ($bank_details as $value) {?> 
                    <tr>
                        <td><?php echo $sn; ?></td>
                        <td><?php echo $value['name']; ?></td>
                        <td><?php echo $value['bank_account']; ?></td>
                        <td><?php echo $value['bank_name']; ?></td>
                        <td><?php echo $value['ifsc_code']; ?></td>
                        <td><?php echo $value['beneficiary_name']; ?></td>
                        <td><?php if(!empty($value['cancelled_cheque_file'])){ ?>
                            <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY."/vendor-partner-docs/".$value['cancelled_cheque_file'] ?>"><i class="fa fa-eye fa-2x" aria-hidden="true"></i></a>
                            <?php }else{ ?>
                            <div class="empty_image">
                                <i class="fa fa-times fa-2x" aria-hidden="true"></i>
                            </div>
                            <?php } ?>
                        </td>
                        <td><?php if(!empty($value['is_verified'])){ ?>
                            <span class="label label-success">Yes</span>
                            <?php } else{ ?>
                            <span class="label label-danger">No</span>
                            <?php }?>
                        </td>
                        <td><button class="btn btn-success" id="approve_<?php echo $value['entity_id'];?>" onclick="verify_bank_details('<?php echo $value['entity_id']; ?>','approve')" <?php ?>>Approve</button></td>
                        <td><button class="btn btn-danger"  id="reject_<?php echo $value['entity_id'];?>" onclick="verify_bank_details('<?php echo $value['entity_id']; ?>','reject')">Reject</button></td>
                    </tr>
                    <?php $sn++;}?>
                </tbody>
            </table>
        </div>
        
        <script>
            function verify_bank_details(id,action){
                if(action === 'approve'){
                    $('#approve_'+id).html("<div>Processing...</div>");
                }else if(action === 'reject'){
                    $('#reject_'+id).html("<div>Processing...</div>");
                }
                
                $.ajax({
                    type:'POST',
                    data:{id:id,type:'SF',action:action},
                    url:"<?php echo base_url(); ?>employee/vendor/verify_bank_details",
                    success:function(response){
                        if(action === 'approve'){
                            $('#approve_'+id).html("<div>Approve</div>");
                        }else if(action === 'reject'){
                            $('#reject_'+id).html("<div>Reject</div>");
                        }  
                        
                        if(response === 'success'){
                            alert("Details has been updated successfully");
                        }else if(response === 'fail'){
                            alert("Error in updating details");
                        }
                    }
                    
                });
            }
        </script>
    </div>
</div>