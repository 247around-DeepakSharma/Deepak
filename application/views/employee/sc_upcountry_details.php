
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
       <div class="col-md-12">
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i>Upcountry Details </h2>
            </div>
            <div class="panel-body">
               <div class="table-responsive table-editable" id="table" >
                  <table class="table table-bordered table-hover table-striped">
                     <thead>
                         <tr>
                           <th>S No.</th>
                           <th>State</th>
                           <th>City</th>
                           <th>Pincode</th>
                           <th>Upcountry Rate</th>
  
                         </tr>
                     </thead>
                     <tbody>
                         <?php $sn_no =1; foreach ($data as $value) { ?>
                          <tr>
                              <td><?php echo $sn_no; ?></td>
                              <td><?php echo $value['state']; ?></td>
                              <td><?php echo $value['district']; ?></td>
                              <td><?php echo $value['pincode']; ?></td>
                              <td><?php echo $value['upcountry_rate']." PER KM"; ?></td>
                               
                               </tr>
                             
                        <?php $sn_no++; }?>
                        
                     </tbody>
                  </table>
               </div>
            </div>
           </div>
           
       </div>
   </div>

</div>

</div>
