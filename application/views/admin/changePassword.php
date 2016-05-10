<div id="page-wrapper">
   <div class="container-fluid">
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
               Chnage Password 
            </h1>
            
         </div>
      </div>


         <form action="<?php echo base_url()?>admin/reset_password" class="form-horizontal" method="POST">
            <div class="form-group <?php if( form_error('email') ) { echo 'has-error';} ?>">
               <label for="admin email" class="col-md-2">Email</label>
               <div class="col-md-4">
                  <input type="email" class="form-control"  name="email"  value="<?php echo set_value('email'); ?>"  >
                  <?php echo form_error('email'); ?>
               </div>
            </div>
             <div class="form-group <?php if( form_error('oldpassword') ) { echo 'has-error';} ?>">
               <label for="admin email" class="col-md-2">Old Password</label>
               <div class="col-md-4">
                  <input type="password" class="form-control"  name="oldpassword"    >
                  <?php echo form_error('oldpassword'); ?>
               </div>
            </div>
            <div class="form-group <?php if( form_error('password') ) { echo 'has-error';} ?>">
               <label for="admin email" class="col-md-2">Password</label>
               <div class="col-md-4">
                  <input type="password" class="form-control"  name="password"    >
                  <?php echo form_error('password'); ?>
               </div>
            </div>
            <div class="form-group <?php if( form_error('passconf') ) { echo 'has-error';} ?>">
               <label for="admin email" class="col-md-2">Confirm Password</label>
               <div class="col-md-4">
                  <input type="password" class="form-control"  name="passconf"    >
                  <?php echo form_error('passconf'); ?>
               </div>
            </div>
            <div class="form-group">
               <div class="col-md-7">
                  <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:30%"></center>
               </div>
            </div>
         </form>
     
   </div>
</div>
</body>
</html>
<?php $this->session->unset_userdata('success'); ?>
