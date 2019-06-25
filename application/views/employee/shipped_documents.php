<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<style>
/*    *{
        border: solid
    }*/

.vertical_align{
    
    padding-top: 1%;
    height: 100%;
}

.disabledbutton {
    pointer-events: none;
    opacity: 0.4;
}
.select-editable {
     position:relative;
     background-color:white;
     border:solid grey 1px;
     width:120px;
     height:18px;
 }
 .select-editable select {
     position:absolute;
/*     top:0px;
     left:0px;
     font-size:14px;
     border:none;
     width:120px;
     margin:0;*/
 }
 .select-editable input {
     position:absolute;
/*     top:0px;
     left:0px;
     width:100px;*/
/*     padding:1px;
     font-size:12px;*/
     border:none;
 }
 .select-editable select:focus, .select-editable input:focus {
     outline:none;
 }
</style>
<div class="container-fluid">
    <div class="row">
        <form class="panel col-md-12 form-inline" action="<?php echo base_url().'employee/accounting/save_documents/';?>" id="myform" enctype='multipart/form-data' method="post" novalidate>
            <div class="panel-info">
                <div class="clear"></div>
                <div class="panel-heading"style="padding-top:1px;padding-bottom:1px">
                    <?php
                        if(isset($courier_details)){
                            echo "<h3>Update Shipping Documents";                            
                        }
                        else
                            echo "<h3>Add Shipping Documents";
                    ?>
                    
                </div>
            </div>
            <div class="clear"></div>
            <div class="panel-info col-md-12">
                <div class="panel-body">
                    <div class="form-group col-md-6">
                        <input type="hidden" value="<?php if(isset($courier_details)){ echo $courier_details[0]->id; } else{ echo "add";}  ?>" name="add_edit">
                        <label for="entity_type" class="col-md-5 vertical_align" id="label_entity_type">Receiver Type *</label>
                        <select id="entity_type" class="form-control col-md-6" name="entity_type" style="width: 195px;">
                            <option disabled <?php if(!isset($courier_details)) echo "selected"?> value="" >Select</option>
                            <option value="247around" <?php if(isset($courier_details) && $courier_details[0]->receiver_entity_type=='247around') echo "selected"?>>247 Around</option>
                            <option value="vendor" <?php if(isset($courier_details) && $courier_details[0]->receiver_entity_type=='vendor') echo "selected"?>>Vendor</option>
                            <option value="partner" <?php if(isset($courier_details) && $courier_details[0]->receiver_entity_type=='partner') echo "selected"?>>Partner</option>
                        </select>
                    </div>
                    
                    <!--<div class="clear"></div>-->
                    <div class="form-group col-md-6">
                        <label for="doc_type" class="col-md-5 vertical_align" id="label_doc_type">Document Type *</label>
                        <select id="doc_type" name="doc_type" class="form-control col-md-6  <?php if(!isset($courier_details)) echo 'disabledbutton'?>" style="width: 195px;">
                            <option disabled value="" <?php if(!isset($courier_details)) echo "selected"?>>Select</option>
                            <option value="invoice" <?php if(isset($courier_details) && $courier_details[0]->document_type=='invoice') echo "selected"?>>Invoice</option>
                            <option value="contract" <?php if(isset($courier_details) && $courier_details[0]->document_type=='contract') echo "selected"?>>Contract</option>
                        </select>
                    </div>  
                    <div class="clear"></div>
                    
                    <div class="form-group col-md-6 <?php if(isset($courier_details)&& $courier_details[0]->document_type != 'invoice') echo 'hidden'?>" id="div_id">
                        <label for="invoice_id" class="col-md-5 vertical_align" id="label_invoice_id">Invoice ID *</label>
                        <input type="text" class="form-control col-md-6 idclass " id="invoice_id" name="invoice_id" value="<?php if((isset($courier_details) && $courier_details[0]->document_type=='invoice')) echo $courier_details[0]->partner_invoice_id?>" placeholder="Invoice ID"/>
                    </div>

                    <div class="form-group col-md-6  <?php if(!(isset($courier_details) && $courier_details[0]->document_type=='contract' && $courier_details[0]->receiver_entity_type=='partner')) echo 'hidden'?> <?php if( form_error('partner_id') ) { echo 'has-error';} ?>" id="div_partner_id">
                        <label for="partner_id" class="col-md-5" id="label_partner_id">Select Partner</label>
                        <div class="wrapper">
                            <select  class="form-control editableBox idclass" id="partner_id" required="" name="partnerid" style="width:195px;">
                                <option value="" <?php if (!isset($courier_details)) echo "selected";?> disabled> Select Partner</option>
                           <?php
                                    foreach ($partner_id as $partner) {
                                        $selected_partner=$partner['id'];
                                        $selected_partner_name = $partner['public_name'];
                                            echo "<option value='".$partner["id"]."'";
                                            if(isset($courier_details) && $courier_details[0]->receiver_entity_id==$selected_partner){
                                                echo "selected";
                                                }
                                            echo ">".$partner["public_name"]."</option>";  
                                    }
                                    ?>
                            </select>
                            <div class="col-md-5"></div>
                            <div class="col-md-6" style="padding-left:12%">OR</div>
                            <div class="col-md-5"></div>
                            <input type="hidden" value="" name="vendor_partner_id" id="vendor_partner_id">
                            <input class="partnerTextBox form-control idclass" id="partnerTextBox" name="partnerbox" placeholder="Enter partner's name" value="<?php if (isset($courier_details)) echo $courier_details[0]->receiver_entity_name;?>"/>
                        </div>
                        <?php echo form_error('partnerid'); ?>
                    </div>
                    <div class="form-group col-md-6 <?php if(!(isset($courier_details) && $courier_details[0]->document_type=='contract' && $courier_details[0]->receiver_entity_type=='247around')) echo 'hidden'?>" id="div_partner">
                        <label class="col-md-5" id="label_partner">Partner</label>
                        <label class="form-control idclass" id="partner" required="" name="partner" style="width:195px;" value="2">247 Around</label>
                    </div>

                    <div class="form-group col-md-6 <?php if(!(isset($courier_details) && $courier_details[0]->document_type=='contract' && $courier_details[0]->receiver_entity_type=='vendor')) echo 'hidden'?> <?php if( form_error('sf_id') ) { echo 'has-error';} ?>" id="div_sf_id">
                        <label for="sf_id" class="col-md-5" id="label_sf_id">Select Service Center</label>
                        <div>
                            <select class="form-control" id="sf_id" required="" name="sfid" style="width:195px;">
                                <option value="" selected disabled> Select Service Center</option>
                                <?php
                                    foreach ($sf_id as $sf) {
                                            echo "<option value='".$sf["id"]."'";
                                            if(isset($courier_details) && $courier_details[0]->document_type=='contract' && $courier_details[0]->receiver_entity_type=='vendor' && $courier_details[0]->id==$sf["id"])
                                                echo "selected";
                                            echo ">".$sf["name"]."</option>";
                                    }
                                    ?>
                                
                            </select>
                        </div>
                        <?php echo form_error('sfid'); ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="courier" class="col-md-5 vertical_align" id="label_courier">Courier Name *</label>
                        <input type="text" id="courier_name" required class="form-control <?php if(!isset($courier_details)) echo 'disabledbutton'?>" maxlength="256" name="courier_name" placeholder="Courier company name" value="<?php if(isset($courier_details)) echo $courier_details[0]->courier_name?>"/>
                    </div>  
                    <!--image-->
                    <div class="clear"></div>
                    <div class="form-group col-md-6">
                        <label for="track_label" class="col-md-5 vertical_align" id="label_track">Courier File *</label>
                        <input type="file" name="courier" class="form-control col-md-2 <?php if(!isset($courier_details)) echo 'disabledbutton'?>" style="width:195px" id="track_file" required value="<?php if(isset($courier_details)) echo $courier_details[0]->courier_file?>"/>
                        <div class="col-md-1">
                            <?php
                            $src = base_url() . 'images/no_image.png';
                            $image_src = $src;
                            if (isset($courier_details) && !empty($courier_details[0]->courier_file)) {
                                //Path to be changed
                                $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $courier_details[0]->courier_file;
                                $image_src = base_url() . 'images/view_image.png';
                            }
                            ?>
                            <?php if (isset($courier_details) && !empty($courier_details[0]->courier_file)) { ?>
                            <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" class="vertical_align  " style="border:1px solid black;margin-left:-5px;" /></a>
                            <?php } ?>
                        </div>

                        
                    </div>
                    
                    
                    <!--AWB no-->
                    <div class="form-group col-md-6">
                        <label for="awb_no" class="col-md-5 vertical_align" id="label_awb_no">AWB No. *</label>
                        <input type="text" name="awb_no" required class="form-control col-md-4 <?php if(!isset($courier_details)) echo 'disabledbutton'?>" style="width:195px" id="awb_no"  maxlength="256"
                               value="<?php if(isset($courier_details)) echo $courier_details[0]->AWB_no?>"/>
                    </div>
                    
                    <div class="clear"></div>
                    <!--Shipment Date-->
                    <div class="form-group col-md-6">
                        <label for="shipment_date" class="col-md-5 vertical_align" id="label_awb_no">Shipment Date *</label>
                        <input type="date" required name="shipment_date" class="form-control col-md-4 <?php if(!isset($courier_details)) echo 'disabledbutton'?>" style="width:195px" id="shipment_date" 
                            value="<?php if(isset($courier_details)) echo date('Y-m-d', strtotime ($courier_details[0]->shipment_date));?>"/>
                    </div>
                    
                    
                <!--    Contact
                    <div class="form-group col-md-6">
                        <label for="contact" class="col-md-5 vertical_align" id="label_contact">Contact</label>
                        <select  class="form-control hidden <?php //if(!isset($courier_details)) echo 'disabledbutton'?>" id="contact" name="contact" style="width:195px">
                                <option value="" <?php //if (!isset($courier_details)) echo "selected";?> disabled> Select Contact Person</option>
                         </select>
                        <input type="text" id="contact_input" name="contact_input" class="form-control <?php //if(!isset($courier_details)) echo 'disabledbutton'?>"
                               value="<?php //if(isset($courier_details)) {if($courier_details[0]->contact_person_name) echo $courier_details[0]->contact_person_name; else echo $courier_details[0]->contact_person_id;}?>"/>
                </div>  -->
                    
                    <!--Contact-->
                    <!--    <div class="form-group col-md-6">
                        <label for="contact" class="col-md-5 vertical_align" id="label_contact">Email</label>
                        
                        <input type="text" id="contact_input" name="contact_input" class="form-control <?php // if(!isset($courier_details)) echo 'disabledbutton'?>"
                               value="<?php// if(isset($courier_details)) {if($courier_details[0]->contact_person_name) echo $courier_details[0]->contact_person_name; else echo $courier_details[0]->contact_person_id;}?>"/>
                    </div> -->
                    <div class="form-group col-md-6">
                        <label for="contact" class="col-md-5 vertical_align" id="label_contact">Email *</label>
                        
                        <input type="email" id="email_input" name="email_input" class="form-control <?php if(!isset($courier_details)) echo 'disabledbutton'?>"
                               value="<?php if(isset($courier_details)) { if($courier_details[0]->contact_person_name) echo $courier_details[0]->contact_person_name; else echo $courier_details[0]->notification_email; } ?>"/>
                    </div>
                    
                    <div class="clear"></div>
                    <div class="form-group col-md-6">
                        <label for="remarks" class="col-md-5 vertical_align" id="label_awb_no">Remarks</label>
                        <textarea name="remarks" class="form-control col-md-4 <?php if(!isset($courier_details)) echo 'disabledbutton'?>" style="width:195px" id="remarks" maxlength="1024"
                                  /><?php if(isset($courier_details)) echo $courier_details[0]->remarks?></textarea>
                    </div>
                    
                </div>  
            </div>
            <!--Submit-->
            <div class="panel-info">
                <div class="panel-body">
                    <div class="form-group col-md-12"> 
                        <div class="col-md-12" align="center">
                            <input type="hidden" name="id" value=""/>
                            <div class="clear"></div>
                            <button type="submit" class="btn btn-primary" id="submit" onclick="return validate();">
                                <?php
                                if (isset($courier_details)) {
                                    echo "Update";                         
                                } else
                                    echo "Save";
                                ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!--script-1-->
