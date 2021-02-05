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

            <h1>Partners
                <div class="pull-right" style="margin:0px 10px 20px 0px;">
                    <a href="<?php echo base_url(); ?>employee/partner/get_add_partner_form"><input class="btn btn-sm btn-primary" type="Button" value="Add Partner"></a>
                    <button class="btn btn-sm btn-success" onclick="get_download_history()" >Download Partner List</button>
        <!--            <a href="<?php echo base_url(); ?>employee/partner/upload_partner_brand_logo"><input class="btn btn-primary" type="Button" value="Upload Partner Brand Logo" style="margin-left:10px;"></a>-->
                </div>
            </h1>
        </div>
        <div class="col-md-12" style="margin-bottom: 15px;">   
            <form class="form-inline pull-left" action="<?php echo base_url(); ?>employee/partner/viewpartner" method="post">
                <div class="form-group">
                    <label for="Service Code">Active/Disabled</label>
                    <select class="form-control" id="partner_type" name="active">
                        <option value="All" <?php if ($active == 'All') { echo "selected"; } ?>>All</option>
                        <option value="1" <?php if ($active == '1') { echo "selected"; } ?>>Active</option>
                        <option value="0" <?php if ($active == '0') { echo "selected"; } ?>>Disabled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Service Code">Partner Type</label>
                    <select class="form-control filter_table" id="partner_sc" name="partnerType[]" multiple="multiple" placeholder="All">
                        <option value="<?php echo OEM; ?>" <?php if (in_array(OEM, $partnerType)) { echo "selected"; } ?>><?php echo OEM ?></option>
                        <option value="<?php echo EXTWARRANTYPROVIDERTYPE; ?>" <?php if (in_array(EXTWARRANTYPROVIDERTYPE, $partnerType)) { echo "selected"; } ?>><?php echo EXTWARRANTYPROVIDERTYPE ?></option>
                        <option value="<?php echo BUYBACKTYPE; ?>" <?php if (in_array(BUYBACKTYPE, $partnerType)) {
                            echo "selected";
                        } ?>><?php echo BUYBACKTYPE ?></option>
                        <option value="<?php echo INTERNALTYPE; ?>" <?php if (in_array(INTERNALTYPE, $partnerType)) {
                            echo "selected";
                        } ?>><?php echo INTERNALTYPE ?></option>
                        <option value="<?php echo ECOMMERCETYPE; ?>" <?php if (in_array(ECOMMERCETYPE, $partnerType)) {
                            echo "selected";
                        } ?>><?php echo ECOMMERCETYPE ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="Service Code">Account Manager</label>
                    <select class="form-control" id="accountManager" name="accountManager">
                        <option value="All" <?php if ($ac == 'All') {
                            echo "selected";
                        } ?>>All</option>
                        <option value="NULL" <?php if ($ac == 'NULL') {
                echo "selected";
            } ?>>No One</option>
            <?php
            foreach ($accountManagerArray as $accountManager) {
                ?>
                            <option value='<?php echo $accountManager['id'] ?>' <?php if ($ac == $accountManager['id']) {
                echo "selected";
            } ?>><?php echo $accountManager['full_name'] ?></option>
                <?php
            }
            ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="submit" value="Apply Filter" class="btn btn-sm btn-success"> 
                </div>

            </form>
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

            <table class="table table-striped table-bordered" id="partner_details">
				<thead>
                <tr>
                    <th class='jumbotron'>ID</th>
                    <th class='jumbotron' style="text-align: center">Company Name</th>
                    <th class='jumbotron' style="text-align: center">Login</th>
                    <th class='jumbotron' style="text-align: center">Appliances/Brands</th>
                    <th class='jumbotron' style="text-align: center">PoC Name</th>
                    <th class='jumbotron' style="text-align: center">PoC Phone</th>
                    <th class='jumbotron' style="text-align: center">PoC Email</th>
                    <th class='jumbotron' style="text-align: center">Customer Care Phone</th>
                    <th class='jumbotron' style="text-align: center">Prepaid</th>
                    <th class='jumbotron' style="text-align: center">Go To Invoice Page</th>
                    <th class='jumbotron' style="text-align: center">Action</th>
                    <th class='jumbotron' style="text-align: center">Generate Price</th>
                    <th class='jumbotron' style="text-align: center">View Price</th>
                    <th class='jumbotron' style="text-align: center">Summary Report<br>Send / View</th>
                    <th class='jumbotron' style="text-align: center">Activation / Deactivation<br>History</th>
                </tr>
				</thead>
				<tbody>
                        <?php foreach ($query as $key => $row) { ?>
                    <tr>
                        <td><?= ($key + 1) . '.'; ?></td>
                        <td>
                            <a href="<?php echo base_url(); ?>employee/partner/editpartner/<?= $row['id']; ?>"><?= $row['company_name']; ?></a>
                            <br/>
                            <strong><?php echo $row['public_name']; ?> (<b><?php echo $row['code']; ?></b>)</strong>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-sm btn-success"  onclick='return login_to_partner(<?php echo $row['id'] ?>)' title="<?php echo isset($row['clear_text']) && $row['clear_text'] ? $row['user_name'] . '/' . $row['clear_text'] : ''; ?>"><i class="fa fa-sign-in" aria-hidden="true"></i></a>  
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
                        <td><?= $row['customer_care_contact']; ?></td>
                        <td><?php if ($row['is_prepaid'] == 1) { ?> <i class="fa fa-credit-card fa-2x" aria-hidden="true"></i><?php } ?></td>
                        <td><a class="btn btn-sm btn-primary" href="<?php echo base_url(); ?>employee/invoice/invoice_summary/partner/<?php echo $row['id']; ?>" target="_blank" title="Go To Invoice"><i class="fa fa-inr" aria-hidden="true"></i></a></td>
                           
                        <!--Only Allow Admin to activate/deactivate partners -->
                        <?php
                            if ($this->session->userdata('user_group') == _247AROUND_ADMIN)
                            {
                                $activeClass = "activate_partner";
                                $deactiveClass = "deactivate_partner";
                            }
                            else
                            {
                                $activeClass = "disabled";
                                $deactiveClass= "disabled";
                            }
                        ?>
                        
                        <td><?php if ($row['is_active'] == 1) { ?>
                            <a class="btn btn-sm btn-primary <?php echo $activeClass?>" href="javascript:void(0);"  id="<?php echo $row['id'] ?>" title="Deactivate"><i class="fa fa-check" aria-hidden="true"></i></a>       
                            <?php } else { ?>
                                <!--Do not allow Partner Activation if PAN details not found for partner-->                                    
                                <?php if(empty($row['pan']) || empty($row['pan_file'])){ ?>
                                    <a class="btn btn-sm btn-danger" onclick="alert('Please Enter PAN Details of Partner to allow Activation');" title="Save PAN Details of Partner to allow Activation"><i class="fa fa-ban" aria-hidden="true"></i></a><?php               
                                } else { ?>
                                    <a class="btn btn-sm btn-danger <?php echo $deactiveClass?>" href="javascript:void(0);" id="<?php echo $row['id'] ?>" title="Activate"><i class="fa fa-ban" aria-hidden="true"></i></a><?php               
                                } ?>                                
                            <?php } ?>
                        </td>
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
                        <td align="center">
                            <button type="button" class="btn btn-sm btn-info" id='btn_history' title="Partner Activation/Deactivation History" onclick='get_activation_history(<?= $row['id'] ?>,"<?= $row['company_name']?>")'><i class="fa fa-history" aria-hidden="true"></i></button>
                            <?php
                            /*if (array_key_exists($row['id'], $push_notification)) {
                                $tooltipText = '';
                                if (array_key_exists("subscription_count", $push_notification[$row['id']])) {
                                    $tooltipText = $tooltipText . "Subscriptions: " . $push_notification[$row['id']]['subscription_count'];
                                }
                                if (array_key_exists("blocked_count", $push_notification[$row['id']])) {
                                    $tooltipText = $tooltipText . ", Blocked: " . $push_notification[$row['id']]['blocked_count'];
                                }
                                if (isset($push_notification[$row['id']]['blocked_count']) && !isset($push_notification[$row['id']]['subscription_count'])) {
                                    echo '<button type="button" class="btn btn-info btn-lg glyphicon glyphicon-ban-circle" data-toggle="tooltip" data-placement="left" title="' . $tooltipText . '" style="border-radius: 3px;padding: 2px 7px;margin: 0px 10px;"></button>';
                                } else if (isset($push_notification[$row['id']]['unsubscription_count']) && !isset($push_notification[$row['id']]['subscription_count'])) {
                                    echo '<button type="button" class="btn btn-info btn-lg " data-toggle="tooltip" data-placement="left" title="' . $tooltipText . '" style="border-radius: 3px;padding: 2px 7px;margin: 0px 10px;"><i class="fa fa-bell-slash" aria-hidden="true"></i></button>';
                                } else if (isset($push_notification[$row['id']]['subscription_count'])) {
                                    echo '<button type="button" class="btn btn-info btn-lg " data-toggle="tooltip" data-placement="left" title="' . $tooltipText . '" style="border-radius: 3px;padding: 2px 7px;margin: 0px 10px;"><i class="fa fa-bell" aria-hidden="true"></i></button>';
                                }
                            } else {
                                echo '<button type="button" class="btn btn-sm btn-info title="Notification"><i class="fa fa-spinner" aria-hidden="true"></i></button>';
                            }*/
                            ?>
                        </td>
                    </tr>
<?php } ?>
            </table>

		</tbody>

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

