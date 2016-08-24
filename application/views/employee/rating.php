<div id="page-wrapper">
  <div class="">
    <div class="row">
      <div style="width:600px;margin:50px;">
         
        <h2 style="color:blue;">Rating Given by Customer</h2>
         <br>
          
        <form class="form-horizontal" id ="rating_form" action="<?php echo base_url()?>employee/booking/process_rating_form/<?php echo $data[0]['booking_id'];?>/<?php echo $status; ?>" method="POST">

        <div><input type="hidden" name="user_id" value="<?php echo $data[0]['user_id'];?>"></div>
        
        <div class="form-group <?php if( form_error('rating_star') ) { echo 'has-error';} ?>">
                        <label for="rating_star" class="col-md-2">Star Rating</label>
                        <div class="col-md-6">
                            <Select type="text" class="form-control"  name="rating_star" value="<?php echo set_value('rating_star'); ?>" >
                            <option>Select</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            </Select>
                        </div>
        </div>

        <div class="form-group <?php if( form_error('rating_comments') ) { echo 'has-error';} ?>">
            <label for="rating_comments" class="col-md-2">Rating Comments</label>
            <div class="col-md-6">
                <textarea style="height:80px;width:400px;" class="form-control"  name="rating_comments"></textarea>
                <?php echo form_error('rating_comments'); ?>
            </div>
        </div>
    <br>
     
      <div style="float:left;width:600px;">
        <div><input type="Submit" value="Save Rating" class="btn btn-primary"></div>
        </form>
      </div>
      
    </div>
  </div>
</div>
</div>
