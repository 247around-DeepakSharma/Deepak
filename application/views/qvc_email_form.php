
<div class="container">
    <div class="panel panel-info">
        <div class="panel-heading">Email Form</div>
        <form action="<?php echo base_url(); ?>employee/invoice/process_to_send_qvc_transction" method="POST">
        <div class="panel-body">
            <div class="form-group <?php if( form_error('transction_date') ) { echo 'has-error';} ?>">
                <label for="transction_date">Transction Date</label>
                <input type="date" style="width:345px;" class="form-control" id="transction_date" placeholder="Enter Transction Date" name="transction_date" required>
                <?php echo form_error('transction_date'); ?>
            </div>
            <div class="form-group <?php if( form_error('transction_amount') ) { echo 'has-error';} ?>">
                <label for="transction_amount">Transction Amount</label>
                 <input type="number" class="form-control" id="transction_amount" placeholder="Enter Transction Amount" name="transction_amount" required>
                <?php echo form_error('transction_amount'); ?>
            </div>
             <div class="form-group <?php if( form_error('review') ) { echo 'has-error';} ?>">
                <label for="review">Review</label>
                <textarea rows="3" cols="3" class="form-control" id="review" placeholder="Review" name="review" required></textarea>
                <?php echo form_error('review'); ?>
            </div>
            <button type = "submit" class = "btn btn-default">Submit</button>
            </div>
        </form>
             </div>
    </div>
                
                
                

