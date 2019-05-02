<?php if(!$is_ajax){ ?>
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="page_title">
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <h3>Bank Details</h3>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <select class="form-control" id="sf_type" style="margin-top:13px;">
                                    <option value="1">Active</option>
                                    <option value="0">Disabled</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="col-md-4">
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
            <?php if(!empty($bank_details)) { ?>
                <div class="page_content ">
                    <table class="table table-condensed table-hover table-bordered text-center" id="bank_details">
                        <thead>
                            <th class="text-center">S.No.</th>
                            <th class="text-center">RM Name</th>
                            <th class="text-center">SF Name</th>
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
                                <td><?php echo $value['rm_name']; ?></td>
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


                                <td><button class="btn btn-success" id="approve_<?php echo $value['entity_id'];?>" onclick="verify_bank_details('<?php echo $value['entity_id']; ?>','approve')" <?php if(!empty($value['is_verified'])){ echo "disabled";}?> >Approve <?php  //echo $value['is_verified']; ?></button></td>


                                <td><button class="btn btn-danger"  id="reject_<?php echo $value['entity_id'];?>" onclick="verify_bank_details('<?php echo $value['entity_id']; ?>','reject', '<?php echo $value['rm_email']; ?>', '<?php echo $value['primary_contact_email']; ?>', '<?php echo $value['owner_email']; ?>','<?php echo $value['name']; ?>')">Reject</button></td>
                            </tr>
                            <?php $sn++;}?>
                        </tbody>
                    </table>
                </div>
            <?php }else{ ?>
            <div class="alert alert-danger">
                <div class="text-center">No Data Found</div>
            </div>
            <?php } ?>




    <link href="https://connect.amperecomputing.com/css/sweetalert.css" rel="stylesheet">
 
    <script src="https://connect.amperecomputing.com/js/sweetalert.min.js"></script>




            <script>
                function verify_bank_details(id,action,rm_email,poc_email,owner_email,sf_name){
                    if(action === 'approve'){
                      //  $('#approve_'+id).html("<div>Processing...</div>");
                    }else if(action === 'reject'){
                       // $('#reject_'+id).html("<div>Processing...</div>");
                    }
                    /////////////// START AJAX ////////////////

if (action === 'approve') {
var verify = "Verified";
var color = "#5cb85c";
}else{
var verify = "Rejected";
var color ="#3DD6B55";
}
swal({
  title: "Are you sure?",
  text: "SF bank details will be  "+verify,
  type: "info",
  showCancelButton: true,
  confirmButtonColor: color,
   cancelButtonColor: "#DD6B55", 
  confirmButtonClass: "btn-danger",
  confirmButtonText: "Yes, "+verify+" it!",
  cancelButtonText: "No, cancel please!",
  closeOnConfirm: false,
  closeOnCancel: false
},
function(isConfirm) {
  if (isConfirm) {

                    $.ajax({
                        type:'POST',
                        data:{id:id,type:'SF',action:action,rm_email:rm_email,poc_email:poc_email,owner_email:owner_email,sf_name:sf_name},
                        url:"<?php echo base_url(); ?>employee/vendor/verify_bank_details",
                        success:function(response){
                            if(action === 'approve'){
                                $('#approve_'+id).html("<div>Approve</div>");
                            }else if(action === 'reject'){
                                $('#reject_'+id).html("<div>Reject</div>");
                            }  

                            if(response === 'success'){
                             //   alert("Details has been updated successfully");

                                 swal({title: verify, text: "SF Bank Details are  "+verify, type: "success"},
                                 function(){ 
                                 location.reload();
                                 }
                                 );
                                $('#'+id+'_details').hide();
                            }else if(response === 'fail'){
                               swal("Error", "Error in updating Bank Details", "error");
                            }
                        }

                    });
      // $.ajax({url: "deletedoctype/"+row['id'], success: function(result){
      // }});
//     swal({title: "Approved", text: "SF Bank Details are verified ", type: "success"},
//     function(){ 
//        location.reload();
//    }
// );

  } else {
    swal("Cancelled", "SF Bank Details are not "+verify, "error");
  }
});





                    // $.ajax({
                    //     type:'POST',
                    //     data:{id:id,type:'SF',action:action,rm_email:rm_email,poc_email:poc_email,owner_email:owner_email,sf_name:sf_name},
                    //     url:"<?php //echo base_url(); ?>employee/vendor/verify_bank_details",
                    //     success:function(response){
                    //         if(action === 'approve'){
                    //             $('#approve_'+id).html("<div>Approve</div>");
                    //         }else if(action === 'reject'){
                    //             $('#reject_'+id).html("<div>Reject</div>");
                    //         }  

                    //         if(response === 'success'){
                    //             alert("Details has been updated successfully");
                    //             $('#'+id+'_details').hide();
                    //         }else if(response === 'fail'){
                    //             alert("Error in updating details");
                    //         }
                    //     }

                    // });





                    ////////END AJAX ///////////////
                }

                $('#sf_type').change(function(){
                    var sf_type = $('#sf_type').val();
                    var is_bank_details_verified = $('#is_bank_details_verified').val();
                    var rm_id = $('rm_id').val();
                    $('.page_content').html("<div class = 'text-center'><i class = 'fa fa-spinner fa-spin fa-4x'></i></div>");
                    $.ajax({
                        type:'POST',
                        data:{sf_type:sf_type,is_bank_details_verified:is_bank_details_verified,rm_id:rm_id},
                        url:"<?php echo base_url(); ?>employee/vendor/show_bank_details",
                        success:function(response){
                            $('.page_content').html(response);
                        }

                    });
                });
                
                $('#is_bank_details_verified').change(function(){
                    var sf_type = $('#sf_type').val();
                    var is_bank_details_verified = $('#is_bank_details_verified').val();
                    var rm_id = $('rm_id').val();
                    $('.page_content').html("<div class = 'text-center'><i class = 'fa fa-spinner fa-spin fa-4x'></i></div>");
                    $.ajax({
                        type:'POST',
                        data:{sf_type:sf_type,is_bank_details_verified:is_bank_details_verified,rm_id:rm_id},
                        url:"<?php echo base_url(); ?>employee/vendor/show_bank_details",
                        success:function(response){
                            $('.page_content').html(response);
                        }

                    });
                });
                
                $('#rm_id').change(function(){
                    var sf_type = $('#sf_type').val();
                    var is_bank_details_verified = $('#is_bank_details_verified').val();
                    var rm_id = $('rm_id').val();
                    $('.page_content').html("<div class = 'text-center'><i class = 'fa fa-spinner fa-spin fa-4x'></i></div>");
                    $.ajax({
                        type:'POST',
                        data:{sf_type:sf_type,is_bank_details_verified:is_bank_details_verified,rm_id:rm_id},
                        url:"<?php echo base_url(); ?>employee/vendor/show_bank_details",
                        success:function(response){
                            $('.page_content').html(response);
                        }

                    });
                });
            </script>
        </div>
    </div>
 <?php }else if($bank_details){ ?>
    <table class="table table-condensed table-hover table-bordered text-center" id="bank_details">
        <thead>
        <th class="text-center">S.No.</th>
        <th class="text-center">RM Name</th>
        <th class="text-center">SF Name</th>
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
            <tr id="<?php echo $value['entity_id'].'_details' ;?>">
                <td><?php echo $sn; ?></td>
                <td><?php echo $value['rm_name']; ?></td>
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
                <td><button class="btn btn-danger"  id="reject_<?php echo $value['entity_id']; ?>" onclick="verify_bank_details('<?php echo $value['entity_id']; ?>','reject','<?php echo $value['rm_email']; ?>', '<?php echo $value['primary_contact_email']; ?>', '<?php echo $value['owner_email']; ?>','<?php echo $value['name']; ?>')">Reject</button></td>
            </tr>
        <?php $sn++;
    } ?>
    </tbody>
    </table>
 <?php }else{ ?> 
<div class="alert alert-danger">
    <div class="text-center">No Data Found</div>
</div>
 <?php } ?>