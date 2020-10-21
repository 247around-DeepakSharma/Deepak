<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<div id="page-wrapper" >
    <div class="container-fluid" >
        <?php
        if ($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:20;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
        }
        ?>
        <span id="add_details_status" style="margin-left: 550px; font-size: 15px;font-weight: bold;"></span>
        <div class="panel panel-info" style="margin-top:20px;">            
            <div class="panel-heading text-center">
                <h4>Add New HSN Code</h4>
            </div>
            <form name="add_hsn_code" id="add_hsn_code" class="form-horizontal">
                <div class="panel-body">                   
                    <div class="row">                        
                        <div class="col-md-12" >
                            <table class="table priceList table-striped table-bordered">                       
                                <thead>
                                    <tr> 
                                        <th style="border-color: #bce8f1;" class="text-center">
                                            <p style="float: left;margin-left: 4px;">HSN Code</p>
                                             <input type="text" class="form-control" name="hsn_code" id="hsn_code" placeholder="Enter HSN Code"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
                                            <br>
                                        </th>
                                        <th style="border-color: #bce8f1;" class="text-center">
                                            <p style="float: left;margin-left: 2px;">GST Rate</p>
                                            <input type="text" class="form-control" name="gst_rate" id="gst_rate" placeholder="Enter GST Rate">
                                        </th>                                      
                                        <th style="border-color: #bce8f1;" class="text-center">
                                            <div style="text-align: center;">
                                                <input type="hidden" name="agent_id" value="<?php echo $this->session->userdata('id'); ?>" />
                                                <input  id="form_submit" type= "submit"  class="btn btn-primary btn-lg" style="padding: 3px 30px;"  value ="Submit" >
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                            </table>                            
                        </div>                        
                        <div class="col-md-12" >
                            <h5><strong>HSN Code List</strong></h5>
                            <hr>
                            <table class="table priceList table-striped table-bordered">                       
                                <thead>
                                    <tr>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">HSN Code</th>                                        
                                        <th class="text-center">GST Rate</th>
                                        <th class="text-center">Created Date</th>                                          
                                    </tr>
                                </thead>
                                <?php foreach ($hsn_code_list as $key => $val) { ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $val['id']; ?></td>
                                        <td style="text-align: center;">                                            
                                            <span class="hsncode_details_text" id="<?php echo $val['id']."|hsn_code";?>"><?php echo $val['hsn_code']; ?></span> <span class="hsn_code_details_edit"><i class="fa fa-pencil fa-lg"></i></span>
                                        </td>
                                        <td style="text-align: center;">                                            
                                            <span class="hsncode_details_text" id="<?php echo $val['id']."|gst_rate";?>"><?php echo $val['gst_rate']; ?></span> <span class="hsn_code_details_edit"><i class="fa fa-pencil fa-lg"></i></span>
                                        </td>
                                        <td style="text-align: center;"><?php echo date("jS M, Y", strtotime($val['create_date'])); ?></td>
                                    </tr>
                                <?php } ?>
                            </table>                            
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $("#form_submit").click(function(){
        var textVal = $("#gst_rate").val();
        var regexp = /^\d+(\.\d{1,2})?$/;
        if(!regexp.test(textVal)){
            $("#gst_rate").val('');
            alert('Please enter valid GST.');
            return false;
        }
    });
    
    $(function () {
        $("#add_hsn_code").submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url() ?>employee/invoice/post_add_new_hsncode',
                type: 'post',
                dataType: 'json',
                data: $("#add_hsn_code").serialize(),
                success: function (data) {
                    if(data['status']=='success'){
                       $("#add_details_status").html("Data inserted successfully").css({'color':'green'});
                       location.reload();
                    }else{
                       $("#add_details_status").html("Something is wrong please try again.").css({'color':'red'}); 
                       $('#add_hsn_code').trigger("reset");
                    }
                }
            });

        });
    });
    
    $(".hsn_code_details_edit").click(function() {
        if ($(this).siblings(".hsncode_details_text").is(":hidden")) {
            var prethis = $(this);
            var text_id = $(this).siblings(".hsncode_details_text").attr('id');       
            var split = text_id.split('|');
            var line_item_id = split[0];
            var column = split[1];
            var data_value = $(this).siblings("input").val();
            $(this).siblings(".hsncode_details_text").text($(this).siblings("input").val());

            $.ajax({
                url: "<?php echo base_url() ?>employee/invoice/update_hsn_code_details_column",
                type: "POST",
                beforeSend: function(){                
                     prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
                 },
                data: { data: data_value, id: line_item_id, column:column},
                success: function (data) {
                    if(data === "Success"){                    
                        prethis.siblings("input").remove();
                        prethis.siblings(".hsncode_details_text").show();
                        prethis.html('<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>');                 
                    } else {
                        alert("There is a problem to update");
                        alert(data);
                    }                
                }
            });
        } else {
            var text = $(this).siblings(".hsncode_details_text").text();
            $(this).before("<input type=\"text\" class=\"form-control\" value=\"" + text + "\">");
            $(this).html('<i class="fa fa-check fa-lg" aria-hidden="true"></i>');
            $(this).siblings(".hsncode_details_text").hide();
        }
    });
    
</script>