<script type="text/javascript">
$(document).ready(function(){
    $("#doc_type").change(function(){
        var selectedDocument = $("#doc_type option:selected").val();
        if(selectedDocument=="invoice"){
            $('#div_id').removeClass('hidden');
            $('#div_partner_id').addClass('hidden');
            $('#div_sf_id').addClass('hidden');
            $('#div_partner').addClass('hidden');
        }
        else if(selectedDocument=="contract"){
            if($("#entity_type").val() == "partner"){
                $('#div_partner_id').removeClass('hidden');
            }
            else if($("#entity_type").val() == "vendor"){
                $('#div_sf_id').removeClass('hidden');
            }
            else{
                $('#div_partner').removeClass('hidden');
            }
            $('#div_id').addClass('hidden');
        } 
    });
});
</script>




<script type="text/javascript">
    $(document).ready(function(){
    $("#partner_id").change( function() {
        var id = get_id(); //Get the option which was selected
        var entity_type = $("#entity_type option:selected").val();
        $.ajax({
            
            type: "post",
            url: "<?php echo base_url() ?>employee/accounting/get_contact",
            data: {id:id, entity:entity_type},
            dataType: 'json',
            cache: false,
            success: function(response){
                        var len = response.length;
                        $("#contact").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#contact").append("<option value='"+id+"'>"+name+"</option>");
                        }
                        if(len<1){
                            $("#contact_input").removeClass("hidden");
                            $("#contact").addClass("hidden");
                        }
                        $("#contact_input").addClass("hidden");
                        $("#contact").removeClass("hidden");
                        $("#contact_input").val("");
                     }
            });
    });
    });
    
    function get_id(){
       
        var entity_type = $("#entity_type option:selected").val();
        var doc_type= $('#doc_type option:selected').val();
        var id="";
        
                switch(entity_type){
                    case "247around":
                        id = "2";
                        break;
                    case "vendor":
                        id = $('#sf_id').val();
                        break;
                    case "partner":
                        if($('#partner_id').val())
                            id = $('#partner_id').val();
                        else
                            id = $('#partnerTextBox').val();
                        break;
                }
                
        return id;
    }
