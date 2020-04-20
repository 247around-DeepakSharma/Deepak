<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div class="panel panel-default">
               <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-money fa-fw"></i> User Profile</h3>
               </div>
               <div class="panel-body">
                  <div class="table-responsive">
                     <table class="table table-bordered table-hover table-striped" id="userTable">
                        <thead>
                           <tr>
                              <th>No #</th>
                              <th>Name</th>
                              <th>Phone</th>
                              <th>Home Address</th>
                              <th>Office Address</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php 
                              foreach ($getalluserInfo as $getuserdetails) { 
                               ?>
                           <tr>
                              <td><?php echo $getuserdetails['id']; ?></td>
                              <td><?php echo $getuserdetails['name']; ?></td>
                              <td><?php echo $getuserdetails['phone']; ?></td>
                              <td><?php echo $getuserdetails['home_address']; ?></td>
                              <td><?php echo $getuserdetails['office_address']; ?></td>
                              <td><a class="btn btn-small btn-success"  href="<?php echo base_url()?>form/getuserid?id=<?php echo $getuserdetails['id'];?>">Edit</a></td>
                           </tr>
                           <?php 
                              }?>
                        </tbody>
                     </table>
                  </div>
                  <!-- <div class="text-right">
                     <a href="#">View All Transactions <i class="fa fa-arrow-circle-right"></i></a>
                     </div>-->
               </div>
            </div>
         </div>
      </div>
   </div>
</div>