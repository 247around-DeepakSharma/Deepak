<div id="page-wrapper">
   <div class="container-fluid">
   	<div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               List of Advertise
            </h1>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   Advertise
               </li>
            </ol>
         </div>
      </div>

       <table class="table table-bordered table-hover table-striped data"  >
         <thead>
            <tr>
               <th>No #</th>
               <th>Photo</th>
               <th>Advertise</th>
             
            </tr>
         </thead>
         <tbody>
         	 <?php foreach($ads as $key =>$row) {?>
         	<tr>
         		<td  ><?php echo $row['id'] ;?></td>
               <td ><img src="https://d28hgh2xpunff2.cloudfront.net/advertise_photo/<?php echo $row['ads_picture'] ; ?>" class="img-circle  "  style="width:60px; height:60px;"></td>
         	   <td  ><?php echo $row['ads'] ;?></td>
         	</tr>
         	<?php } ?>
         </tbody>
     </table>

      <!--  end of container -->
   </div>
</div>