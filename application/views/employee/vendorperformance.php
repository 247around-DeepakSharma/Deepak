<?php if(isset($vendor)) {?>
<script type="text/javascript">
   $(function() {
   
   getVendorPerformance();
   
   });
</script>
<div id="page-wrapper" >
   <script src="<?php echo base_url()?>js/report.js"></script>
   <div class="container-fluid" >
      <div class="panel panel-info" style="margin-top:20px;">
         <div class="panel-heading">
            <h2>Vendor Performance</h2>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="col-md-12">
                  <ul class="vendor_performance ">
                     <li class="col-md-2">
                        <select onchange="getVendorPerformance()" class="form-control"  id="vendor" >
                           <option value="">All Vendor</option>
                           <?php 
                              foreach ($vendor as $key => $value) { ?>
                           <option value="<?php echo $value['id'] ?>"> <?php echo $value['name']; ?></option>
                           <?php }
                              ?>
                        </select>
                     </li>
                     <li class="col-md-2">
                        <select onchange="getVendorPerformance()" class="form-control"  id="city" name="city" >
                           <option  disabled>Select city</option>
                           <option value="" selected>All City</option>
                           <?php 
                              foreach ($city as $key => $City) { ?>
                           <option value="<?php echo $City['City'] ?>"> <?php echo $City['City']; ?></option>
                           <?php }
                              ?>
                        </select>
                     </li>
                     <li class="col-md-2" style="border: 1px solid #bbb;">
                        <select  onchange="getVendorPerformance()" class="form-control"  id="service" name="service" >
                           <option  disabled>Select Appliances</option>
                           <option value="" selected>All Appliances</option>
                           <?php 
                              foreach ($services as $key => $values) { ?>
                           <option value="<?php echo $values['id'] ?>"> <?php echo $values['services']; ?></option>
                           <?php }
                              ?>
                        </select>
                     </li>
                     <li class="col-md-2" style="border: 1px solid #bbb;" >
                        <select  onchange="getVendorPerformance()" class="form-control"  id="source" name="source" >
                           <option  disabled>Select Source</option>
                           <option  value = "" selected>All Source</option>
                           <?php 
                              foreach ($source as $key => $partner) { ?>
                           <option value="<?php echo $partner['code'] ?>"> <?php echo $partner['source']; ?></option>
                           <?php }
                              ?>
                        </select>
                     </li>
                     <li class="col-md-2" style="border: 1px solid #bbb;" >
                        <select  onchange="getVendorPerformance()" class="form-control"  id="period" name="period" >
                           <option  disabled>Select Period</option>
                           <option  value = "" selected>All group By Date</option>
                           <option value="All Year" >Year</option>
                           <option value="All Month" >Month</option>
                           <option >Quater</option>
                           <option value="Week" >Week</option>
                        </select>
                     </li>
                     <li class="col-md-2" style="border: 1px solid #bbb;" >
                        <select  onchange="getVendorPerformance()" class="form-control"  id="sort" name="sort" >
                           <option  value = "DESC" selected>DESC</option>
                           <option  value = "ASC" selected>ASC</option>
                        </select>
                     </li>
                  </ul>
               </div>
               <div class="col-md-12 col-md-offset-3"><img src="" id="loader_gif"></div>
               <div class="col-md-12" style="margin-top:20px;">
                  <table class="table paginated  table-striped table-bordered" id="performance">
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $('#vendor').select2();
   $('#city').select2();
   $('#service').select2();
   $('#source').select2();
   $('#sort').select2();
   $('#period').select2();
</script>
<style type="text/css">
   div.pager {
   text-align: center;
   margin: 1em 0;
   }
   div.pager span {
   display: inline-block;
   width: 1.8em;
   height: 1.8em;
   line-height: 1.8;
   text-align: center;
   cursor: pointer;
   background: #bce8f1;
   color: #fff;
   margin-right: 0.5em;
   }
   div.pager span.active {
   background: #c00;
   }
   thead {color:green;}
   table,th,td { border:1px solid black;}
   th:hover{
   cursor:pointer;
   background:#AAA;
   }
</style>
<?php } ?>
<?php  if(isset($data)){ ?>
<script type="text/javascript">
   function sortTable(f,n){
   var rows = $('#mytable tbody  tr').get();
   
   rows.sort(function(a, b) {
   
     var A = getVal(a);
     var B = getVal(b);
   
     if(A < B) {
       return -1*f;
     }
     if(A > B) {
       return 1*f;
     }
     return 0;
   });
   
   function getVal(elm){
     var v = $(elm).children('td').eq(n).text().toUpperCase();
     if($.isNumeric(v)){
       v = parseInt(v,10);
     }
     return v;
   }
   
   $.each(rows, function(index, row) {
     $('#mytable').children('tbody').append(row);
   });
   }
   var f_sl = 1;
   var f_nm = 1;
   $("#complete").click(function(){
     f_sl *= -1;
     var n = $(this).prevAll().length;
     sortTable(f_sl,n);
   });
   $("#cancelled").click(function(){
     f_sl *= -1;
     var n = $(this).prevAll().length;
     sortTable(f_sl,n);
   });
   $("#percentage").click(function(){
     f_sl *= -1;
     var n = $(this).prevAll().length;
     sortTable(f_sl,n);
   });
   $("#nm").click(function(){
     f_nm *= -1;
     var n = $(this).prevAll().length;
     sortTable(f_nm,n);
   });
</script>
<table id="mytable"  class="table  table-striped table-bordered">
   <thead>
      <tr>
         <th >No.</th>
         <th id="nm">Vendor</th>
         <th>City</th>
         <th>Appliances</th>
         <th>Source</th>
         <th id="complete">Completed Booking</th>
         <th id="cancelled">Cancelled Booking</th>
         <th id="percentage">% Completed Booking</th>
         <th>SC Avg Amount</th>
         <th>All SC Avg Amount</th>
         <th>Closed Date</th>
      </tr>
   </thead>
   <tbody>
      <?php $i=1; $completed = 0; $cancelled = 0; $per = 0 ; $amount_paid =0; $avg_amount_paid = 0; foreach ($data as $key => $variable) {
         foreach ($variable as $keys => $value) { ?>
      <tr>
         <td><?php  echo $i; ?></td>
         <td><?php echo $variable[0]['Vendor_Name']; ?></td>
         <td><?php if(isset($variable[0]['City'])) { echo $variable[0]['City']; }?></td>
         <td><?php if(isset($variable[0]['Appliance'])) { echo $variable[0]['Appliance'];} ?></td>
         <td><?php if(isset($variable[0]['source'])){ echo $value['source']; }?></td>
         <td><?php echo $value['completed_booking']; $completed += $value['completed_booking']?></td>
         <td><?php echo $value['cancelled_booking']; $cancelled +=  $value['cancelled_booking']; ?></td>
         <td><?php echo $value['percentage']; $per += $value['percentage'];?></td>
         <td><?php if(isset($value['amount_paid'])){ echo  sprintf ("%.2f", $value['amount_paid']); $amount_paid += $value['amount_paid']; }?></td>
         <td><?php if(isset($value['avg_amount_paid'])){ echo  sprintf ("%.2f",$value['avg_amount_paid']); $avg_amount_paid += $value['avg_amount_paid']; }?></td>
         <td><?php if(isset($value['month'])) { if(isset($value['year'])){ echo $value['month']."  ".$value['year'];} else {echo $value['month'];} }  ?></td>
      </tr>
      <?php $i++; } ?>
      <?php }  ?>
      <tr>
         <td>Total</td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td><?php echo $completed; ?></td>
         <td><?php echo $cancelled; ?></td>
         <td><?php if($completed + $cancelled) { echo sprintf ("%.2f",(($completed * 100)/ ($completed + $cancelled)));} else { echo "0"; } ?></td>
         <td><?php echo sprintf ("%.2f",$amount_paid/5) ;?></td>
         <td><?php echo sprintf ("%.2f", $avg_amount_paid/5);?></td>
         <td></td>
      </tr>
   </tbody>
</table>
<?php } ?>