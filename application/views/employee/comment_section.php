
<h1 style='font-size:24px;'>Comments</h1>
<table  class="table table-striped table-bordered table-hover" >
    <?php foreach ($comments as $key => $row) { ?>
      <?php if($row['isActive']==1) {?>
        <tr>
            <td style="font-size: 90%;">
                <p style="color:#0033cc">
                    <strong><b> <?php echo $row['employee_id']; ?></b></strong> added a comment on -<?php
                $old_date = $row['create_date'];
                $old_date_timestamp = strtotime($old_date);
                $new_date = date('j F, Y g:i A', $old_date_timestamp);
                echo $new_date;
                ?>
                    <button type="submit" style="float:right; border: hidden; background: rgba(0, 0, 0, 0);" onclick="deleteComment(<?php echo $row['id']?>);">
                        <span style="float:right; color:#ccccb3" class="glyphicon glyphicon-remove"></span>
                    </button> 
                    <button type="submit" style="float:right; border: hidden; background: rgba(0, 0, 0, 0); " onclick="">
                        <span style="float:right; color:#ccccb3" class="glyphicon glyphicon-pencil"></span>
                    </button> 
                   
                </p>
                <strong><b><?php echo $row['remarks']; ?></b></
            </td>
        </tr>
      <?php }?>
    <?php } ?>
</table>


<form name="myForm" class="form-horizontal" id ="comment_form" action="javascript:void(0)" method="POST" enctype="multipart/form-data">


    <div>
        <div  class="form-group <?php
        if (form_error('comment')) {
            echo 'has-error';
        }
        ?>">
           
            <label  for="comment">

                <button style="margin-left: 15px;" type="submit" class="btn btn-primary" onclick="load_comment_area();" id="commnet_btn">
                    <span class="glyphicon glyphicon-comment"></span> Comment
                </button>   
            </label>
        </div>
    </div>
</form>

<div class="col-md-4" id="comment_section" style="display:none;">
    <textarea rows="10"  class="form-control" id="comment" name="comment" required=""></textarea>
    <br>
    <button type="Submit" class="btn btn-primary" id="add_btn" value="Add" onclick="addComment()" >Add</button>
    <button type="Submit" class="btn btn-primary" id="cancel_btn" value="Cancel" onclick="cancel();" >Cancel</button>
    
</div>