
<?php
    if(!empty($am_compare))
    {
       
        ?>
<form  method="post" action="javascript:void(0)" id="rm_compair_booking_form" name="rm_compair_booking_form" style="margin-bottom: 25px" onsubmit="getcompairamdata()">
   <?php
    foreach($am_compare as $value)
   {
     $partner_id_explode=explode(',',$value['partnerId']);
    ?>
<div class="row">
<div class="col-md-3 col-md-offset-1">
                                        <div class="item form-group">
                                            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-left: 0px;">
                                                <label><?php echo $value['full_name'];?></label>
                                                <script type="text/javascript">
                                                    var id='<?php echo $value['account_manager_id']; ?>';
                                                    $('#am_partner_'+id).select2();
                                                </script>
                                                <select class="form-control filter_table" id="<?php echo 'am_partner_'.$value['account_manager_id'];?>" name="<?php echo 'am_partner['.$value['account_manager_id'].'][]';?>" multiple>
<!--                                                    <option value="" selected="selected">All</option>-->
                                                    <?php
                                                    foreach($partner_arr as $value1)
                                                    {
                                                       $selected=in_array($value1['id'],$partner_id_explode)?'selected':'';
                                                    ?>
                                                    <option value="<?php echo $value1['id']?>" <?php echo $selected  ?>><?php echo $value1['public_name']?></option>
                                                    <?php
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
</div>
<?php
}
    }
 ?>
</div>
                                   <div class="form-group col-md-2" style="width: 120px;float:right;margin-right: 30px;">
                                        <input type="submit" class="btn btn-primary"  style="margin-top: 23px;background: #405467;border-color: #405467;" value="Compare AM" />
                                   </div>

</form> 


