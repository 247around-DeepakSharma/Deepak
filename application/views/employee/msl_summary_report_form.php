<script src="<?php echo base_url();?>js/base_url.js"></script>
<style>
   #inventory_stock_table_filter{
   text-align: right;
   }
   .select2-container{
   width: 100%!important;
   }
   .select2-container .select2-selection--single{
   height: 35px;
   }
   .select2-container--default .select2-selection--single .select2-selection__rendered{
   line-height: 33px;
   }
   .select2-container--default .select2-selection--single .select2-selection__arrow{
   height: 31px;
   }
   #total_stock{
   font-size: 14px;
   }
   .pull-right{
   padding: 0 0 0 19px;
   }
</style>
<style>
   .dropbtn {
   background-color: #337ab7;
   color: white;
   padding: 9px;
   font-size: 14px;
   border: none;
   cursor: pointer;
   border-radius: 5px;
   width: 220px;
   }
   .dropdown {
   position: relative;
   display: inline-block;
   }
   .dropdown-content {
   display: none;
   position: absolute;
   background-color: #f9f9f9;
   min-width: 160px;
   box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
   z-index: 1;
   }
   .dropdown-content a {
   color: black;
   padding: 12px 16px;
   text-decoration: none;
   display: block;
   font-size: 14px;
   }
   .dropdown-content a:hover {background-color: #a8b7b863;}
   .dropdown:hover .dropdown-content {
   display: block;
   }
</style>
<div class="right_col" role="main">
<div class="row">
   <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
      <div class="x_panel">
         <div class="x_title">
            <h3>MSL Summary Report <span id="total_stock" style='display:none'></span> 
            </h3>
            <?php if($this->session->flashdata('error')) {
                   echo '<div class="alert alert-danger alert-dismissible" role="alert" id="error_message">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('error') . '</strong>
                   </div>';
                   }
            ?>
            <div class="clearfix"></div>
         </div>
         <form method='post' action='<?php echo base_url(); ?>employee/spare_parts/msl_summary_report/' id='form_submit'>
            <div class="x_content">
               <div class="x_content_header">
                  <section class="fetch_inventory_data">
                     <div class="row">
                        <div class="form-inline">
                           <div class="form-group col-md-3">
                              <select class="form-control" id="partner_id" name="partner_id">
                                 <option value="" disabled="">Select Partner</option>
                              </select>
                           </div>
                           <div class="form-group col-md-3">
                              <select class="form-control" id="wh_id" name="wh_id">
                                 <option value="" disabled="">Select Warehouse</option>
                              </select>
                           </div>
                           <div class="btn btn-success" id="get_inventory_data1" onclick='download_report()'>Download Report</div>
                        </div>
                     </div>
                  </section>
               </div>
         </form>
         <div class="clearfix"></div>
         <hr>                    
         </div>
      </div>
   </div>
</div>
<script>
   $(document).ready(function () {
       $('#wh_id,#to_wh_id').select2({
           placeholder:"Select Warehouse"
       });        
       get_partner();        
      
   });
   
   function get_partner(){
       $.ajax({
           type:'POST',
           url:'<?php echo base_url();?>employee/partner/get_partner_list',
           data:{is_wh:true},
           success:function(response){
               $('#partner_id').html(response);
               var option_length = $('#partner_id').children('option').length;
               if(option_length == 2){
                $("#partner_id").change();   
               }
               $('#partner_id').select2();
           }
       });
   }
   
   function get_vendor(partner_id) {
       $.ajax({
           type: 'POST',
           url: '<?php echo base_url(); ?>employee/vendor/get_service_center_with_micro_wh',
           data:{'is_wh' : 1,partner_id:partner_id},
           success: function (response) {
               $('#wh_id').html(response);
           }
       });
   }
   
   function get_wh(partner_id) {
       $.ajax({
           type: 'POST',
           url: '<?php echo base_url(); ?>employee/vendor/get_service_center_details',
           data:{is_wh:1,partner_id:partner_id},
           success: function (response) {               
               $('#to_wh_id').html(response);
           }
       });
   }
   function download_report(){
        var partner_id = $("#partner_id").val();
        var wh_id = $("#wh_id").val();
        
        var submit = 1;
        if(partner_id==null){
            submit = 0;
            alert('Please select Partner.');
        }
        if(wh_id==null && submit==1){
            submit = 0;
            alert('Please select Warehouse.');
        }
        if(submit==1){
            $('#error_message').hide();
            $("#form_submit").submit();
        }
   }
   
   $('#partner_id').on('change',function(){
       var partner_id = $('#partner_id').val();
       if(partner_id){            
           get_vendor(partner_id);
           get_wh(partner_id);
       }else{
           alert('Please Select Partner');
       }
   });  
</script>