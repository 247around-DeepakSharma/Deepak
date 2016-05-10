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
                <img src="<?php echo base_url()?>uploads/share.png" style="width:60px; height:60px;">  <?php echo ucfirst($sharetext);?>
            </h1>

              <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="<?php echo base_url()?>#">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-share"></i> Share Text
               </li>
            </ol>
   
            <form class="form-horizontal" action="<?php echo base_url()?>sharetext" method="POST" >
            	 <div class="form-group <?php if( form_error('search') ) { echo 'has-error';} ?>">
                  <label for="share text" class="col-md-2">Share Text </label>
                  <div class="col-md-6">
                     <textarea type="text" class="form-control"  name="sharetext" data-role="tagsinput"  ><?php if(isset($sharetext)) { echo $sharetext ; } ?></textarea>
                     <?php echo form_error('sharetext'); ?>
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
