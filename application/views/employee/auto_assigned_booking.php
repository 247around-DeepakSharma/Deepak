<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
       <div class="col-md-12">
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Auto Assigned Booking </h2>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-hover table-striped">
                     <thead>
                         <tr>
                           <th class="text-center">S No.</th>
                           <th class="text-center">Booking ID</th>
                           <th class="text-center">Service Center</th>
                           <th class="text-center">Assigned Date</th>
                         </tr>
                     </thead>
                     <tbody>
                     <?php foreach ($data as $key => $value) { ?>
                     <tr >
                        <td class="text-center"><?php echo $key +1; ?></td>
                        <td class="text-center"><?php echo $value['booking_id']; ?></td>
                        <td class="text-center"><?php echo $value['name']; ?></td>
                        <td class="text-center"><?php echo date('d-m-Y',  strtotime($value['create_date'])); ?></td>
                     </tr>
                             
                     <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
       </div>
   </div>
</div>
           