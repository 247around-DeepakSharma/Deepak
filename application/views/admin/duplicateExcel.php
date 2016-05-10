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
          <h2 class="page-header">
               Import Excel File to Add Handyman
            </h2>
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
         <br/><br/>
        <div class="container" style="margin-top: 54px;">
         <div class="row" >
           <h3>Duplicate Handyman </h3>
         </div>
          <div class="table-responsive">
             <table class="table table-bordered table-hover table-striped data"  >
         <thead>
            <tr>
              <th>No #</th>
               <th>Profle Photo</th>
               <th>Name</th>
               <th>Phone</th>
               <th>Service Id</th>
            </tr>
          </thead>
          <tbody>
              <?php  $i=1;foreach($result as $key =>$gethandyman) { if(!empty($gethandyman[0])){?>
            <tr >
                <td><?php echo $i;?></td>
               <td><img src="https://d28hgh2xpunff2.cloudfront.net/vendor-320x252/<?php echo $gethandyman[0]['profile_photo'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
               <td ><?php echo $gethandyman[0]['name']?></td>
               <td><?php echo $gethandyman[0]['phone']?></td>
               <td><?php echo $gethandyman[0]['service_id'];?></td>
           </tr>
           <?php $i= $i+1; }} ?>
          
         </tbody>
       </table>
          </div>
     </div>
  
 </div>

