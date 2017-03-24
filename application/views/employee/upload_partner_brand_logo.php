<div id="page-wrapper" style="margin-top: 30px;">
    <div class="row">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4>Upload Partner Brand Logo</h4>
            </div>
            <div class="panel-body">
                <?php if($this->session->flashdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('success') . '</strong>
                    </div>';
                    }
                    ?>
                <?php if($this->session->flashdata('failed')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('failed') . '</strong>
                    </div>';
                    }
                    ?>
                <form enctype="multipart/form-data" action="<?php echo base_url(); ?>employee/partner/process_upload_partner_brand_logo" method="post" class="form-inline">
                <div class="panel-body">
                    <div class="clonedInput" id="clonedInput">
                        <table class="table  table-striped table-bordered">
                            <tr>
                                <th style="width: 30%;">
                                     <div class="form-group">
                                        <label>Choose Files</label>
                                        <input type="file" class="form-control" name="partner_brand_logo[]" id ="partner_brand_logo_1" accept="image/*" required="" multiple/>
                                    </div>
                                </th>
                                <th style="width: 30%;">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="partner_name" value="<?php echo $partner['public_name']?>" disabled="">
                                        <input class="form-control" type="hidden" name="partner_id" value="<?php echo $partner['partner_id']?>">
                                        <input class="form-control" type="hidden" name="partner_name" value="<?php echo $partner['public_name']?>">
                                        
                                    </div>
                                </th>
                                <th style="width: 30%;">
                                    <input type="submit" value="Upload" onclick="return check_validation()" class="btn btn-md btn-primary" />
                                </th>
<!--                                <th class="text-center">
                                    <button class="clone btn btn-sm btn-success" id="add_1">Add New Row</button>
                                </th>
                                <th class="text-center">
                                    <button class="remove btn btn-sm btn-danger" id="delete_1">Delete Row</button>
                                </th>-->
                            </tr>
                        </table>
                    </div>
                    <div class="cloned"></div>
<!--                    <div class="col-md-12">
                        <center><img id="loader_gif" src="" style="display: none;width:40px;"></center>
                        <center><input type="submit" value="Upload" onclick="return check_validation()" class="btn btn-md btn-primary" /></center>
                    </div>-->
                </div>
            </form>
            </div>
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
               $('#partner_brand_logo_' + cloneIndex).val('');
               $('#partner_' + cloneIndex).val('');
               $('#partner_name_' + cloneIndex).val('');
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
<?php $this->session->unset_userdata('success'); ?>
<?php $this->session->unset_userdata('failed'); ?>