</script>

<script>
    function validate(){ 
        var track_file;
        var is_file = <?php if(isset($courier_details[0]->courier_file) && !empty($courier_details[0]->courier_file)){ echo '1';}else{echo '0';}?>;
        var entity_type = $("#entity_type option:selected").val();
        var doc_type= $('#doc_type option:selected').val();
        var email = $("#email_input").val();
        var courier_name = $("#courier_name").val();
        var id="";
        switch(doc_type){
            case "invoice":
                id = $('#invoice_id').val();
                break;
            case "contract":
                switch(entity_type){
                    case "247around":
                        id = "2";
                        break;
                    case "vendor":
                        id = $('#sf_id').val();
                        break;
                    case "partner":
                        id = $('#partnerTextBox').val();
                        break;
                }
                break; 
        }
       
        if(is_file == 0){
             track_file= $('#track_file').val();
        }
        else{
           track_file =  is_file;
        }
        var awb_no = $('#awb_no').val();
        var shipment_date = $('#shipment_date').val();
        if(!entity_type || !doc_type || !id || !track_file || !awb_no || !email || !shipment_date || !courier_name){
            alert('Please fill all the fields');
            return false;
        }
    }
</script>

<!--Checking if the ID entered exists in the database or not-->
<script>
$(document).ready(function(){
    $("#invoice_id").on("change", function() {
        var invoice_id = $("#invoice_id").val();
        var entity_type = $("#entity_type").val();
        $.ajax({
            type: 'post',
            url: '<?php echo base_url()?>employee/accounting/check_invoice_id/'+invoice_id+'/'+entity_type,
            data: $('#invoice_id').serialize(),
            cache:false,
            data:{ id:invoice_id},
            success: function (response) {
                        if(response=="false"){
                            alert('Please enter valid invoice id!!');
                            return false;
                        }
                        else{
                                $("#vendor_partner_id").val(response);
                        }
                     },
            error: function() {
                        location.reload();
                        alert('Error occured');
                   }
        });
    });
});
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $("#entity_type").change(function(){
            $(".disabledbutton").removeClass("disabledbutton");
            $('#div_id_for_isset').addClass('hidden');
            var selectedDocument = $("#doc_type option:selected").val();
            if(selectedDocument=="contract"){
                if($("#entity_type option:selected").val() == "partner"){
                    $('#div_partner_id').removeClass('hidden');
                    $('#div_sf_id').addClass('hidden');
                    $('#div_partner').addClass('hidden');
                }
                else if($("#entity_type option:selected").val() == "vendor"){
                    $('#div_sf_id').removeClass('hidden');
                    $('#div_partner_id').addClass('hidden');
                    $('#div_partner').addClass('hidden');
                }
                else{
                    $('#div_sf_id').addClass('hidden');
                    $('#div_partner_id').addClass('hidden');
                    $('#div_partner').removeClass('hidden');
                }
            }
        });
    });
    
   

</script>
<script type="text/javascript">
$(document).ready(function(){
   
    $(".editableBox").change(function(){
        $(".partnerTextBox").val($(".editableBox option:selected").html());
        
    });
    $(".partnerTextBox").change(function(){         
        $(".editableBox").val("");
    });
});
</script>


