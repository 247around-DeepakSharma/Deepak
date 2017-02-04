<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info" style="margin-top:30px;">
            <div class="panel-heading">
                <h1 class="panel-title"><i class="fa fa-money fa-fw"></i>Only Wall Mount Given By Service Center</h1>
            </div>
            <?php if($this->session->flashdata('msg')){ ?>
                <div class="col-sm-12 alert alert-success" style="margin-top: 10px;">
                    <?php echo $this->session->flashdata('msg');    ?>
		</div>
            <?php } ?>
            <form method="POST" action="<?php echo base_url(); ?>employee/booking/process_update_not_pay_to_sf_booking" class="form-inline">
                <div class="panel-body">
                    <div class="clonedInput" id="clonedInput">
                        <table class="table  table-striped table-bordered">
                            <tr>
                                <th style="width: 40%;">
                                     <div class="form-group">
                                        <label for="booking-id">Booking ID:</label>
                                        <input type="text" class="form-control get_required" id="booking-id_1" name="booking_id[]">
                                      </div>
                                </th>
                               
                                <th class="text-center">
                                    <button class="clone btn btn-sm btn-success" id="add_1">Add New Row</button>
                                </th>
                                <th class="text-center">
                                    <button class="remove btn btn-sm btn-danger" id="delete_1">Delete Row</button>
                                </th>
                            </tr>
                        </table>
                    </div>
                    <div class="cloned"></div>
                    <div class="col-md-12">
                        <center><img id="loader_gif" src="" style="display: none;width:40px;"></center>
                        <center><input type="submit" value="Wall Mount Not Given" onclick="return check_validation()" class="btn btn-md btn-primary" /></center>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = $(".clonedInput").length +1;
    
    function clone(){
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
               }
               $('#booking-id_' + cloneIndex).val('');
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
                    alert("Please Enter " + this.id.split('_')[0]);
                    break;
                case typeof this === "undefined":
                    validation = 0;
                    alert("Please Enter " + this.id.split('_')[0]);
                    break;
                case "":
                    validation = 0;
                    alert("Please Enter " + this.id.split('_')[0]);
                    break;
                case false:
                    validation = 0;
                    alert("Please Enter " + this.id.split('_')[0]);
            }
        });
        
        
        if(validation ===0){
            return false;
            
        } else if(validation === 1){
            return true;
        }
        
        
    }
</script>