<div id="page-wrapper">
   <div class="container-fluid">
   	<div class="row">
         <div class="col-lg-12">
            <h2 class="page-header">
               List of handyman Verified by employee <small></small>
            </h2>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   Vendors
               </li>
               <li>
                <select id="dynamic_select">
               <option value="<?php echo base_url().'employees/verifylist/0'?>" <?php if($this->uri->segment(3) == 0){ echo 'selected';}?>>Today</option>
               <option value="<?php echo base_url().'employees/verifylist/2'?>" <?php if($this->uri->segment(3) == 2){ echo 'selected';}?>>Last3Days</option>
               <option value="<?php echo base_url().'employees/verifylist/14'?>" <?php if($this->uri->segment(3) == 14 ){ echo 'selected';}?>>Last2weeks</option>
            </select>
               </li>
                <li >
                  Today(<?php if(!empty($one)){print_r(count($one));} else { echo "0";}?>)
               </li>
                <li >
                  Last3days(<?php if(!empty($three)){print_r(count($three));} else{ echo "0";}?>)
               </li>
                <li >
                  last14days(<?php if(!empty($forteen)){print_r(count($forteen));} else{ echo "0";}?>)
               </li>
               
            </ol>
         </div>
      </div>
     <?php if(is_array($result) && sizeof($result)>0){ ?>
      <table  class="table table-bordered table-hover table-striped data" >
      	 <thead>
            <tr>
               <th>No #</th>
               <th>Approve By </th>
               <th>Handyman Image</th>
               <th>Handyman Name</th>
               <th>Approve Date</th>
               
            </tr>
         </thead>
     <?php $i=1; foreach ($result as $key => $value) { ?>     
         <tbody>

            <tr>
            	<td><?php echo $i;?></td>
            	<td><?php echo $value['verify_by'];?></td>
            	<td><img src="https://d28hgh2xpunff2.cloudfront.net/vendor-320x252/<?php echo $value['profile_photo'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
            	<td><?php echo $value['name'];?></td>
            	<td><?php echo $value['verify_date'];?></td>
            </tr>
            <?php $i =$i+1; }?>
        </tbody>
      </table>
      <p align="center" style="padding-top:20px;">
    <?php } else{ echo " handyman Not Verified";}?>
 </p>

 <!--   end of controllers -->
   </div>
</div>
<script>
   $(function(){
   
     $('#dynamic_select').bind('change', function () {
         var url = $(this).val(); 
         if (url) {
             window.location = url; 
         }
         return false;
     });
   });
</script>



