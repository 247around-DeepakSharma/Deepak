<!--<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-1.3.2.min.js"></script>-->
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

    <div class="booking_history_div">   
        <h1 style='font-size:24px;'>Booking History</h1>
        <table  class="table table-striped table-bordered">
            <tr>
                <th class="jumbotron" style="text-align: center">S.N</th>
                <th class="jumbotron" style="text-align: center">Old State</th>
                <th class="jumbotron" style="text-align: center">New State</th>
                <th class="jumbotron" style="text-align: center">Remarks</th>
                <th class="jumbotron" style="text-align: center">Agent</th>
                <th class="jumbotron" style="text-align: center">Partner</th>
                <th class="jumbotron" style="text-align: center">Date</th>
            </tr>
            <?php foreach ($data as $key => $row) { ?>
            <tr>
                <td><?php echo ($key + 1) . '.'; ?></td>
                <td><?php echo $row['old_state']; ?></td>
                <td><?php echo $row['new_state']; ?></td>
                <td><?php echo $row['remarks']; ?></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php
                    if ($row['source'] == "Website") {
                        echo '247 Around';
                    } else {
                        echo $row['source'];
                    }
                    ?></td>
                <td><?php
                    $old_date = $row['create_date'];
                    $old_date_timestamp = strtotime($old_date);
                    $new_date = date('j F, Y g:i A', $old_date_timestamp);
                    echo $new_date;
                    ?>
                </td>
            </tr>
            <?php } ?>
        </table>
   </div>
    <hr>
    <?php if (!empty($sms_sent_details)) { ?>
    
   <h1 style='font-size:24px;'>Booking SMS</h1>
    <table  class="table table-striped table-bordered table-hover">
        <tr>
            <th class="jumbotron" style="text-align: center;width: 1%">S.N</th>
            <th class="jumbotron" style="text-align: center">Phone</th>
            <th class="jumbotron" style="text-align: center;width:45%;">Content</th>
            <th class="jumbotron" style="text-align: center">Sent on Date</th>
        </tr>
        <?php foreach ($sms_sent_details as $key => $row) { ?>
        <tr>
            <td><?php echo ($key + 1) . '.'; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td style="font-size: 90%;"><?php echo $row['content']; ?></td>
            <td><?php
                $old_date = $row['created_on'];
                $old_date_timestamp = strtotime($old_date);
                $new_date = date('j F, Y g:i A', $old_date_timestamp);
                echo $new_date;
                ?>
            </td>
        </tr>
        <?php } ?>

</table>
<?php } ?>

    
  </table>
    <hr>
    <?php if (!empty($email_sent_details)) { ?>
    
    <h1 style='font-size:24px;'>Booking Email</h1>
    <table  class="table table-striped table-bordered table-hover">
        <tr>
            <th class="jumbotron" style="text-align: center;width: 1%">S.N</th>
            <th class="jumbotron" style="text-align: center">Subject</th>
            <th class="jumbotron" style="text-align: center">From</th>
            <th class="jumbotron" style="text-align: center;">To</th>
            <th class="jumbotron" style="text-align: center">Cc</th>
            <th class="jumbotron" style="text-align: center">Bcc</th>
            <th class="jumbotron" style="text-align: center;">Message</th>
        </tr>
        <?php foreach ($email_sent_details as $key => $row) { ?>
        <tr>
            <td><?php echo ($key + 1) . '.'; ?></td>
            <td><?php echo $row['subject'] ?></td>
            <td><?php echo $row['email_from'] ?></td>
            <td><?php echo $row['email_to'] ?></td>
            <td><?php echo $row['cc'] ?></td>
            <td><?php echo $row['bcc'] ?></td>
            <td style="text-align: center;"><textarea style="display: none"><?php echo $row['message'];  ?></textarea><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#email_message" onclick="viewEmailMessage(this)">View Message</button></td>
        </tr>
        <?php } ?>
<!--</div>-->
</table>
    <div id="email_message" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" style="text-align: center;">Message</h4>
      </div>
        <div class="modal-body" id="email_message_body" align="center">
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<?php } ?>

<script>

function viewEmailMessage(button){
   $("#email_message_body").html($(button).parent("td").find("textarea").text());
   $("#email_message_body div").each(function(){ $(this).removeAttr("style");  });
}

</script>