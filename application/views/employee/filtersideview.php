<div id="page-wrapper">
   <div class="container" >
      <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">
               Vendors <small></small>
            </h1>
            <ol class="breadcrumb">
               <li >
                  <i class="fa fa-dashboard"></i> Dashboard
               </li>
               <li class="active">
                  <i class="fa fa-fw fa-search"></i>   Vendors
               </li>
            </ol>
         </div>
      </div>
      <div class="row">
         <div class=" col-sm-3">
            <legend >Filter By :-</legend>
         </div>
      </div>
      <div class="row">
         <div class=" col-sm-3" >
            <legend>Service</legend>
            <ul  class="facestyle">
               <?php foreach ($service as $key => $value) {?>
               <li class="face">
                  <input type="checkbox" name="service[]" value="<?php echo $value['id']?>">          <?php echo $value['services'] ;?>
               </li>
               <?php  }?>
            </ul>
         </div>
      </div>
     
      <div class="row">
         <div class=" col-sm-3">
            <legend style="margin-top:2%">Experience</legend>
            <ul   class="facestyle">
               <li class="face">
                  <input type="checkbox" name = "experience[]" value="0-1"> 0-1
               </li>
               <li class="face">
                  <input type="checkbox" name = "experience[]" value="2-5"> 2-5
               </li>
               <li class="face">
                  <input type="checkbox" name = "experience[]" value="6-10"> 6-10
               </li>
               <li class="face">
                  <input type="checkbox" name = "experience[]" value="10-12"> 10-12
               </li>
               <li class="face">
                  <input type="checkbox" name = "experience[]" value="12-15"> 12-15
               </li>
               <li class="face"> 
                  <input type="checkbox" name = "experience[]" value="20-25"> 20-25
               </li>
               <li class="face">
                  <input type="checkbox" name = "experience[]" value=">25"> Greater than 25
               </li>
            </ul>
         </div>
      </div>
      <div class="row">
         <div class=" col-sm-3">
            <legend style="margin-top:2%">Rating By Agent</legend>
            <ul class="facestyle">
               <li class="face" >
                  <input type="checkbox" name="Rating_by_Agent[]" value="Good"> Good
               </li>
               <li class="face">
                  <input type="checkbox" name="Rating_by_Agent[]"  value="Average"> Average
               </li>
               <li class="face">
                  <input type="checkbox" name="Rating_by_Agent[]"  value="Exceptional">  Exceptional
               </li>
               <li class="face">
                  <input type="checkbox" name="Rating_by_Agent[]"  value="Bad"> Bad
               </li>
               <li class="face">
                  <input type="checkbox" name="Rating_by_Agent[]"  value="Very Bad">  Very Bad
               </li>
            </ul>
         </div>
      </div>
      <div class="row">
         <div class=" col-sm-3" >
            <legend>Rating By Agent</legend>
            <ul  class="facestyle" style="height:80%">
               <?php foreach ($agent as $key => $value) {?>
               <li class="face">
                  <input type="checkbox" name="Agent[]" value="<?php echo $value['Agent']?>">    <?php echo $value['Agent'] ;?>
               </li>
               <?php  }?>
            </ul>
         </div>
      </div>
      <div class="row">
         <div class=" col-sm-3">
            <legend style="margin-top:2%">Serive On Call</legend>
            <ul class="facestyle" style="height:80%">
               <li class="face" >
                  <input type="checkbox" name="service_on_call[]" value="Yes"> Yes
               </li>
               <li class="face">
                  <input type="checkbox" name="service_on_call[]"  value="No"> No
               </li>
            </ul>
         </div>
      </div>
      <!--  <script>
         $(document).ready(function () {
           $('.group').hide();
           $('#option1').show();
           $('#selectMe').change(function () {
             $('.group').hide();
             $('#'+$(this).val()).show();
           })
         });
         </script> -->
   </div>
</div>
</div>
<style>
   .face:hover{color: #666;
   text-decoration: none;
   background-color: yellow;
   }
   .facestyle {
   overflow: auto; height: 175px;list-style:none;font-size: 16px;
   }
   .col-md-2{
   width:20%;
   }
   #filter{
   position: relative;
   top: 51px;
   left: 225px;
   width: 225px;
   margin-left: -4px;
   border: none;
   border-radius: 0;
   overflow-y: auto;
   background-color: #fff;
   height: 95%;
   }
</style>