<?php if(isset($source)){?>
<div id="page-wrapper">
   <script src="<?php echo base_url()?>js/report.js"></script>
   <div class="container-fluid" >
      <div class="panel panel-info" style="margin-top:20px;">
         <div class="panel-heading">
            <h2>Users</h2>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="col-md-12">
                  <ul class="vendor_performance ">
                     <li class="col-md-2">
                        <select onchange="gettransactionalusercount()" class="form-control"  id="type" name="mon_user">
                           <option  disabled>Select Any One</option>
                           <option value="Unique User" selected >Unique User</option>
                           <option value="All Month" >All Month</option>
                           <option value="All Year" >All Year</option>
                           <option value="Quater" >Quarter</option>
                           <option value="Week" >Week</option>
                        </select>
                     </li>
                     <li class="col-md-2" style="border: 1px solid #bbb;">
                        <select onchange="gettransactionalusercount()" class="form-control"  id="user_source" name="user_source" >
                           <option  disabled>Select Source</option>
                           <option value="" selected>All Source</option>
                           <?php 
                              foreach ($source as $key => $partner) { ?>
                           <option value="<?php echo $partner['code'] ?>"> <?php echo $partner['source']; ?></option>
                           <?php }
                              ?>
                        </select>
                     </li>
                     <li class="col-md-2" style="border: 1px solid #bbb;" >
                        <p id="total_user"></p>
                     </li>
                  </ul>
               </div>
               <div class="col-md-12 col-md-offset-3"><img src="" id="loader_gif"></div>
               <div class="col-md-12" style="margin-top:20px;">
                  <table class="table paginated  table-striped table-bordered" id="t_u_count">
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
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
</style>
<script type="text/javascript">
   $('#type').select2();
      $('#user_source').select2();
          //user_source
      $(function() {
   
     gettransactionalusercount();
   
   });
   
</script>
<?php } elseif (isset($user)) { ?>
<table class="table table-striped table-bordered" >
   <tr>
      <th>Total User</th>
      <th>Month/Year</th>
   </tr>
   <tbody>
      <?php $total_user = 0;foreach ($user as $value) {  ?>
      <tr>
         <td><?php echo $value['total_user'];  $total_user += $value['total_user']; ?></td>
         <td><?php if(isset($value['month'])) { if(isset($value['year'])){ echo $value['month']."  ".$value['year'];} else {echo $value['month'];} }  ?></td>
      </tr>
      <?php  } ?>
      </input>
   </tbody>
</table>
<input type="hidden" value="<?php  echo $total_user; ?>" id ="total_booking_user"></input>
<?php }?>