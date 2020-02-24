<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    .error{margin-top:3px;color:red}
</style>
<div id="page-wrapper">
    <div class="row">
        <div  class = "panel panel-info" style="margin:20px;">
            <div class="panel-heading" style='height:auto;overflow:hidden'>
                <div class='col-md-6'>UPDATE ACCESSORIES</div>
                <div class='col-md-6' style='text-align:right'>
                    <a href='<?php echo base_url(); ?>employee/accessories/show_accessories_list' class='btn btn-primary btn-sm'>Show Accessories List</a></div>
            </div>

            <div class="container1">
                <form method="post" action='<?php echo base_url() ?>employee/accessories/process_submit_add_product' id='form_edit_product' >
                    <br>
                    <input type="hidden" name="hid" value="<?php echo $accessories_detail[0]['id'] ?>">
                    <div class='col-lg-12' id='response_div' style='display:none'>
                        <div class="alert alert-success" id='response_div_s'>
                            <strong>Success!</strong> Product updated successfully.
                        </div>
                        <div class="alert alert-danger" id='response_div_e'>
                            <strong>Error!</strong> <span id='response_div_e_span'></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row1">
                                    <div class="col-sm-4">
                                        <label for="appliance" >Appliance <sup class='mandatory'>*</sup></label>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control" name="appliance_id" id='appliance_id'>
                                            <option value="<?php echo $accessories_detail[0]['appliance']; ?>">Select Appliance Name</option>
                                            <?php
                                            foreach ($services_detail as $row) {
                                                $selected_appliance = '';
                                                if ($row->id == $accessories_detail[0]['service_id']) {
                                                    $selected_appliance = 'selected';
                                                }
                                                ?>
                                                <tr>
                                                <option value="<?php echo $row->id; ?>" <?php echo $selected_appliance; ?>><?php echo $row->services; ?></option>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row1">
                                    <div class="col-sm-4">
                                        <label for="product_name" >Product Name <sup class='mandatory'>*</sup></label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="product_name" class="form-control" placeholder="Enter Product Name" id='product_name' value=<?php echo $accessories_detail[0]['product_name']; ?> >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style='margin:10px;' class='col-sm-12'></div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row1">
                                    <div class="col-sm-4">
                                        <label for="description">Description <sup class='mandatory'>*</sup></label>
                                    </div>
                                    <div class="col-sm-6">
                                        <textarea name="description" class="form-control" placeholder="Enter Description" id='description'><?php echo $accessories_detail[0]['description']; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="row1">
                                    <div class="col-sm-4">
                                        <label for="basic_charge">Basic Charge <sup class='mandatory'>*</sup></label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="number" name="basic_charge" id='basic_charge' class="form-control numeric" placeholder="Enter Basic Charge" value="<?php echo $accessories_detail[0]['basic_charge']; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style='margin:10px;' class='col-sm-12'></div>
                    <div class="row">


                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row1">
                                    <div class="col-sm-4">
                                        <label for="hsn_code">HSN Code <sup class='mandatory'>*</sup></label>
                                    </div>
                                    <div class="col-sm-6">

                                        <select class="form-control"  name="hsn_code" id="hsn_code" >
                                            <option value="<?php echo $accessories_detail[0]['hsn_code']; ?>">Select HSN Code</option>
                                            <?php
                                            foreach ($hsn_code_detail as $row1) {
                                                $selected_hsn = '';
                                                if ($row1['hsn_code'] == $accessories_detail[0]['hsn_code']) {
                                                    $selected_hsn = 'selected';
                                                }
                                                ?>
                                                ?>
                                                <option value="<?php echo $row1['hsn_code'] ?>" <?php echo $selected_hsn ?> ><?php echo $row1['hsn_code'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>   

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row1">
                                    <div class="col-sm-4">
                                        <label for="tax_rate">Tax Rate <sup class='mandatory'>*</sup> <span id='tax_loading_form'></span></label>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" value="<?php echo $accessories_detail[0]['tax_rate']; ?>" id="tax_rate" name="tax_rate" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style='margin:10px;' class='col-sm-12'></div>         
                    </div>
                    <input type='hidden' value="<?php echo $id ?>" name='idtoedit'>
                </form>


                <div class="panel-footer" align='center'>
                    <input type="button" id="submitform" onclick="editproduct()" class="btn btn-primary" value="Update Product">
                </div>
                <div class="form-group  col-sm-12" >
                    <center>
                </div>
                </center>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $('#appliance_id').select2();
        $('#hsn_code').select2();

        $("#hsn_code").on('change', function () {
            var hsncode = $("#hsn_code").val();

            var datastring = "hsncode=" + hsncode;
            if (hsncode == '' || hsncode == null)
            {
                $('#tax_rate').val('');
            } else
            {
                $.ajax({
                    method: 'post',
                    data: datastring,
                    url: "<?php echo base_url() ?>employee/accessories/calculate_tax",
                    beforeSend()
                    {
                        $("#tax_loading_form").html("<img src='<?php echo base_url() ?>images/loading.gif' style='width:15px'>");
                        $("#submitform").prop('disabled', true);
                    },
                    success: function (datastring)
                    {
                        datastring  =   datastring.trim();
                        $("#tax_loading_form").html("<img src='' style='width:15px'>");
                        $('#tax_rate').val(datastring);
                        $("#submitform").prop('disabled', false);
                    }
                });
            }
        });

        function editproduct()
        {
            var appliance_id = $("#appliance_id").val().trim();
            var product_name = $("#product_name").val().trim();
            var description = $("#description").val().trim();
            var basic_charge = $("#basic_charge").val().trim();
            var hsn_code = $("#hsn_code").val().trim();
            var tax_rate = $("#tax_rate").val().trim();

            var submit = true;

            if (appliance_id == '')
            {
                alert('Please select appliance id');
                submit = false;
                return false;
            }
            if (product_name == '')
            {
                alert('Please enter Product name');
                submit = false;
                return false;
            }
            if (description == '')
            {
                alert('Please enter Description');
                submit = false;
                return false;
            }
            if (basic_charge == '')
            {
                alert('Please enter Basic charge');
                submit = false;
                return false;
            } else
            {
                if (!$.isNumeric(basic_charge) || basic_charge < 0)
                {
                    alert('Please enter valid Basic charge');
                    submit = false;
                    return false;
                }
            }
            if (hsn_code == '')
            {
                alert('Please select hsn_code');
                submit = false;
                return false;
            }
            if (tax_rate == '')
            {
                alert('tax rate should not be blank');
                submit = false;
                return false;
            }
            if (submit == true)
            {
                var datastring = $("#form_edit_product").serialize();
            }

            $.ajax({
                method: 'post',
                data: datastring,
                url: "<?php echo base_url() ?>employee/accessories/process_submit_edit_product",
                beforeSend()
                {
                    $("#submitform").val("Updating product...");
                    $("#submitform").prop('disabled', true);

                    $("#response_div").hide();
                    $("#response_div_s").hide();
                    $("#response_div_e").hide();
                },
                success: function (data)
                {
                    var returndata = JSON.parse(data);
                    if (returndata.status != 'error')
                    {
                        $("#response_div_s").show();
                    } else
                    {
                        $("#response_div_e").show();
                        $("#response_div_e_span").html(returndata.msg);
                    }
                    $("#response_div").show();
                    $("#submitform").val("Update product");
                    $("#submitform").prop('disabled', false);
                }
            });




        }

    </script>
</div>
</div>

<style>
    .mandatory
    {
        color: red;
        font-weight: bold;
    }
</style>