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
               Import Excel File to Add Handyman
            </h1>
            <ol class="breadcrumb">
               <li>
                  <i class="fa fa-dashboard"></i>  <a href="<?php echo base_url()?>admin/dashboard">Dashboard</a>
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-edit"></i> handyman
               </li>
            </ol>
            <form class="form-horizontal" action="<?php echo base_url()?>excel/upload" method="POST"  enctype="multipart/form-data">
            	<div class="form-group">
                  <label for="excel" class="col-md-2" style="font-size:19px;">Excel File</label>
                  <div class="col-md-6">
                     <input type="file" class="form-control"  name="file" required>
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

