<?php if($this->uri->segment(4)){ $sn_no =  $this->uri->segment(4) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
       <div class="col-md-12">
           <div class="panel panel-default">
            <div class="panel-heading">
               <h2 class="panel-title"><i class="fa fa-money fa-fw"></i> Update Upcountry Details </h2>
            </div>
            <div class="panel-body">
               <div class="table-responsive table-editable" id="table" >
                  <table class="table table-bordered table-hover table-striped">
                     <thead>
                         <tr>
                           <th>S No.</th>
                           <th>Booking ID</th>
                           <th>Pincode</th>
  
                         </tr>
                     </thead>
                     <tbody>
                         <?php foreach ($details as $value) { ?>
                          <tr>
                              <td><?php echo $sn_no; ?></td>
                              <td  contenteditable="true"><?php echo $value['booking_id']; ?></a>
			    </td>
                             
                               <td>
                                   
                                   <select name="pincode" onchange="getpincode()" >
                                       <?php foreach ($value['pincode_details'] as $pincode) { ?>
                                       <option value="<?php echo $pincode['district']." - ".$pincode['pincode'];?>"> <?php echo $pincode['district']." - ".$pincode['pincode'];?></option>;
                                       <?php } ?>
                                  
                                  </select>
                               </td>
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

  
  <button id="export-btn" class="btn btn-primary">Export Data</button>
  <p id="export"></p>
</div>

<style>


table {
  word-wrap:break-word;
    table-layout:fixed;
}




</style>

<script>
    var $TABLE = $('#table');
    var $BTN = $('#export-btn');
    var $EXPORT = $('#export');


// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

$BTN.click(function () {
  var $rows = $TABLE.find('tr:not(:hidden)');
  var headers = [];
  var data = [];
  
  // Get the headers (add special header logic here)
  $($rows.shift()).find('th:not(:empty)').each(function () {
    headers.push($(this).text().toLowerCase());
  });
  
  // Turn all existing rows into a loopable array
  $rows.each(function () {
    var $td = $(this).find('td');
    var h = {};
    
    // Use the headers from earlier to name our hash keys
    headers.forEach(function (header, i) {
      h[header] = $td.eq(i).text();   
    });
    
    data.push(h);
  });
  
  // Output the result
  $EXPORT.text(JSON.stringify(data));
});
</script>