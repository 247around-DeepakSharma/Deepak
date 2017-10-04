<style>
    .table>tbody>tr>td, .table>tbody>tr>th, 
    .table>tfoot>tr>td, .table>tfoot>tr>th, 
    .table>thead>tr>td, .table>thead>tr>th{
    padding: 4px;
    }
</style>
<div class="row">
    <div class="col-md-8 col-md-offset-2 col-sm-12 col-xs-12">
        <table class="table table-striped table-bordered table-hover text-center" style="font-size:12px">
            <thead>
                <th class="text-center">Month</th>
                <th class="text-center">Completed Booking</th>
                <th class="text-center">Cancelled booking</th>
                <th  style="border: 1px solid #fff;"></th>
                <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;
                    <?php if(empty($this->session->userdata('is_prepaid'))){ ?> border-right: 1px solid #fff; <?php }?>"></td>
                <?php if(!empty($this->session->userdata('is_prepaid'))){ ?>
                <th class="text-center">Prepaid Amount</th>
                <?php } ?>
            </thead>
            <tbody>
                <?php foreach ($bookings_count as $val) { ?> 
                <tr>
                    <td><?php echo $val['month']; ?></td>
                    <td><?php echo $val['completed']; ?></td>
                    <td><?php echo $val['cancelled']; ?></td>
                    <td style="border:0px;border:0px;background-color: #fff;"></td>
                    <td style="border-top: 1px solid #fff;background-color: #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff; 
                        <?php if(empty($this->session->userdata('is_prepaid'))){ ?> border-right: 1px solid #fff; <?php }?>"></td>
                    <?php if(!empty($this->session->userdata('is_prepaid'))){ ?>
                    <td style="border:0px;border:0px;background-color: #fff;"></td>
                    <?php } ?>
                </tr>
                <?php } ?>
                <tr style="background-color:#fff; ">
                    <td style="border: 1px solid #fff;"></td>
                    <td style="border: 1px solid #fff;"></td>
                    <td style="border: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;border-right: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;background-color: #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;
                        <?php if(empty($this->session->userdata('is_prepaid'))){ ?> border-right: 1px solid #fff; <?php }?>"></td>
                    <?php if(!empty($this->session->userdata('is_prepaid'))){ ?>
                    <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;"></td>
                    <?php } ?>
                </tr>
                <tr style="background-color:#fff;">
                    <td style="border: 1px solid #fff;"></td>
                    <td style="border: 1px solid #fff;"></td>
                    <td style="border: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;border-right: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;background-color: #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;
                        <?php if(empty($this->session->userdata('is_prepaid'))){ ?> border-right: 1px solid #fff; <?php }?>"></td>
                    <?php if(!empty($this->session->userdata('is_prepaid'))){ ?>
                    <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;"><?php echo $prepaid_amount['prepaid_amount'];?></td>
                    <?php } ?>
                </tr>
                <tr style="background-color:#fff; ">
                    <td style="border-top: 1px solid #fff;border-right: 1px solid #fff;border-left: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;border-right: 1px solid #fff;border-left: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;border-left: 1px solid #fff;border-right: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;border-right: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;background-color: #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;
                        <?php if(empty($this->session->userdata('is_prepaid'))){ ?> border-right: 1px solid #fff; <?php }?>"></td>
                    <?php if(!empty($this->session->userdata('is_prepaid'))){ ?>
                    <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;"></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td rowspan='2' class="text-center" style="padding-top: 16px;">
                        <strong>Escalation (%)</strong>
                    </td>
                    <td >
                        <strong>Installation</strong>
                    </td>
                    <td>
                        <strong>Repair</strong>
                    </td>
                    <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;border-right: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;background-color: #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;
                        <?php if(empty($this->session->userdata('is_prepaid'))){ ?> border-right: 1px solid #fff; <?php }?>"></td>
                    <?php if(!empty($this->session->userdata('is_prepaid'))){ ?>

                    <td style="border-top: 1px solid #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;"></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td><?php echo round($escalation_percentage[0]['unique_installation_escalate_percentage'], 1) ?></td>
                    <td><?php echo round($escalation_percentage[0]['unique_repair_escalate_percentage'], 1) ?></td>
                    <td style="border-top: 1px solid #fff;background-color: #fff;border-bottom: 1px solid #fff;border-right: 1px solid #fff;"></td>
                    <td style="border-top: 1px solid #fff;background-color: #fff;border-bottom: 1px solid #fff;border-left: 1px solid #fff;
                        <?php if(empty($this->session->userdata('is_prepaid'))){ ?> border-right: 1px solid #fff; <?php }?>"></td>
                    <?php if(!empty($this->session->userdata('is_prepaid'))){ ?>

                    <td style="border-top: 1px solid #fff;background-color: #fff;border-left: 1px solid #fff;"></td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php  if(!empty($prepaid_amount['prepaid_msg'])){ ?>
<script src="<?php echo base_url(); ?>js/around_notify.js"></script>
<script>
    
    $(document).ready(function(){
        $.notify({
            message: '<?php echo $prepaid_amount['prepaid_msg']; ?>'

            },{
                type: 'danger',
                placement: {
			from: "bottom",
			align: "center"
		}
            }
        );
    });
</script>
<?php } ?>

<style>
    @keyframes blink {
    50% { opacity: 0.0; }
    }
    @-webkit-keyframes blink {
    50% { opacity: 0.0; }
    }
    .blink {
    animation: blink 1s step-start 0s infinite;
    -webkit-animation: blink 1s step-start 0s infinite;
    }
</style>