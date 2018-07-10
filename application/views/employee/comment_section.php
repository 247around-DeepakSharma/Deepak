

<table  class="table table-striped table-bordered table-hover" >
    <?php foreach ($comments as $key => $row) { ?>
        <tr>
            <td style="font-size: 90%;">
                <p style="color:#0033cc">
                    <strong><b> <?php echo $row['employee_id']; ?></b></strong> added a comment on - <?php echo date('jS M, Y', strtotime($row['create_date'])); ?>
<!--                    <button type="submit" style="float:right; border: hidden; background: rgba(0, 0, 0, 0);" onclick="deleteComment($row['id']);">
                        <span style="float:right; color:#ccccb3" class="glyphicon glyphicon-remove"></span>
                    </button> -->

                </p>
                <strong><b><?php echo $row['remarks']; ?></b></
            </td>
        </tr>
    <?php } ?>
</table>


<form name="myForm" class="form-horizontal" id ="comment_form" action="javascript:void(0)" method="POST" enctype="multipart/form-data">


    <div class="form-group col-md-8">
        <div  class="form-group <?php
        if (form_error('comment')) {
            echo 'has-error';
        }
        ?>">
            <div class="col-md-8">
                <input  type="text" class="form-control" id="comment" name="comment" required >
                <?php echo form_error('comment'); ?>
            </div>
            <label  for="comment" class="col-md-2">

                <button type="submit" class="btn btn-primary"  onclick="addComment()">
                    <span class="glyphicon glyphicon-comment"></span> Comment
                </button>   
            </label>
        </div>
    </div>
</form>