<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
         	<?php if(isset($sucess) && $sucess !==0) {
               echo '<div class="alert alert-success alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $sucess . '</strong>
               </div>';
               }
               ?>
               <h1 class="page-header">
                <i class="fa fa-sign-in"></i>  <?php echo ucfirst($report_message);?>
            </h1>

              <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="<?php echo base_url()?>#">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-share"></i> Report Message
               </li>
            </ol>
   
            <form class="form-horizontal" action="<?php echo base_url()?>report_message" method="POST" >
            	 <div class="form-group <?php if( form_error('report_message') ) { echo 'has-error';} ?>">
                  <label for="share text" class="col-md-2">Report Message </label>
                  <div class="col-md-6">
                     <input type="text" class="form-control"  name="report_message" value = "<?php if(isset($report_message)) { echo $report_message ; } ?>" data-role="tagsinput"  >
                     <?php echo form_error('report_message'); ?>
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