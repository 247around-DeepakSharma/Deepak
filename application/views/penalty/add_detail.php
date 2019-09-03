<div id="page-wrapper">
    <div class="row">
        <div class="col-md-10">
            <div class="panel panel-info">
                <div class="panel-heading" >
                    <b>
                        <?php 
                       // echo"<pre>";print_r($penalty);exit;
                        if (isset($penalty['id'])) {
                            echo "Update Penalty Detail";
                            } else {
                            echo "Add Penalty Detail";
                        } 
                        ?>
                    </b>
                </div>
                <?php echo form_open('penalty/get_penalty_detail_form/'.(!empty($penalty['id']) ? $penalty['id'] : NULL)); ?>
                <div class="panel-body form-horizontal">
                    <?php 
                        if ($this->session->userdata('success')) {
                            echo '<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>' . $this->session->userdata('success') . '</strong>
                                </div>';
                            
                            $this->session->unset_userdata('success');
                        }
                    ?>
                    
                    
                    
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="criteria">Escalation Reason</label>
                        </div>
                        <div class="col-md-9">
                            <textarea class="form-control" name="escalation" id="escalation"><?php if(!empty($penalty['escalation_reason'])) { echo $penalty['escalation_reason']; } ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-md-12">
                            <input type="checkbox" onchange="applyPenalty();" name="apply_penalty" id="apply_penalty" <?php if(!empty($penalty['id'])) { 
                                if(!empty(!empty($penalty['escalation_id']))) { echo 'checked';} else {echo '';}
                            } else { echo 'checked'; } ?>>&nbsp;Apply Penalty
                        </div>
                    </div>                    
                    <div class="amount-section">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label for="penalty_amount">Penalty Amount</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="penalty_amount" id="penalty_amount" value="<?= (!empty($penalty['penalty_amount']) ? $penalty['penalty_amount'] : NULL); ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-3">
                                <label for="cap_amount">CAP Amount</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="cap_amount" id="cap_amount" value="<?= (!empty($penalty['cap_amount']) ? $penalty['cap_amount'] : NULL); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="save" value="<?= (!empty($penalty['id']) ? 'Update' : 'Save'); ?>" class="btn btn-success" style="margin-left: 1%;" onclick="return validate_form();">
                        &nbsp;
                        <a class="btn btn-md btn-primary" href="<?php echo base_url() ?>penalty/view_penalty_details" title="Close">Close</a>                
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
            
    </div>
        
</div>


<style>
    .panel {
        margin-top:40px;
        border-radius: 0px !important;
    }
</style>

<script>
    
    $(document).ready(function() {
        applyPenalty();
    });
    
    function applyPenalty() {
        if($('#apply_penalty').prop('checked') === true) {
            $('.amount-section').show();
        } else {
            $('.amount-section').hide();
        }
    }
    
    function validate_form() {
        var penalty_amount = $('#penalty_amount').val();
        var cap_amount = $('#cap_amount').val();
        var reason = $('#escalation').val();
        var is_penalty = $('#apply_penalty').prop("checked");
        
        if(reason == '') {
            alert('Escalation reason cannot be blank.');
            return false;
        }
        
        if((penalty_amount != '' && !$.isNumeric(penalty_amount)) || (cap_amount != 0 && !$.isNumeric(cap_amount))) {
            alert('Amount must be number.');
            return false;
        }
        
        if(penalty_amount != '' && cap_amount != '' && parseInt(cap_amount) < parseInt(penalty_amount)) {
            alert('CAP amount must be greater than or equal to penalty amount.');
            return false;
        } 
        
        if(is_penalty === true && (penalty_amount == '' || cap_amount == '')) {
            alert('If apply penalty is checked then amount fields cannot be blank.');
            return false;
        }
        
    }
</script>