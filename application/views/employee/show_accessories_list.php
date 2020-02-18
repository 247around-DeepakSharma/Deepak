<div id="page-wrapper" >
   <div class="container1" >
      <div class="panel panel-info" >
         <div class="panel-heading" style="font-size:130%;">
            <b>
               <center>ACCESSORIES LIST</center>
            </b>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="col-md-12">
                  <table id="annual_charges_report" class="table  table-striped table-bordered">
                     <thead>
                        <tr>
                           <th class="text-center">Sn.</th>
                           <th class="text-center">Appliance</th>
                           <th class="text-center">Product Name</th>
                           <th class="text-center">Description</th>
                           <th class="text-center">Basic Charge</th>
                           <th class="text-center">HSN Code</th>
                           <!--<th class="text-center">Last cash Invoice for Installation Service</th>-->
                           <th class="text-center">Tax Rate</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php  
                           $StartRowCount=0;
                           $totalAmount=0;
                           $TotalCashInoviceInst=0;
                           
                               foreach ($product_list as $row)  
                               {  //print_r($row);
                               ?>
                        <tr>
                           <td class="text-center"><?php echo ++$StartRowCount; ?></td>
                           <td class="text-center"><?php echo $row['services']; ?></td>
                           <td class="text-center"><?php echo $row['product_name']; ?></td>
                           <td class="text-center"><?php echo $row['description']; ?></td>
                           <td class="text-center"><?php echo $row['basic_charge']; ?></td>
                           <td class="text-center"><?php echo $row['text_hsn_code']; ?></td>
                           <td class="text-center"><?php echo $row['tax_rate']; ?></td>
                        </tr>
                        <?php
                           }
                           ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   $(document).ready(function() {
   $('#annual_charges_report').DataTable({
   "processing": true, 
   "serverSide": false,  
   "dom": 'lBfrtip',
   "buttons": [
   {
       extend: 'excel',
       text: '<span class="fa fa-file-excel-o"></span>  Export',
       title: 'accessories_list_<?php echo date('Ymd-His'); ?>',
       footer: true
   }  
   ],            
   "order": [],            
   "ordering": true,     
   "deferRender": true,
   //"searching": false,
   //"paging":false
   "pageLength": 10,
    "language": {                
       "emptyTable":     "No Data Found",
       "searchPlaceholder": "Search by any column."
   },
   });
   });
</script>
<style>
   #annual_charges_report_filter label
   {
   float: right !important;
   }
   #annual_charges_report_filter .input-sm
   {
   width: 272px !important;    
   }
   .dataTables_length label
   {
   float:left;
   }
   .dt-buttons
   {
   float:left;
   margin-left:85px;
   }
   .paging_simple_numbers
   {
   width: 45%;
   float: right;
   text-align: right;
   }
   .dataTables_info
   {
   width: 45%;
   float: left;
   padding-top: 30px;
   }
</style>