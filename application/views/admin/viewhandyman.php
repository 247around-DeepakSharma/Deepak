<div id="page-wrapper">
   <div class="container-fluid">
      <?php if(isset($error) && $error !==0) {
         echo '<div class="alert alert-danger alert-dismissible" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
             <strong>' . $error . '</strong>
         </div>';
         }
         ?>
      <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               Handyman <small></small>
            </h1>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   Handyman
               </li>
            </ol>
         </div>
      </div>
      <?php if(isset($gethandymanInfo)) {?>
      <div class="row">
         <div class="col-lg-12">
          
              
                  <div class="table-responsive">
                     <table class="table table-bordered table-hover table-striped" id="userTable">
                        <thead>
                           <tr>
                              <th>No #</th>
                              <th>Profle Photo</th>
                              <th>Name</th>
                              <th>Phone</th>
                              <th>Service</th>
                              <th>Address</th>
                              <th>Experience</th>
                              <th>Age</th>
                              <th>Paid</th>
                             <!-- <th>Passport</th>
                              <th>Identity</th>
                              <th>Married </th>
                              <th>Works on</th>-->
                              <th>Service on Call</th>
                              <th style="text-align: center;">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php 
                              foreach ($gethandymanInfo as $gethandyman) { 
                            
                                  ?>
                           <tr>
                              <td><?php echo $gethandyman['id']; ?></td>
                              <td><img src="<?php echo base_url()?>uploads/<?php echo $gethandyman['profile_photo'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
                              <td><?php echo $gethandyman['name']; ?></td>
                              <td><?php echo $gethandyman['phone']; ?></td>
                              <td><?php $service = json_decode($gethandyman['service'],true); 
                              foreach ($service as $services) {
                                
                                       echo "<span>".$services."   "."</span>";
                                     
                                   }
                                   ?> 
                              </td>
                              <td><?php echo $gethandyman['address']; ?></td>
                              <td><?php echo $gethandyman['experience']; ?></td>
                              <td><?php echo $gethandyman['age']; ?></td>
                              <td><?php echo $gethandyman['is_paid']; ?></td>
                             <!-- <td><?php  

                                if($gethandyman['passport'] == "Yes") {
                                 echo $gethandyman['passport'] ;
                                 } else {
                                 echo "NO";
                                 }?>
                              </td>
                              <td><?php if($gethandyman['identity'] == "Yes") {
                                 echo $gethandyman['identity'] ;
                                 } else {
                                 echo "NO";
                                 }
                                 
                                 ?></td>
                              <td><?php if($gethandyman['marital_status'] == "Married"){
                                 echo $gethandyman['marital_status'];
                                 } else {
                                  echo "Single";
                                 
                                 }?></td>
                              <td><?php if($gethandyman['works_on_weekends'] == "weekends"){
                                 echo $gethandyman['works_on_weekends'];
                                 } ?><br/>
                                 <?php if($gethandyman['work_on_weekdays'] == "weekdays") {
                                    echo $gethandyman['work_on_weekdays'];
                                    } ?>
                              </td>-->
                              <td ><?php  if($gethandyman['service_on_call'] == "Yes") {
                                 echo $gethandyman['service_on_call'] ;
                                 } else {
                                 echo "NO";
                                 }?></td>
                              <td style="width:20%;padding:2px;vertical-align: middle;">
                                 <p>
                                    <a class="btn btn-small btn-success"  href="<?php echo base_url();?>handyman/update/<?php echo $gethandyman['id'];?>?tab=home">Edit</a>
                                    <a class="btn btn-small btn-danger"  href="<?php echo base_url();?>handyman/deletehandyman/<?php echo $gethandyman['id'];?>">Delete</a>
                                 </p>
                              </td>
                           </tr>
                           <?php  
                              } ?>
                        </tbody>
                     </table>
                 
           </div>
         </div>
      </div>
      <?php } ?>
   </div>
</div>
</div>
<script>
   $('#userTable').dataTable();
</script>
<script>
$(document).ready(function(){
   
        $("#userTable_filter").addClass("pull-right");
   
});
</script>
<style>

.pull-right {
   float: right;
  
}
</style>
</html>
</body>