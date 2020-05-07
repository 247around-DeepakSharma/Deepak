<!--<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.3.2.min.js"></script>-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/select2.min.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/select2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.7.1.custom.min.js"></script>

<script type="text/javascript">

    function login_to_partner(partner_id) {
        var c = confirm('Login to Partner CRM');
        if (c) {
            $.ajax({
                url: '<?php echo base_url() . "employee/login/allow_log_in_to_partner/" ?>' + partner_id,
                success: function (data) {
                    //console.log(data);
                    window.open("<?php echo base_url() . 'partner/dashboard' ?>", '_blank');
                }
            });

        } else {
            return false;
        }
    }
</script>


<div  id="page-wrapper">
    <div class="row">
        <div class="col-md-12">
            <h1>Kenstar</h1>
        </div>
<div class="col-md-12">        
<?php
if ($this->session->userdata('success')) {
    echo '<div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('success') . '</strong>
                        </div>';
}
if ($this->session->userdata('error')) {
    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $this->session->userdata('error') . '</strong>
                        </div>';
}
?>
<?php if ($this->session->userdata('user_group') != 'closure') { ?>
                <?php } ?>

            <table class="table table-striped table-bordered">

                <tr>
                    <th class='jumbotron' style="text-align: center">Company Name</th>
                    <th class='jumbotron' style="text-align: center">Appliances/Brands</th>
                    <th class='jumbotron' style="text-align: center">PoC Name</th>
                    <th class='jumbotron' style="text-align: center">PoC Phone</th>
                    <th class='jumbotron' style="text-align: center">PoC Email</th>
<!--                    <th class='jumbotron' style="text-align: center">Go To Invoice Page</th>-->
                    <th class='jumbotron' style="text-align: center">Generate Price</th>
                    <th class='jumbotron' style="text-align: center">View Price</th>
                    <th class='jumbotron' style="text-align: center">Summary Report<br>Send / View</th>
                </tr>


                        <?php foreach ($query as $key => $row) { ?>
                    <tr>
                        <td>
                            <a href="<?php echo base_url(); ?>employee/partner/editpartner/<?= $row['id']; ?>"><?= $row['company_name']; ?></a>
                            <br/>
                            <strong><?php echo $row['public_name']; ?> (<b><?php echo $row['code']; ?></b>)</strong>
                        </td>

                        <td>
                            <?php
                            if (!empty($service_brands[$key])) {
                                $str = "";
                                foreach ($service_brands[$key] as $val) {
                                    $str .= ' <b>' . $val['services'] . '</b> - ' . $val['brand'] . ' ,';
                                }
                                echo (rtrim($str, ","));
                            }
                            ?>
                        </td>

                        <td><?= $row['primary_contact_name']; ?></td>
                        <td>
    <?= $row['primary_contact_phone_1']; ?>
                        </td>
                        <td><?= wordwrap($row['primary_contact_email'],30,'<br>',true); ?></td>
                        <td>
                            <a  class="btn btn-sm btn-success" href="<?php echo base_url(); ?>employee/service_centre_charges/generate_service_charges_view/<?php echo $row['id']; ?>" title="Generate charge"><i class="fa fa-plus" aria-hidden="true"></i></a>  
                        </td>
                        <td>
                            <a  class="btn btn-sm btn-warning" href="<?php echo base_url(); ?>employee/service_centre_charges/show_charge_list/<?php echo $row['id']; ?>" title="View charge"><i class="fa fa-eye" aria-hidden="true"></i></a>  
                        </td>
                        <td style="width: 96px;">
                            <a style="float:left" href="<?php echo base_url(); ?>BookingSummary/send_leads_summary_mail_to_partners/<?php echo $row['id']; ?>" class="btn btn-sm btn-color" title="Send Summary Email"><i class="fa fa-envelope" aria-hidden="true"></i></a>  
                            <a target="_blank" style="float:right;" href="<?php echo base_url(); ?>BookingSummary/old_summary_report_view/<?php echo $row['id']; ?>" class="btn btn-sm btn-color" title="Download Summary Report"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i></a>  
                        </td>
                    </tr>
<?php } ?>
            </table>



        </div>
    </div>
</div>      
<!-- This model class is used Update History View-->
<div class="modal fade" id="history_view" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Updated History View</h4>
            </div>
            <div class="modal-body">
                <div id="table_container"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<?php if ($this->session->userdata('success')) {
    $this->session->unset_userdata('success');
} ?>
<?php if ($this->session->userdata('error')) {
    $this->session->unset_userdata('error');
} ?>
<script>
    $(document).ready(function () {
        $("#partner_sc").select2();
        $(".select2-container--default").css("width", "auto");
        $(".select2-container--default").css("min-width", "230px");
    });

    function get_history_view(partnerID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/vendor/get_partner_vendor_updation_history_view/' + partnerID + '/partners/trigger_partners',
            success: function (response) {
                console.log(response);
                $("#table_container").html(response);
            }
        });
    }
    function get_activation_history(partnerID,companyName) {
        $("#div_historymodal").html('<center><img style="width: 46px;" src="<?php echo base_url(); ?>images/loader.gif" /></center>');
        $("#myModal").modal('show');
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_activation_deactivation_history',
            data: {'partner_id': partnerID},
            success: function (response) {
                response = JSON.parse(response);
                
                if(response['msg'] === 'failed')
                {
                    alert(response['data']);
                }
                var str='';
                
                str += '<h4><center>'+companyName+'</center></h4></br>';
                str += "<table class='table table-bordered table-hover table-responsive'><thead><th>S.No.</th><th>Old State</th><th>New State</th><th>Date Modified</th></thead>";
                
                var old_state='';
                var new_state='';
                for(var i=0;i<response['data'].length;i++)
                {
                    new_state= ((response['data'][i]['status'] == 1)?'Active':'Deactive');
                    str += "<tbody><td>"+(i+1)+"</td><td>"+old_state+"</td><td>"+new_state+"</td><td>"+response['data'][i]['date']+"</td></tbody></table";
                    old_state= new_state;
                }
                $("#div_historymodal").empty();
                $("#div_historymodal").append(str);
            }
        });
    }
</script>
