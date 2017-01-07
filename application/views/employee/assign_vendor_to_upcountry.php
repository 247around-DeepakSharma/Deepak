<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:30px;">
            <div class="panel-heading">
                <h1 class="panel-title"><i class="fa fa-money fa-fw"></i> Assign SF to UpCountry</h1>
            </div>
            <form method="POST" action="<?php echo base_url(); ?>employee/upcountry/add_sub_sf_upcountry/<?php echo $service_center_id;?>">
                <div class="panel-body">
                    <div class="clonedInput" id="clonedInput">
                        <table class="table  table-striped table-bordered">
                            <tr>
                                <th >
                                    <select class="form-control state get_required" onchange="getDistrict(this)"  id="state_1" name="state[]" required>
                                        <option selected disabled>Select State</option>
                                        <?php 
                                            foreach ($all_state AS $value) { ?>
                                        <option value="<?php echo $value['state'] ?>" <?php  if (strcasecmp($value['state'], $state) == 0) { echo "Selected";} ?>> <?php echo $value['state']; ?></option>
                                        <?php }
                                            ?>
                                    </select>
                                </th>
                                <th>
                                    <select class="form-control city get_required" id="city_1" onchange="getPincode(this)" name="city[]" required>
                                        <option selected disabled >Select City</option>
                                    </select>
                                </th>
                                <th>
                                    <select class="form-control pincode get_required"  id="pincode_1" name="pincode[]" required>
                                        <option selected disabled >Select Pincode</option>
                                    </select>
                                </th>
                                <th>
                                    <select class="form-control charges get_required" id="charges_1" name="charges[]" required>
                                        <option selected disabled >Select Charges</option>
                                        <option value="2">2 Per KM</option>
                                        <option value="4">4 Per KM</option>
                                    </select>
                                </th>
                                <th>
                                    <button class="clone btn btn-sm btn-success" id="add_1">Add New Row</button>
                                </th>
                                <th>
                                    <button class="remove btn btn-sm btn-danger" id="delete_1">Delete Row</button>
                                </th>
                            </tr>
                        </table>
                    </div>
                    <div class="cloned"></div>
                    <div class="col-md-12">
                        <center><img id="loader_gif" src="" style="display: none;width:40px;"></center>
                        <center><input type="submit" value="Add UpCountry Charges" onclick="return check_validation()" class="btn btn-md btn-primary" /></center>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".state").select2();
    $(".city").select2();
    $(".charges").select2();
    $(".pincode").select2({
        tags: true
    });
    
    
    function getDistrict(elem) {
       
       var div_id = $(elem).attr("id");
       var split_id  = div_id.split('_');
       var state = $("#state_"+ split_id[1]).val();
       $("#city_"+ split_id[1]).val("val","");
       $("#city_"+ split_id[1]).select2({
            placeholder: "Select City"
        });
        $("#pincode_"+ split_id[1]).val("val","");
        $("#pincode_"+ split_id[1]).select2({
              placeholder: "Select Pincode"
         });
      
        $('#loader_gif').css("display", "inline-block");
        $('#loader_gif').attr('src',  "<?php echo base_url(); ?>images/loader.gif");
    
     $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/vendor/getDistrict',
       data: {state: state},
       success: function (data) {
        
         $("#city_" + split_id[1]).html(data);
         $('#loader_gif').attr('src',  "");
         $('#loader_gif').css("display", "none");
    
       }
     });
    }
     function getPincode(elem) {
       var div_id = $(elem).attr("id");
       var split_id  = div_id.split('_');
       $('#loader_gif').css("display", "inline-block");
       $('#loader_gif').attr('src',  "<?php echo base_url(); ?>images/loader.gif");
    
       
       var city = $("#city_"+ split_id[1]).val();
       $("#pincode_"+ split_id[1]).val("val","");
       $("#pincode_"+ split_id[1]).select2({
              placeholder: "Select Pincode"
         });
       
      
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>employee/vendor/getPincode',
        data: {district: city},
        success: function (data) {
         
          $("#pincode_"+ split_id[1]).html(data);
          $('#loader_gif').attr('src',  "");
          $('#loader_gif').css("display", "none");
       }
     });
    }
    
    $(function () {
    var state = $("#state_1").val();
    if (state !== "") {
       $('#loader_gif').css("display", "inline-block");
       $('#loader_gif').attr('src',  "<?php echo base_url(); ?>images/loader.gif");
            $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/vendor/getDistrict',
          data: {state: state},
          success: function (data) {
    
            $("#city_1").html(data);
            $('#loader_gif').attr('src',  "");
            $('#loader_gif').css("display", "none");
    
          }
        });
    }
    });
    
</script>
<script type="text/javascript">
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = $(".clonedInput").length +1;
    
    function clone(){
       $('.state').select2("destroy");
       $('.pincode').select2("destroy");
       $('.city').select2("destroy");
       $(".charges").select2("destroy");
       $(this).parents(".clonedInput").clone()
           .appendTo(".cloned")
           .attr("id", "cat" +  cloneIndex)
           .find("*")
           .each(function() {
               
               var id = this.id || "";
               var match = id.match(regex) || [];
               //console.log(match.length);
               if (match.length === 3) {
                   this.id = match[1] + (cloneIndex);
                    $('.state').select2();
                    $('.pincode').select2();
                    $('.city').select2();
                    $(".charges").select2();
               }
               
           })
           .on('click', 'button.clone', clone)
           .on('click', 'button.remove', remove);
    
           
       cloneIndex++;
       return false;
    }
    function remove(){
        var length =  $(".clonedInput").length;
        
        if(length === 1){
            alert("Atleast one row being added");
            return false;
        } else {
            $(this).parents(".clonedInput").remove();
        }
       
       
       return false;
    }
    $("button.clone").on("click", clone);
    
    $("button.remove").on("click", remove);
    
    function check_validation(){
        var validation = 1;
        $('.get_required').each(function (i) {
            var input_field = $("#" + this.id).val();
            
            switch(input_field){
                case null:
                    validation = 0;
                    alert("Please select " + this.id.split('_')[0]);
                    break;
                case typeof this === "undefined":
                    validation = 0;
                    alert("Please select " + this.id.split('_')[0]);
                    break;
                case "":
                    validation = 0;
                    alert("Please select " + this.id.split('_')[0]);
                    break;
                case false:
                    validation = 0;
                    alert("Please select " + this.id.split('_')[0]);
            }
        });
        
        
        if(validation ===0){
            return false;
            
        } else if(validation === 1){
            return true;
        }
        
        
    }
</script>