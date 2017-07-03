<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<style>
    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        height: 34px;
    }
</style>
<?php $offset = $this->uri->segment(5); ?>
<script>
    $(function(){
      $('#dynamic_select').bind('change', function () {
          var url = $(this).val();
          if (url) {
              window.location = url;
          }
          return false;
      });
    });
</script>
<div id="page-wrapper" >
    <?php  if($this->uri->segment(3) == 'show_brackets_list'){?>
            <div class="pagination">
                <select id="dynamic_select" class="form-control">
                    <option value="<?php echo base_url().'employee/inventory/show_brackets_list'?>" <?php if($this->uri->segment(4) == 50){ echo 'selected';}?>>50</option>
                    <option value="<?php echo base_url().'employee/inventory/show_brackets_list/100/0'?>" <?php if($this->uri->segment(4) == 100){ echo 'selected';}?>>100</option>
                    <option value="<?php echo base_url().'employee/inventory/show_brackets_list/200/0'?>" <?php if($this->uri->segment(4) == 200){ echo 'selected';}?>>200</option>
                    <option value="<?php echo base_url().'employee/inventory/show_brackets_list/500/0'?>" <?php if($this->uri->segment(4) == 500){ echo 'selected';}?>>500</option>
                    <option value="<?php echo base_url().'employee/inventory/show_brackets_list/0/All'?>"<?php if($this->uri->segment(5) == "All"){ echo 'selected';}?> >All</option>

                </select>
            </div>
            <?php } ?>
    <div class="row">
        <div class="filter_brackets" style="margin-top:15px;">
            <div class="filter_box">
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_role" name="sf_role">
                            <option selected disabled>Select Role</option>
                            <option value="order_received_from">Order Received From</option>
                            <option value="order_given_to">Order Given To</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select class="form-control" id="sf_id" name="sf_id">
                            <option selected="" disabled="">Select Service Center</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                            <input type="text" class="form-control valid" id="daterange" name="daterange">
                    </div>
                    <div class="col-sm-3">
                        <div class="btn btn-success" id="filter">Filter</div>
                    </div>
            </div>
        </div>
    </div>
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;">Brackets List</center></div>
        <div class="col-md-12">
            <div class="col-md-4">
                <div class="col-md-6">
                    <div style="background-color: #FF8080;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-6" style="margin-top:10px;margin-bottom: 10px;"> 
                    <span>Requested Brackets List</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-6">
                    <div style="background-color: #FFEC8B;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-6" style="margin-top:10px;margin-bottom: 10px;">
                    <span >Shipped Brackets List</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="col-md-6">
                    <div style="background-color: #4CBA90;margin-top:10px;margin-bottom: 10px;border-radius: 10px;">&nbsp;&nbsp;</div>
                </div>
                <div class="col-md-6" style="margin-top:10px;margin-bottom: 10px;">
                    <span >Received Brackets List</span>
                </div>
            </div>
            <br><hr>
        </div>
        
        <div class="panel-body">
             <?php
                    if ($this->session->userdata('brackets_update_success')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:30px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('brackets_update_success').'</strong>
                    </div>';
                    }
                    ?>
             <?php
                    if ($this->session->userdata('brackets_cancelled_error')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:30px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->userdata('brackets_cancelled_error').'</strong>
                    </div>';
                    }
                    ?>
            <div id="loader"><img src="<?php echo base_url(); ?>images/loadring.png" style="display:none;"></div>
            <div class="show_brackets_list" id="brackets_list_box">
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="5" class="jumbotron" style="text-align: center;"><b>Requested Brackets</b></td>
                        <td colspan="5" class="jumbotron" style="text-align: center;"><b>Shipped Brackets</b></td>
                        <td colspan="5" class="jumbotron" style="text-align: center;"><b>Received Brackets</b></td>
                        <td></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <th class="jumbotron">S.N.</th>
                        <th class="jumbotron" >Order ID</th>
                        <th class="jumbotron" style="width:15%">Received From</th>
                        <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">26-32"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">36-42"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">&gt;43"</th>
                        <th class="jumbotron" style="padding:1px;">Total</th>
                        <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">26-32"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">36-42"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">&gt;43"</th>
                        <th class="jumbotron" style="padding:1px;">Total</th>
                        <th class="jumbotron" style="padding:1px;width:4%">19-24"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">26-32"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">36-42"</th>
                        <th class="jumbotron" style="padding:1px;width:4%">&gt;43"</th>
                        <th class="jumbotron" style="padding:1px;">Total</th>
                        <th class="jumbotron" style="padding:1px;text-align: center">Date</th>
                        <!--<th class="jumbotron" style="width:20%">Given To</th>-->
                        <th style="text-align: center" colspan="2" class="jumbotron">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($brackets as $key=>$value){
                        $is_shipped = $value['is_shipped'];
                        $is_received = $value['is_received'];
                        $style="";
                        if($is_shipped == 0 && $is_received == 0){
                            $style='style="background-color:#ff8080"';
                        }elseif($is_shipped == 1 && $is_received == 0){
                            $style='style="background-color:#FFEC8B"';
                        }elseif($is_shipped == 1 && $is_received == 1){
                            $style='style="background-color:#4CBA90"';
                        }
                        $date = "";
                        if($value['order_date'] > 0){
                            $date = $value['order_date'];
                        }
                        if($value['shipment_date'] > 0){
                            $date = $value['order_date'];
                        }
                        if($value['received_date'] > 0){
                            $date = $value['received_date'];
                        }
                        ?>		
                    <tr <?php echo $style?>>
                            <td ><?php echo ($key+1).'.'?></td>
                            <td ><a href="<?php echo base_url()?>employee/inventory/show_brackets_order_history/<?php echo $value['order_id']?>" target="_blank"><?php echo $value['order_id']?></a></td>
                            <td style="text-align: center;">
                                <?php echo $order_received_from[$key]['owner_name'].'<br>'?>
                                <?php echo $order_received_from[$key]['name']?>
                            </td>
                            <td style="text-align: center;"><?php echo $value['19_24_requested']?></td>
                            <td style="text-align: center;"><?php echo $value['26_32_requested']?></td>
                            <td style="text-align: center;"><?php echo $value['36_42_requested']?></td>
                            <td style="text-align: center;"><?php echo $value['43_requested']?></td>
                            <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_requested']?></strong></td>
                            <td style="text-align: center;"><?php echo $value['19_24_shipped']?></td>
                            <td style="text-align: center;"><?php echo $value['26_32_shipped']?></td>
                            <td style="text-align: center;"><?php echo $value['36_42_shipped']?></td>
                            <td style="text-align: center;"><?php echo $value['43_shipped']?></td>
                            <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_shipped']?></strong></td>
                            <td style="text-align: center;"><?php echo $value['19_24_received']?></td>
                            <td style="text-align: center;"><?php echo $value['26_32_received']?></td>
                            <td style="text-align: center;"><?php echo $value['36_42_received']?></td>
                            <td style="text-align: center;"><?php echo $value['43_received']?></td>
                            <td style="text-align: center;"><strong style="font-weight: 900;"><?php echo $value['total_received']?></strong></td>
                            <td style="text-align: center;"><?php 
                                    $old_date = $date;
                                    $old_date_timestamp = strtotime($old_date);
                                    $new_date = date('j M, Y g:i A', $old_date_timestamp);  
                                    echo $new_date;
                            ?></td>
                            <!--<td><?php //echo $order_given_to[$key]?></td>-->
                            <td>
                                <a href="<?php echo base_url();?>employee/inventory/get_update_requested_form/<?php echo $value['order_id']?>" class="btn btn-sm btn-primary" title="Update Requested" <?php if($value['is_shipped'] == 1 || $value['active'] == 0){echo 'disabled=TRUE';}?> > <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                <a href="<?php echo base_url();?>employee/inventory/get_update_shipment_form/<?php echo $value['order_id']?>" class="btn btn-sm btn-primary" title="Update Shipment" style="margin-bottom: 3px;" <?php if($value['active'] == 0){echo 'disabled=TRUE';}?>>  <i class="fa fa-truck" aria-hidden="true"></i></a>&nbsp;
                                <a href="<?php echo base_url();?>employee/inventory/get_update_receiving_form/<?php echo $value['order_id']?>" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Update Receiving" <?php if($value['is_shipped'] != 1 || $value['active'] == 0){echo 'disabled=TRUE';}?> > <i class="fa fa-shopping-cart" aria-hidden="true"></i></a>&nbsp;
                                <a href="<?php echo base_url();?>employee/inventory/uncancel_brackets_request/<?php echo $value['order_id']?>" class="btn btn-sm btn-primary" style="margin-bottom: 3px;" title="Un-Cancel Request" <?php if($value['active'] == 1){echo 'disabled=TRUE';}?> > <i class="fa fa-undo" aria-hidden="true"></i></a>&nbsp;
                            </td>
                                


                        </tr>
                    <?php } ?>
                    </tbody>
            </table>
            <?php if(!empty($links)){ ?><div class="custom_pagination" style="float:left;margin-top: 20px;margin-bottom: 20px;"> <?php if(isset($links)){echo $links;} ?></div> <?php } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
    
    $("#sf_id").select2();
    $(function() {
        $('input[name="daterange"]').daterangepicker();
    });
    
    $(document).ready(function(){
        $.ajax({
            method:'POST',
            url: "<?php echo base_url();?>employee/vendor/get_service_center_details",
            success:function(response){
                $('#sf_id').val('val', "");
                $('#sf_id').val('Select Service Center').change();
                $('#sf_id').select2().html(response);
            }
        });
    });
    
    $('#filter').click(function(){
       var role = $('#sf_role').val();
       var sf_id = $('#sf_id').val();
       var daterange = $('#daterange').val();
       var start_date = daterange.split("-")[0];
       var end_date = daterange.split("-")[1];
       if(role === '' ||role === null || sf_id === '' || sf_id === undefined || sf_id === null || start_date === '' || end_date === ''){
           alert("Please Select All Field");
       }else{
           $('#loader').show();
           $.ajax({
                method:'POST',
                url: "<?php echo base_url();?>employee/inventory/get_filtered_brackets_list",
                data: {'sf_role':role,'sf_id':sf_id,'start_date':start_date,'end_date':end_date,'filter':'filter'},
                success:function(response){
                    //console.log(response);
                    if(response === 'No Data Found'){
                        var res = "<div class='text-center text-danger'><strong>"+response+"</strong></div>";
                        $('#brackets_list_box').html(res);
                        $('#loader').hide();
                    }else{
                        $('#brackets_list_box').html(response);
                        $('#loader').hide();
                    }
                }
            });
       }
    });
</script>
<?php $this->session->unset_userdata('brackets_update_success');?>
<?php $this->session->unset_userdata('brackets_cancelled_error');?>
