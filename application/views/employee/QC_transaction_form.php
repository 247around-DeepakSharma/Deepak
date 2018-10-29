
<div class="container">
     <?php if($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:20;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('success') . '</strong>
            </div>';
            }
            ?>
        
            <?php if($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:20;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('error') . '</strong>
            </div>';
            }
         ?>
    <div class="panel panel-info">
        <div class="panel-heading">Qwik Cilver Transaction Form</div>
        
        <form action="<?php echo base_url(); ?>employee/invoice/process_to_send_QC_transction" method="POST">
            <input type="hidden" name="partner_vendor" value="partner">
            <input type="hidden" name="partner_vendor_id" value="<?PHP echo QWIKCILVER_PARTNER_ID; ?>">
            <input type="hidden" name="credit_debit" value="Credit">
            <input type="hidden" name="bankname" value="">
            <input type="hidden" name="tds_amount" value="0">
            <input type="hidden" name="transaction_mode" value="transfer">
            <input type="hidden" name="transaction_id" value="">
            <div class="panel-body">
                <div class="row">
                <div class="col-md-6 form-group <?php if( form_error('transction_amount') ) { echo 'has-error';} ?>">
                    <label for="transction_amount">Amount *</label>
                    <input type="number" class="form-control" id="transction_amount" name="amount" placeholder="Enter Transaction Amount" name="transaction_amount" required>
                    <?php echo form_error('transction_amount'); ?>
                </div>
                <div class="col-md-6 form-group <?php if( form_error('transction_date') ) { echo 'has-error';} ?>">
                    <label for="transction_date"> Date *</label>
                    <input type="date" class="form-control" id="transction_date" name="tdate" placeholder="Enter Transaction Date" required>
                    <?php echo form_error('transction_date'); ?>
                </div>
                </div>
                 <div class="form-group <?php if( form_error('review') ) { echo 'has-error';} ?>">
                    <label for="review">Description *</label>
                    <textarea rows="3" cols="3" class="form-control" id="review" name="description" placeholder="Description" name="review" required></textarea>
                    <?php echo form_error('review'); ?>
                </div>
                <button type = "submit" class = "btn btn-primary">Submit</button>
            </div>
        </form>
             </div>
    </div>
<?php if($this->session->userdata('success')) { $this->session->unset_userdata('success'); } ?>
<?php if($this->session->userdata('error')) { $this->session->unset_userdata('error'); } ?>
                
                
                

