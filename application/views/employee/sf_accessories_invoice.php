<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    .error{margin-top:3px;color:red}
</style>
<div id="page-wrapper">
    <div class="container col-md-12" >
        <div class="panel panel-info" >
            <div class="panel-heading" ><b><center>SF ACCESSORIES INVOICE</center></b></div>
            <div class="panel-body">
                <div class="row">
                    <div class="container col-md-12" >
                        <?php if($this->session->userdata('success')) {
                            echo '<div class="alert alert-success alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <strong>' . $this->session->userdata('success') . '</strong>
                            </div>';
                            }
                            ?>
                        <?php if($this->session->userdata('error')) {
                            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <strong>' . $this->session->userdata('error') . '</strong>
                            </div>';
                            }
                            ?>
                    </div>
                </div>
                <div class="row">
                    <div class="container col-md-12">
                        <div class="form-group ">
                            <div class="form-group col-md-4 <?php
                            if (form_error('service_center')) {
                                echo 'has-error';
                            }
                            ?>">
                                <label for="service_center" class="col-md-4">Service Center *</label>							
                                <div class="col-md-4">							
                                    <select id="service_center" class="form-control" name ="service_center" required>
                                        <option selected value=''>Select Service Center</option>
                                        <?php foreach ($sf_list as $key => $value) { ?>
                                            <option value ="<?php echo $value['id']; ?>"> <?php echo $value['name']; ?> </option>
                                        <?php } ?>
                                    </select>
                                    <?php echo form_error('service_center'); ?>
                                </div>
                            </div>
                            <button type="button" id="search" class="btn btn-success col-md-1" onclick="showAccessoriesRow()">Search</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="container col-md-12">
                        <form name="sf_accessories_invoice_form" class="form-horizontal" id="sf_accessories_invoice_form" action="<?php echo base_url(); ?>employee/accessories/process_sf_accessories_invoice" method="POST" enctype="multipart/form-data" onsubmit="return process_sf_accessories_invoice_validations()" style="display:none;">
                            <input type="hidden" id="sf_id" name="sf_id" >

                            <div class="clonedInput panel panel-info " id="clonedInput">                      
                                <div class="panel-heading" style=" background-color: #f5f5f5;">
                                    <p style="color: #000;"><b>Add Accessories Details</b></p>
                                    <div class="clone_button_holder" style="float:right;margin-top: -31px;">
                                        <button class="clone btn btn-sm btn-info">Add</button>
                                        <button class="remove btn btn-sm btn-info">Remove</button>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <div class="col-md-6 form-group <?php
                                                    if (form_error('accessories')) {
                                                        echo 'has-error';
                                                    } ?>">
                                                    <label for="accessories" class="col-md-4">Accessories *</label>
                                                    <div class="col-md-6">
                                                        <div class="accessories_holder" id="accessories_holder_1">
                                                            <select class="form-control accessories" name ="accessories[0][id]" id="accessories_1" onchange="hideAccessories(this.id)" required>
                                                                <option selected  value=''>Select Accessories</option>
                                                                <?php foreach ($services_name as $key => $value) { ?>
                                                                    <option value ="<?php echo $value['id']; ?>" > <?php echo $value['product_name']; ?> </option>
                                                                <?php } ?>
                                                            </select>
                                                            <?php echo form_error('accessories'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 form-group <?php
                                                    if (form_error('quantity')) {
                                                        echo 'has-error';
                                                    } ?>">
                                                    <label for="quantity" class="col-md-4">Quantity *</label>							
                                                    <div class="col-md-6">
                                                        <div class="quantity_holder" id="quantity_holder_1">
                                                            <select class="form-control quantity" name ="accessories[0][qty]" id="quantity_1" minimum="1" maximum="1000" required>
                                                                <option selected  value=''>Select Quantity</option>
                                                                <?php foreach ($quantity_list as $key => $value) {
                                                                    ?>
                                                                    <option value ="<?php echo $value; ?>" > <?php echo $value; ?> </option>
                                                                <?php } ?>
                                                            </select>
                                                            <?php echo form_error('quantity'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                    </div>
                                </div>   
                            </div>
                            <div class="cloned"></div>

                            <div class="form-group" style="text-align:center">
                                <input type="submit" class="btn btn-primary" id='submit' name='submit' value="Submit"> <!--  onclick="validate_form()" -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>
<?php if($this->session->userdata('success')){$this->session->unset_userdata('success');} ?>
<script>
    $('#service_center').select2({
        width : '300px',
        placeholder: "Select Service Center",
        allowClear: true,
    });

    $('.accessories').select2({
        width : '350px',
        placeholder: "Select Accessories",
        allowClear: true,
    });
    $('.quantity').select2({
        width : '250px',
        placeholder: "Select Quantity",
        allowClear: true,
    });
    
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = $(".clonedInput").length +1;
    var partArr = new Array();
    
    function clone() {
        if($('div.clonedInput').length < 10) {
            $(this).parents(".clonedInput").clone()
               .appendTo(".cloned")
               .attr("id", "mapping" +  cloneIndex)
               .find("*")
               .each(function() {
                    var id = this.id || "";
                    var match = id.match(regex) || [];
                    if (match.length === 3) {
                        this.id = match[1] + (cloneIndex);
                    }
                })
                .on('click', 'button.clone', clone)
                .on('click', 'button.remove', remove);
            $('#quantity_holder_'+cloneIndex+' .select2').hide();
            $('#accessories_holder_'+cloneIndex+' .select2').hide();
            $('#quantity_'+cloneIndex).select2({
                width : '250px',
                placeholder: "Select Quantity",
                allowClear: true,
            });
            $('#accessories_'+cloneIndex).select2({
                width : '350px',
                placeholder: "Select Accessories",
                allowClear: true,
            });
            $('#accessories_'+cloneIndex).attr('name','accessories['+(cloneIndex-1)+'][id]').attr('onchange','hideAccessories(this.id)');
            $('#quantity_'+cloneIndex).attr('name','accessories['+(cloneIndex-1)+'][qty]');
            $("#select2-quantity_"+cloneIndex+"-container").val("");
            $("#select2-accessories_"+cloneIndex+"-container").val("");
            $('#accessories_'+cloneIndex+' option').removeAttr('disabled');
            for(var key in partArr) {
                $('#accessories_'+cloneIndex+' option[value="'+partArr[key]+'"]').attr('disabled',true);
            }
            cloneIndex++;
       }
       return false;
    }  
    function remove() {
        if($('div.clonedInput').length > 1) {
            $(this).parents(".clonedInput").remove();
            resetPartArr();
        }
        return false;
    }
    $("button.clone").on("click", clone);
    $("button.remove").on("click", remove);
    
    function showAccessoriesRow() {
        var service_center = $("#service_center").val().trim();
        $("#sf_id").val(service_center);
        $("#sf_accessories_invoice_form").show();
//        $("div.cloned").children('div.clonedInput').remove(); // Used to remove all cloned div added using Add button If SF is changed
        if (service_center == '')
        {
            alert('Please Select Service Center.');
            $("#sf_accessories_invoice_form").hide();
        }
    }
    
    function hideAccessories(id) {
        var index = id.split('_')[1];
        var accessory = $('#accessories_'+index).val();
        if($.inArray(accessory,partArr) >= 0) {
           alert("Please select another accessory as this is already selected!!");
           $('#accessories_'+index).removeAttr('selected').val('');
           $('#select2-accessories_'+index+'-container').text('');
        }
        if( partArr === undefined ) {
            partArr = new Array();
        }
        resetPartArr();
    }
    
    function resetPartArr() {
        partArr = new Array();
        $('.accessories').each(function() {
            var id = (this.id).split("_")[1];
            var accessory = $('#accessories_'+id).find(':selected').val();
            if(accessory !== "") {
                partArr.push(accessory);
            }
        });
    }
    
    function process_sf_accessories_invoice_validations (){
        $('#search').attr('disabled',true);
        $('#submit').val('Processing..');
        $('#submit').attr('disabled',true);
        var count=0;
        $('.quantity').each(function() {
            var id = (this.id).split("_")[1];
            var quantity = $("#quantity_"+id).val();
            var accessories = $("#accessories_"+id).val();
            if(quantity && accessories){ 
                ++count;
            }
            else{
                alert('Please add all mandatory fields!!');
                $('#search').removeAttr('disabled');
                $('#submit').val('Submit');
                $('#submit').removeAttr('disabled');
                return false;
            }
        });
        if(count == $('.quantity').length) {
            partArr = new Array();
            return true;
        }
    }
    
</script>
