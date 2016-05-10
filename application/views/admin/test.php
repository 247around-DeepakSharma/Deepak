<div id="page-wrapper">
<div class="container">
   <div class="row">
     
         <div class="col-md-6" >
            <form class="form-horizontal" action="<?php echo base_url()?>employee/filter/viewhandyman" method="POST" >
               <label for="experience" class="col-md-2" style="margin-top:1%">Filetr By-</label>
               <div class="col-md-4">
                  <!-- <input type="text" class="form-control"  name="experience"   value = "<?php echo set_value('experience'); ?>" placeholder = "Experience"> -->
                  <select id="selectMe" class="js-example-basic-multiple form-control">
                     <option value="option1">Service</option>
                     <option value="option2">Experience</option>
                     <option value="option3">Rating By Agent</option>
                     <option value="option4">Agent</option>
                     <option value="option5">Service On Call</option>
                     <option value="option6">Work on Weekdays</option>
                     <option value="option7">Work on Weekends</option>
                  </select>
               </div>
               <div id="option1" class="group col-md-4">
                  <select  name="service[]" class="js-example-basic-multiple form-control" multiple="multiple">
                     <?php foreach ($service as $key => $value) {?>
                     <option value="<?php echo $value['id'] ;?>"><?php echo $value['services'] ;?></option>
                     <?php }?>
                  </select>
               </div>
               <div id="option2" class="group col-md-4">
                  <select   name="experience[]" class="js-example-basic-multiple form-control" multiple="multiple">
                     <option >0-1</option>
                     <option >2-5</option>
                     <option >6-10</option>
                     <option >10-12</option>
                     <option >12-15</option>
                     <option >12-15</option>
                     <option >>25</option>
                  </select>
               </div>
               <div id="option3" class="group col-md-4">
                <select  name="Rating_by_Agent[]" class="js-example-basic-multiple form-control" multiple="multiple">
                     <option >Good</option>
                     <option >Average</option>
                     <option >Exceptional</option>
                     <option >Bad</option>
                     <option >Very Bad</option>
                    
                  </select>
               </div>
               <div id="option4" class="group col-md-4">
                <select  name="Agent" class="js-example-basic-multiple form-control" multiple="multiple">
                     <?php  foreach ($agent as $key => $value) { ?>
                     <option ><?php echo $value['Agent']?></option>
                     <?php }?>
                  </select>
               </div>
                 <div id="option5" class="group col-md-4">
                <select  name="service_on_call[]" class="js-example-basic-multiple form-control" multiple="multiple">
                     <option value="Yes">Yes</option>
                     <option value="No">No</option>
               
                    
                  </select>
               </div>
                <div id="option6" class="group col-md-4">
                <select  name="work_on_weekdays" class="js-example-basic-multiple form-control" multiple="multiple">
                     <option >Yes</option>
                     <option >No</option>
                  </select>
               </div>
                <div id="option7" class="group col-md-4">
                <select   name="works_on_weekends" class="js-example-basic-multiple form-control" multiple="multiple">
                     <option >Yes</option>
                     <option >No</option>
                  </select>
               </div>

               <input type="submit" class="btn btn-small btn-success" value="Filter">
    
            </form>
         </div>
         
         <script>
            $(document).ready(function () {
              $('.group').hide();
              $('#option1').show();
              $('#selectMe').change(function () {
                $('.group').hide();
                $('#'+$(this).val()).show();
              })
            });
         </script>
      </div>
   </div>
</div>
<script>
   $(".js-example-basic-multiple").select2();
</script>