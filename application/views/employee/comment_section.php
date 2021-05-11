
<h1 style='font-size:24px;'>Comments</h1>
<form name="myForm" class="form-horizontal" id ="comment_form" action="javascript:void(0)" method="POST" enctype="multipart/form-data">


    <div>
        <div  class="form-group <?php
        if (form_error('comment')) {
            echo 'has-error';
        }
        ?>">
           
            <label  for="comment">

                <button style="margin-left: 15px;" type="submit" class="btn btn-primary" onclick="load_comment_area();" id="commnet_btn">
                    <span class="glyphicon glyphicon-comment"></span> Add Comment
                </button>   
            </label>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-12" id="comment_section" style="display:none;">
        <textarea rows="5"  class="form-control" id="comment" name="comment" required="" placeholder="Enter comments"></textarea>
        <br>
        <button type="Submit" class="btn btn-primary" id="add_btn" value="Add" onclick="addComment()" >Add</button>
        <button type="Submit" class="btn btn-primary" id="cancel_btn" value="Cancel" onclick="cancel();" >Cancel</button>

    </div>
</div>
<hr/>
<table  class="table table-striped table-bordered table-hover">
    <?php foreach ($comments as $key => $row) { ?>
      <?php if($row['isActive']==1) {?>
        <tr>
            <td style="font-size: 90%;">
                <p style="color:#0033cc">
                    <strong><b> <?php echo $row['full_name']; ?></b></strong> added a comment on -<?php
                $old_date = $row['create_date'];
                $old_date_timestamp = strtotime($old_date);
                $new_date = date('j F, Y g:i A', $old_date_timestamp);
                echo $new_date;
                ?>
                   
                </p>
                <strong id="<?php echo 'comment_text_'. $row['id'];?>"><?php echo $row['remarks']; ?></strong>
            </td>
        </tr>
      <?php }?>
    <?php } ?>
</table>




