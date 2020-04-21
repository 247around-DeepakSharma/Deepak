 <div id="page-wrapper">
   <div class="container-fluid">
 <div class="row">
   <?php if(isset($success) && $success !==0) {
               echo '<div class="alert alert-success alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $success . '</strong>
               </div>';
               }
               ?>
         <div class="col-lg-12">
            <h1 class="page-header">
               Add  Review
            </h1>
            <form class="form-horizontal" action="<?php base_url()?>review" method="POST"  >
               <div class="form-group <?php if( form_error('behaviour') ) { echo 'has-error';} ?>">
                  <label for="Behavior" class="col-md-2">Behaviour</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="behaviour" >
                     <?php echo form_error('behaviour'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('expertise') ) { echo 'has-error';} ?>">
                  <label for="Expertise" class="col-md-2">Expertise</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="expertise"  >
                     <?php echo form_error('expertise'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('review') ) { echo 'has-error';} ?>">
                  <label for="Review" class="col-md-2">Review</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="review"   >
                       <?php echo form_error('review'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('handyman_id') ) { echo 'has-error';} ?>">
                  <label for="handyman_id" class="col-md-2">Handyman id</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="handyman_id"   >
                      <?php echo form_error('handyman_id'); ?>
                  </div>
               </div>
               <div class="form-group <?php if( form_error('user_id') ) { echo 'has-error';} ?>">
                  <label for="user_id" class="col-md-2">User id</label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="user_id"   >
                       <?php echo form_error('user_id'); ?>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Save" style="width:33%"></center>
                  </div>
               </div>
            </form>
         </div>
      </div>

   </div>
</div>
</body>
</html>