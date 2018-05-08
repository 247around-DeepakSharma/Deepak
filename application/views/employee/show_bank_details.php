<?php if(!$is_ajax){ ?>
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="page_title">
                <div class="row">
                    <div class="col-lg-6 col-xs-6">
                        <h3>Bank Details</h3>
                    </div>
                    <div class="col-lg-6 col-xs-6">
                        <div class="col-lg-10">
                            <div class="col-sm-6 pull-right">
                                <select class="form-control" id="sf_type" style="margin-top:13px;">
                                    <option value="1">Active</option>
                                    <option value="0">Disabled</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select class="form-control" id="is_bank_details_verified" style="margin-top:13px;">
                                    <option value="0">Not Verified</option>
                                    <option value="1">Verified</option>
                                </select>
                            </div>
                      </div>
                    </div>
                </div>
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
                        <tr id="<?php echo $value['entity_id'].'_details' ;?>">
                            <td><?php echo $sn; ?></td>
                            <td>
                                <a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $value['entity_id'];?>"><?php echo $value['name']; ?></a>
                            </td>
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
                            <td><button class="btn btn-success" id="approve_<?php echo $value['entity_id'];?>" onclick="verify_bank_details('<?php echo $value['entity_id']; ?>','approve')" <?php if(!empty($value['is_verified'])){ echo "disabled";}?>>Approve</button></td>
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
                                $('#'+id+'_details').hide();
                            }else if(response === 'fail'){
                                alert("Error in updating details");
                            }
                        }

                    });
                }

                $('#sf_type').change(function(){
                    var sf_type = $('#sf_type').val();
                    var is_bank_details_verified = $('#is_bank_details_verified').val();
                    $('.page_content').html("<div class = 'text-center'><i class = 'fa fa-spinner fa-spin fa-4x'></i></div>");
                    $.ajax({
                        type:'POST',
                        data:{sf_type:sf_type,is_bank_details_verified:is_bank_details_verified},
                        url:"<?php echo base_url(); ?>employee/vendor/show_bank_details",
                        success:function(response){
                            $('.page_content').html(response);
                        }

                    });
                });
                
                $('#is_bank_details_verified').change(function(){
                    var sf_type = $('#sf_type').val();
                    var is_bank_details_verified = $('#is_bank_details_verified').val();
                    $('.page_content').html("<div class = 'text-center'><i class = 'fa fa-spinner fa-spin fa-4x'></i></div>");
                    $.ajax({
                        type:'POST',
                        data:{sf_type:sf_type,is_bank_details_verified:is_bank_details_verified},
                        url:"<?php echo base_url(); ?>employee/vendor/show_bank_details",
                        success:function(response){
                            $('.page_content').html(response);
                        }

                    });
                });
            </script>
        </div>
    </div>
 <?php }else { ?>
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
        <?php $sn = 1;
        foreach ($bank_details as $value) { ?> 
            <tr>
                <td><?php echo $sn; ?></td>
                <td>
                    <a href="<?php echo base_url();?>employee/vendor/viewvendor/<?php echo $value['entity_id'];?>"><?php echo $value['name']; ?></a>
                </td>
                <td><?php echo $value['bank_account']; ?></td>
                <td><?php echo $value['bank_name']; ?></td>
                <td><?php echo $value['ifsc_code']; ?></td>
                <td><?php echo $value['beneficiary_name']; ?></td>
                <td><?php if (!empty($value['cancelled_cheque_file'])) { ?>
                        <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $value['cancelled_cheque_file'] ?>"><i class="fa fa-eye fa-2x" aria-hidden="true"></i></a>
        <?php } else { ?>
                        <div class="empty_image">
                            <i class="fa fa-times fa-2x" aria-hidden="true"></i>
                        </div>
        <?php } ?>
                </td>
                <td><?php if (!empty($value['is_verified'])) { ?>
                        <span class="label label-success">Yes</span>
                    <?php } else { ?>
                        <span class="label label-danger">No</span>
        <?php } ?>
                </td>
                <td><button class="btn btn-success" id="approve_<?php echo $value['entity_id']; ?>" onclick="verify_bank_details('<?php echo $value['entity_id']; ?>','approve')" <?php if (!empty($value['is_verified'])) {
            echo "disabled";
        } ?>>Approve</button></td>
                <td><button class="btn btn-danger"  id="reject_<?php echo $value['entity_id']; ?>" onclick="verify_bank_details('<?php echo $value['entity_id']; ?>','reject')">Reject</button></td>
            </tr>
        <?php $sn++;
    } ?>
    </tbody>
    </table>
 <?php } ?>