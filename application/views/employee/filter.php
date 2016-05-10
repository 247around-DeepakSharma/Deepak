<?php if($this->session->userdata('add handyman')){?>
<div id="page-wrapper">
   <div class="container-fluid">
      
      <legend style="font-size: 30px;">Filter By:- <small><a href="<?php echo base_url()?>employee/filter/viewhandyman" class="pull-right">clickToViewAllHandyman</a></small></legend>
      <form class="form-horizontal" action="<?php echo base_url()?>employee/filter/viewhandyman" method="POST" >
         <div class="form-group">
            <div class="col-md-10">
               <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Filter" style="width:33%"></center>
            </div>
         </div>
         <ul style="font-size:16px;">
            <li>
               <a href="javascript:;" data-toggle="collapse" data-target="#service"><i class="fa fa-fw fa-arrows-v"></i> Service <i class="fa fa-fw fa-caret-down"></i></a>
               <ul id="service" class="collapse" style="font-size:16px;">
                  <?php foreach ($service as $key => $value) {?>
                  <li >
                     <input type="checkbox" name="service[]" value="<?php echo $value['id']?>"> <?php echo $value['services'] ;?>
                  </li>
                  <?php  }?>
               </ul>
            </li>
          <!-- <li>
               <a href="javascript:;" data-toggle="collapse" data-target="#handyman"><i class="fa fa-fw fa-arrows-v"></i> Handyman name <i class="fa fa-fw fa-caret-down"></i></a>
               <ul id="handyman" class="collapse" style="font-size:16px;">
                  <?php foreach ($handyman_name as $key => $value) {?>
                  <li >
                     <input type="checkbox" name ="handyman_name[]" value="<?php echo $value['name']?>"> <?php echo $value['name'] ;?>
                  </li>
                  <?php  }?>
               </ul>
            </li> -->
            <li>
               <a href="javascript:;" data-toggle="collapse" data-target="#experirnce"><i class="fa fa-fw fa-arrows-v"></i> Experience <i class="fa fa-fw fa-caret-down"></i></a>
               <ul id="experirnce" class="collapse" style="font-size:16px;">
                  <li >
                     <input type="checkbox" name = "experience[]" value="0-1"> 0-1
                  </li>
                  <li>
                     <input type="checkbox" name = "experience[]" value="2-5"> 2-5
                  </li>
                  <li>
                     <input type="checkbox" name = "experience[]" value="6-10"> 6-10
                  </li>
                  <li>
                     <input type="checkbox" name = "experience[]" value="10-12"> 10-12
                  </li>
                  <li>
                     <input type="checkbox" name = "experience[]" value="12-15"> 12-15
                  </li>
                  <li>
                     <input type="checkbox" name = "experience[]" value="20-25"> 20-25
                  </li>
                  <li>
                     <input type="checkbox" name = "experience[]" value=">25"> Greater than 25
                  </li>
               </ul>
            </li>
            <li>
               <a href="javascript:;" data-toggle="collapse" data-target="#rating"><i class="fa fa-fw fa-arrows-v"></i> Rating <i class="fa fa-fw fa-caret-down"></i></a>
               <ul id="rating" class="collapse" style="font-size:16px;">
                  <li >
                     <input type="checkbox" name="Rating_by_Agent[]" value="Good"> Good
                  </li>
                  <li>
                     <input type="checkbox" name="Rating_by_Agent[]"  value="Average"> Average
                  </li>
                  <li>
                     <input type="checkbox" name="Rating_by_Agent[]"  value="Exceptional">  Exceptional
                  </li>
                  <li>
                     <input type="checkbox" name="Rating_by_Agent[]"  value="Bad"> Bad
                  </li>
                  <li>
                     <input type="checkbox" name="Rating_by_Agent[]"  value="Very Bad">  Very Bad
                  </li>
               </ul>
            </li>
            <li>
               <a href="javascript:;" data-toggle="collapse" data-target="#agent"><i class="fa fa-fw fa-arrows-v"></i> Agent<i class="fa fa-fw fa-caret-down"></i></a>
               <ul id="agent" class="collapse" style="font-size:16px;">
                  <?php  foreach ($agent as $key => $value) { if(!empty($value['Agent'])){?>
                  <li >
                     <input type="checkbox" name="Agent[]" value="<?php echo $value['Agent']?>"> <?php echo $value['Agent']?>
                  </li>
                  <?php }}?>
               </ul>
            </li>
            <li>
               <a href="javascript:;" data-toggle="collapse" data-target="#area"><i class="fa fa-fw fa-arrows-v"></i> Area<i class="fa fa-fw fa-caret-down"></i></a>
               <ul id="area" class="collapse" style="font-size:16px;">
                  <?php  foreach ($handyman_name as $key => $value) { if(!empty($value['address'])){?>
                  <li >
                     <input type="checkbox" name = "address[]" value="<?php echo $value['address']?>"> <?php echo $value['address']?>
                  </li>
                  <?php }}?>
               </ul>
            </li>
           <!--<li>
               <a href="javascript:;" data-toggle="collapse" data-target="#vendors_area_of_operation"><i class="fa fa-fw fa-arrows-v"></i> Vendors Area  Of Operation<i class="fa fa-fw fa-caret-down"></i></a>
               <ul id="vendors_area_of_operation" class="collapse" style="font-size:16px;">
                  <li >
                     <input type="checkbox" name="vendors_area_of_operation[]" value="Noida"> Noida
                  </li>
                  <li >
                     <input type="checkbox" name="vendors_area_of_operation[]" value="indrapuram"> Indrapuram
                  </li>
                  <li >
                     <input type="checkbox" name="vendors_area_of_operation[]" value="vaishali">    Vaishali
                  </li>
                  <li >
                     <input type="checkbox" name="vendors_area_of_operation[]" value="Vasundhara">    Vasundhara
                  </li>
               </ul>
            </li> -->
            <li style="color: #337ab7;">
               <input type="checkbox" name="service_on_call" value="Yes"> Service On Call
            </li>
            <li style="color: #337ab7;">
               <input type="checkbox" name="work_on_weekdays" value="Yes"> Work on Weekdays
            </li>
            <li style="color: #337ab7;">
               <input type="checkbox" name="work_on_weekdays" value="Yes"> Work on Weekends
            </li>
         </ul>
         <div class="form-group">
            <div class="col-md-10">
               <center><input type= "submit"  class="btn btn-danger btn-lg" value ="Filter" style="width:33%"></center>
            </div>
         </div>
      </form>
   </div>
</div>
<?php } ?>