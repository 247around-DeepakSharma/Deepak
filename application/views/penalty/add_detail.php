<div id="page-wrapper">
    <div class="row">
        <div class="col-md-6">
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
                            <label for="escalation">Escalation</label>
                        </div>
                        <div class="col-md-9">
                            <select type="text" class="form-control"  id="escalation" name="escalation_id">
                                <option value=""></option>
                                <?php foreach($escalations as $escalation) { ?>
                                <option value="<?= $escalation['id']; ?>"
                                        <?= (!empty($penalty) && $penalty['escalation_id'] == $escalation['id'] ? 'selected' : ''); ?>
                                        ><?= $escalation['escalation_reason']; ?></option>

                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="criteria">Criteria</label>
                        </div>
                        <div class="col-md-9">
                            <textarea class="form-control" name="criteria" id="criteria"><?= (!empty($penalty['criteria']) ? $penalty['criteria'] : NULL); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="penalty_amount">Penalty Amt.</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="penalty_amount" id="penalty_amount" value="<?= (!empty($penalty['penalty_amount']) ? $penalty['penalty_amount'] : NULL); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="cap_amount">CAP Amt.</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="cap_amount" id="cap_amount" value="<?= (!empty($penalty['cap_amount']) ? $penalty['cap_amount'] : NULL); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" name="save" value="<?= (!empty($penalty['id']) ? 'Update' : 'Save'); ?>" class="btn btn-success" style="margin-left: 2%;">
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
    }
</style>

<script>
    $('#partner').select2({
        placeholder: 'Select Partner'
    });
    $('#escalation').select2({
        placeholder: 'Select Escalation'
    });
    
</script>