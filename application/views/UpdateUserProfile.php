<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <?php if(isset($error) && $error !==0) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $error . '</strong>
               </div>';
               }
               ?>
            <h1 class="page-header">
               Update User Profile
            </h1>
            <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="#">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-edit"></i> <a href="<?php echo base_url()?>pages/user">All User</a>
               </li>
            </ol>
            <?php foreach ($getalluserInfo as $getalluser) {
               ?>
            <form class="form-horizontal" action="<?php echo base_url()?>form/editUserprofile?id=<?php echo $getalluser['id']?>" method="POST"  >
               <div class="form-group">
                  <label for="Name<" class="col-md-2">Name</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control" value ="<?php echo  $getalluser['name']?>" name="name" required>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('phone') ) { echo 'has-error';} ?>">
                  <label for="phone" class="col-md-2">phone</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control" value ="<?php echo  $getalluser['phone']?>" name="phone" >
                  </div>
                  <?php echo form_error('phone'); ?>
               </div>
               <div class="form-group">
                  <label for="home Address" class="col-md-2">Home Address</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="home_address" value ="<?php echo  $getalluser['home_address']?>"  required>
                  </div>
               </div>
               <div class="form-group">
                  <label for="office address" class="col-md-2">Office Address</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="office_address" value ="<?php echo  $getalluser['office_address']?>"  required>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
            </form>
            <?php } ?>
         </div>
      </div>
   </div>
</div>