<style>
    .input-group-addon{
        background-color: #31b0d5;
        border-color: #269abc;
    }
    /* Popup container - can be anything you want */
    #popup {
        position: relative;
        display: inline-block;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* The actual popup */
    #popup .popuptext {
        visibility: hidden;
        width: 160px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px 0;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -80px;
    }

    /* Popup arrow */
    #popup .popuptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    /* Toggle this class - hide and show the popup */
    #popup .show {
        visibility: visible;
        -webkit-animation: fadeIn 1s;
        animation: fadeIn 1s;
    }

    /* Add animation (fade in the popup) */
    @-webkit-keyframes fadeIn {
        from {opacity: 0;} 
        to {opacity: 1;}
    }

    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity:1 ;}
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="challan_details_container" style="border: 1px solid #e6e6e6; margin-top: 20px; padding: 10px;">
            <?php
            if ($this->session->flashdata('success_msg')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('success_msg') . '</strong>
                    </div>';
            }
            if ($this->session->flashdata('error_msg')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('error_msg') . '</strong>
                    </div>';
            }
            ?>
            <section class="serach_challan" style="margin: 10px;">
                <div class="row">
                    <div class="form-inline col-md-4">
                        <div class="form-group">
                            <label for="action">Select Action</label>
                            <select class="form-control" id="action">
                                <option >Select Action</option>
                                <option value="tag">Tag</option>
                                <option value="untag">Untag</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-inline col-md-4">
                        <div class="form-group">
                            <label for="challan_type">Select challan Type</label>
                            <select class="form-control" id="challan_type">
                                <option selected disabled>Select challan type</option>
                                <option value="ST">Service Tax</option>
                                <option value="VAT">VAT</option>
                                <option value="TDS">TDS</option>
                                <option value="ALL">ALL</option>
                            </select>
                        </div>
                    </div>

                </div>
            </section>
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <section class="challan_details_table" style="margin-top: 40px;"></section>
        </div>
    </div>        
</div>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script>
    $('#challan_type').change(function () {
        var action = $('#action').val();
        var type = this.value;
        if(action === 'tag'){
            var url = '<?php echo base_url(); ?>employee/accounting/fetch_challan_details/';
        }
        else {
            var url = '<?php echo base_url(); ?>employee/accounting/fetch_challan_details/untag';
        }
            
        $('#loader').show();
        $.ajax({
            method: 'POST',
            data: {challan_type: type},
            url: url,
            success: function (response) {
                //console.log(response);
                $('#loader').hide();
                $('.challan_details_table').html(response);
            }
        });
    });

    //Adding Validation
    function validate(id) {
        var id = id.split("_")[1];
        if ($('#isCheckedInvoiceId_' + id).is(':checked')) {
            $("#invoiceId_" + id).attr('required', true);
            $("#invoiceId_" + id).attr('disabled', false);
            $("#challanId_" + id).attr('required', true);
            $("#challanId_" + id).attr('disabled', false);
        } else {
            $("#invoiceId_" + id).attr('required', false);
            $("#invoiceId_" + id).attr('disabled', true);
            $("#challanId_" + id).attr('required', false);
            $("#challanId_" + id).attr('disabled', true);
        }
    }

</script>
