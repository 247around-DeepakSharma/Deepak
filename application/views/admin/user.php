<?php $offset = $this->uri->segment(3); ?>
<div id="page-wrapper">
   <div class="container-fluid">
  <?php if($this->session->userdata('error')) {
         echo '<div class="alert alert-danger alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $this->session->userdata('error') . '</strong>
         </div>';
         }
         ?>
         <?php if($this->session->userdata('success')) {
         echo '<div class="alert alert-success alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $this->session->userdata('success') . '</strong>
         </div>';
         }
         ?>
          <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               User <small></small>
            </h1>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   User
               </li>
            </ol>
         </div>
      </div>

       <?php if(is_array($result) && sizeof($result)>0){ ?>
      <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
      <div class="pagination " style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div>
      <table class="table table-bordered table-hover table-striped data"  >
         <thead>
            <tr bgcolor="#ccffff">
                              <th >No #</th>
                              <th>Name</th>
                              <th>Phone</th>
                              <th>Email</th>
                             
                              <!--<th>update Date</th> -->
                              <th>Create Date</th>
                               <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                          <?php foreach($result as $key =>$getuserdetails) {?>
                        <tbody>
                           <tr>
                              <td><?php echo $getuserdetails['user_id']; ?></td>
                              <td><?php echo $getuserdetails['name']; ?></td>
                               <td><?php echo $getuserdetails['phone_number']; ?></td>
                              <td><?php echo $getuserdetails['user_email']; ?></td>
                             <!-- <td><?php echo $getuserdetails['update_date']; ?></td> -->
                              <td><?php echo $getuserdetails['create_date']; ?></td>
                              <td><?php if($getuserdetails['action'] ==0) { echo "Inactive"; } else { echo "Active" ;} ?></td>
                              <td>
                                <?php if($getuserdetails['action']==1) {?>
                                <a class="btn btn-small btn-danger"  href="<?php echo base_url();?>user/deactivate/<?php echo $getuserdetails['user_id'];?><?php echo "/".$offset?>">Deactive</a></td>
                                <?php } if($getuserdetails['action']==0){?>
                                  <a class="btn btn-small btn-success"  href="<?php echo base_url();?>user/toDooactive/<?php echo $getuserdetails['user_id'];?><?php echo "/".$offset?>">Active</a></td>
                          <?php } ?>
                           </tr>
                           <?php }  ?>
                             
                        </tbody>
                     </table>
                       <div class="pagination" style="float:right;"> <?php echo $paginglinks; ?></div>
      <div class="pagination" style="float:left;"> <?php echo (!empty($pagermessage) ? $pagermessage : ''); ?></div>
      <?php }else{?>
      <p align="center" style="padding-top:20px;">
        <?php if(!empty($offset)){ redirect(base_url()."user/viewuser");} else { echo "Record Not Found" ;}?>
      </p>
      <?php }?>

    <!--wrapper-->
   </div>
   </div>
   <?php $this->session->unset_userdata('success'); ?>
   <?php $this->session->unset_userdata('error'); ?>
