<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h2>Engineer Configurations: App Version: <span style="color:#06ba45;font-weight:900;"><?php echo $app_version; ?></span></h2>
        <br>

  
 
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">App Updates</a></li>
    <li class=""><a data-toggle="tab" href="#menu1">WhatsApp Settings</a></li>
    <li class="hide"><a data-toggle="tab" href="#menu2">Menu 2</a></li>
    <li class="hide"><a data-toggle="tab" href="#menu3">Menu 3</a></li>
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
       
      <div class="row">

        <div class="col-md-2">
    
    <div class="alert alert-success alert-dismissible hide" id="alertmsg" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Updated !</strong>
                    </div>

      <div class="form-group">
        <br>
      <label  >Hard/Soft Upgrade:</label>
     <label class="switch">  
  <input id="force_upgrade" type="checkbox"  <?php if($force_upgrade[0]->config_value){ echo 'checked';} ?> value="<?php  echo $force_upgrade[0]->config_value; ?>" data-config_type="<?php  echo $force_upgrade[0]->configuration_type; ?>" >
  <span class="slider round"></span>
</label>
  </div>

        </div>

 
      </div>
    </div>
    <div id="menu1" class="tab-pane fade">
  

            <div class="row">

        <div class="col-md-2">
    
    <div class="alert alert-success alert-dismissible hide" id="alertmsg2" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Updated !</strong>
     </div>

      <div class="form-group">
        <br>
      <label  >Send WhatsApp:</label>
     <label class="switch">  
  <input id="send_whatsapp" type="checkbox"  <?php if($whatsapp[0]->config_value){ echo 'checked';} ?> value="<?php  echo $whatsapp[0]->config_value; ?>" data-config_type="<?php  echo $whatsapp[0]->configuration_type; ?>" >
  <span class="slider round"></span>
</label>
  </div>

        </div>

 
      </div>



    </div>

    
      </div>
    </div>
</div> 

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
<script>

$("#force_upgrade").click(function(){

var checked =0;
var config_type = $(this).attr("data-config_type");
  if ($('#force_upgrade').is(':checked')) {
    checked = 1;
  }else{
    checked = 0;
  }

          $.ajax({
           type: "POST",
           url: "<?php echo base_url();  ?>employee/engineer/update_config",
           data: { config_value: checked , config_type: config_type  },  
           success: function(data)
           {
              var data1  =  JSON.parse(data);
               if(data1.response){
                $("#alertmsg").removeClass("hide");
                 setTimeout(function(){  $("#alertmsg").addClass("hide"); }, 2000);
               } // show response from the php script.
           }
         });

});


$("#send_whatsapp").click(function(){

var checked =0;
var config_type = $(this).attr("data-config_type");
  if ($('#send_whatsapp').is(':checked')) {
    checked = 1;
  }else{
    checked = 0;
  }

          $.ajax({
           type: "POST",
           url: "<?php echo base_url();  ?>employee/engineer/update_config",
           data: { config_value: checked , config_type: config_type  },  
           success: function(data)
           {
              var data1  =  JSON.parse(data);
               if(data1.response){
                $("#alertmsg2").removeClass("hide");
                 setTimeout(function(){  $("#alertmsg2").addClass("hide"); }, 2000);
               } // show response from the php script.
           }
         });

});



</script>