<!-- This modal class is used to view Partner Activation/Deactivation History -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Partner Activation/Deactivation History</b></h4>
      </div>
      <div class="modal-body" id='div_historymodal'>
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
	var partner_details;
		partner_details = $("#partner_details").DataTable(
		{  
		order:[[ 2, "desc" ]],
		pageLength: 50,
		"sDom": '<"row view-filter"<"col-sm-12"<"pull-left"l><"pull-right"f><"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-left"ip>>>'

		});
		partner_details.draw(false);


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
    
    $(document).on('click',".activate_partner", function(){
        var partner_id = $(this).attr("id");
        if(confirm("Are you sure you want to Deactivate ?")){
            window.location = "<?php echo base_url() ?>employee/partner/deactivate/"+partner_id;
        } 
    });
    
    $(document).on('click',".deactivate_partner", function(){
        var partner_id = $(this).attr("id");
        if(confirm("Are you sure you want to Activate ?")){
            window.location = "<?php echo base_url() ?>employee/partner/activate/"+partner_id;
        } 
    });
    
 function get_download_history() {
       var active = $("#partner_type").val();
       var partner_type = $("#partner_sc").val();
       var accountManager = $("#accountManager").val();
       location.href = "<?php echo base_url(); ?>employee/partner/download_partner_summary_details?active="+active+"&partner_type=" +partner_type+
               "&accountManager=" + accountManager;
    }
 
</script>
