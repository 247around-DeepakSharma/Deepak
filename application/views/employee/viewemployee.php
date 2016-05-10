<div id="page-wrapper">
   <div class="container-fluid">
      <!-- Page Heading -->
      <div class="row">
         <div class="col-lg-12">
            <h2 class="page-header">
               List of Employee and their Authority
            </h2>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   View Service
               </li>
            </ol>
         </div>
      </div>
      <?php if(is_array($result) && sizeof($result)>0){ ?>
      <?php if(!empty($paginglinks)) {?>
      <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
      <div class="pagination " style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div>
      <?php } ?>
      <div class="table-responsive">
         <table class="table table-bordered table-hover table-striped">
            <thead>
               <tr  bgcolor="#99cccc">
                  <th>No #</th>
                  <th>Employee Id/Name</th>
                  <th colspan="15" style="text-align:center">Authentication Of Employee</th>
                    <?php  if($this->session->userdata('update_employee')) {?>
                  <th colspan="2">Action</th>
                 <?php } ?>
               </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Add handyman</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Activate/Deactivate handyman</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Delete handyman</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Approve Handyman</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Verify Handyman</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Import XLS file for handyman</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Add Service</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Activate/Deactivate Service</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Create Employee</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Update Employee</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Review Message</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Signup Message</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Report Message</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Popular Serach</b></td>
                  <td  bgcolor="#ccffff" style="width:11%"><b>Review</b></td>
               </tr>
            </thead>
            <tbody>
              
               <?php foreach($result as $key =>$row) {?>
               <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo $row['employee_id']; ?></td>
                  <td ><?php if($row['right_for_add_handyman']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_activate_deactivate_handyman']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_delete']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_approve_handyman']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_verifyhandyman']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_xls_for_handyman']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_add_service'] == 1){ echo "Yes"; } else {echo "No";} ?></td>
                  <td ><?php if($row['right_for_activate_deactivate_service']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_add_employee']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_update_employee']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_review_message']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_signup_message']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_report_messgae']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_popularsearch']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <td ><?php if($row['right_for_review']==1){ echo "Yes"; } else {echo "No";}?></td>
                  <?php  if($this->session->userdata('update_employee')) {?>
               <td>
                  <a class="btn btn-small btn-success"  href="<?php echo base_url();?>employee/employee/update/<?php echo $row['id'] ?>">Edit</a>
               </td>
               <td>
                     <a class="btn btn-small btn-danger"  href="<?php echo base_url();?>employee/employee/delete/<?php echo $row['id'] ?>">Delete</a>
                  </td>
               <?php } ?>
               </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
      <?php if(!empty($paginglinks)) {?>
      <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
      <div class="pagination" style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div>
      <?php } ?>
      <?php }?>
      <!-- /.row -->
   </div>
   <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
</div>
<?php $this->session->unset_userdata('success'); ?>
