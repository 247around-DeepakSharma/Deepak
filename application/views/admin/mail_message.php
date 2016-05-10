<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
         	<?php if(isset($success) && $success !==0) {
               echo '<div class="alert alert-success alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $success . '</strong>
               </div>';
               }
               ?> 
               <h1 class="page-header">
                <i class="fa fa-sign-in"></i>  <?php  echo ucfirst($message[0]['message']);?>
            </h1>

              <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="<?php echo base_url()?>#">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-share"></i> Mail Message
               </li>
            </ol>
   
            <form class="form-horizontal" action="<?php echo base_url()?>marketingmail/marketing_mail_message" method="POST" >
               <div class="form-group <?php if( form_error('subject') ) { echo 'has-error';} ?>">
                  <label for="share text" class="col-md-2">Mail Subject </label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="subject" value = "<?php if(isset($message[0]['subject'])) { echo $message[0]['subject'] ; } ?>" data-role="tagsinput"  >
                     <?php echo form_error('subject'); ?>
                  </div>
               </div>
            	 <div class="form-group <?php if( form_error('message') ) { echo 'has-error';} ?>">
                  <label for="share text" class="col-md-2">Marketing Mail Message </label>
                  <div class="col-md-6">
                      <textarea type="text" class="form-control" rows="4" cols="50" name="message"  value = "" data-role="tagsinput" ><?php if(isset($message[0]['message'])) { echo $message[0]['message'] ; } ?></textarea  >
                     <?php echo form_error('message'); ?>
                  </div>
               </div>
               
                <div class="form-group">
                  <div class="col-md-10">
                     <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Sent Mail" style="width:33%"></center>
                  </div>
               </div>
            </form>
        
           </div>
       </div>
   </div>
</div>