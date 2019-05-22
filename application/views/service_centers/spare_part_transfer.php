<style>
    #inventory_master_list_filter{
        text-align: right;
    }

    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }

    #inventory_master_list_processing{
        position: absolute;
        z-index: 999999;
        width: 100%;
        background: rgba(0,0,0,0.5);
        height: 100%;
        top: 10px;
    }

    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div id="page-wrapper">
    <div class="row" style="border: 1px solid #e6e6e6; padding: 20px;" >
        <div class="title">
            <div class="row">
                <div class="col-md-12">
                    <h3>Search Bookings for Spare Part Transfer From One Booking To Another </h3><hr>
                </div>

            </div>
        </div>
        <br><br>
        <?php if ($this->session->flashdata('error_msg') != '') { ?>
            <div class="error_msg_div" style=" ">
                <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Error ! <?php echo $this->session->flashdata('error_msg'); ?></</strong>
                </div>
            </div><br > 
        <?php }
        ?>

                
                <?php if ($this->session->flashdata('success') != '') { ?>
            <div class="error_msg_div" style=" ">
                <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Success ! <?php echo $this->session->flashdata('success'); ?></</strong>
                </div>
            </div><br>  
        <?php }
        ?>


            <div   >

            <form action="<?php echo base_url(); ?>service_center/booking_spare_list" method="POST" >
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="frombooking">From Booking*</label>
                            <div class="col-md-7  ">
                                <input type="text" class="form-control" required=""  value="<?php
                                if (!empty($frombooking)) {
                                    echo $frombooking;
                                }
                                ?>"  id="frombooking" name="frombooking">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="control-label col-md-4" for="tobooking">To Booking*</label> 
                            <div class="col-md-7  ">                                        
                                <input type="text" class="form-control" required="" id="tobooking"   value="<?php
                                if (!empty($tobooking)) {
                                    echo $tobooking;
                                }
                                ?>"   name="tobooking">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success"  >Search</button>
                    </div>
                </div>   

            </form>

        </div>
            <br>   <br>   <br>  <br> <hr>
        <?php if (!empty($frombooking) && !empty($tobooking)) { ?>
            <form action="<?php echo base_url(); ?>service_center/do_spare_transfer" method="POST" >

                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="frombooking"  value="<?php echo $frombooking; ?>"  />
                        <h3>For booking id - <?php echo $frombooking; ?> </h3>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Part Requested  </th>
                                    <th>Model Number</th>  
                                    <th>Select</th>  
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($from_booking as $from) { ?>
                                    <tr>
                                        <td  ><?php echo $from['parts_requested']; ?> ( <?php echo $from['requested_inventory_id']; ?> )</td>
                                        <td><?php echo $from['model_number']; ?></td>  
                                        <td><input data-value="<?php echo $from['requested_inventory_id']; ?>" class="frominventory" required="required" type="radio" value="<?php echo $from['id']; ?>" name="frominventry" />
                                           
                                        </td> 
                                    </tr>
                                <?php }
                                ?>

                            </tbody>
                        </table>
                    </div>


                    <div class="col-md-6">
                        <input type="hidden" name="tobooking"  value="<?php echo $tobooking; ?>"  />
                        <h3>For booking id - <?php echo $frombooking; ?> </h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Part Requested ( <?php echo $tobooking; ?> )</th>
                                    <th>Model Number</th>  
                                    <th>Select</th>  
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($to_booking as $to) { ?>
                                    <tr>
                                        <td><?php echo $to['parts_requested']; ?>( <?php echo $to['requested_inventory_id']; ?> )</td>
                                        <td><?php echo $to['model_number']; ?></td>  
                                        <td><td><input  data-value="<?php echo $to['requested_inventory_id']; ?>" class="toinventory"  required="required"  type="radio" value="<?php echo $to['id']; ?>" name="toinventory" />
                                        
                                        </td>
                                        
                                        </td> 
                                    </tr>
                                <?php }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <input id="frominventiryid" type="hidden" value="" name="inventoryidfrom" />
                    <input id="toinventiryid" type="hidden" value="" name="inventoryidto" />

                </div>  

                <div><center><button class="btn btn-success" id="submitbutton"  type="submit">Submit</button></center></div>

            </form>         

        <?php } ?>       


    </div>

</div>
<style>
    table{
        border: 1px solid #897777;
        border-collapse: initial  !important;
    }  
</style>
<script>
    
$(".frominventory").click(function(){
  
  var froominventory = $(this).attr("data-value");
  $("#frominventiryid").val(froominventory);
 
});  

$(".toinventory").click(function(){
  
  var toinventory = $(this).attr("data-value");
  $("#toinventiryid").val(toinventory);
 
    
});  
    

//    $(".toinventory").click(function () {
//        var from='';
//        var to ='';
//        var to = $(this).attr("data-value");
//        var from = $("input[name='frominventry']:checked").attr("data-value");
//        console.log(to);
//        console.log(from);
//        if ($("input[name='frominventry']:checked").is(":checked")) {
//            if (to == from) {
//                $("#submitbutton").attr("type", "submit");
//            } else {
//                $("#submitbutton").attr("type", "button");
//                if ($("input[name='frominventry']:checked").is(":checked")) {
//                    alert("Selected inventory not same. Please select same inventory");
//                    $(this).attr('checked', false);
//
//                } else {
//                    alert("Please select both inventory");
//                }
//            }
//
//        }
//    });



//    $(".frominventory").click(function () {
//        var from='';
//        var to ='';
//        var from = $(this).val();
//        var to = $("input[name='toinventry']:checked").attr("data-value");
//        if ($("input[name='toinventry']:checked").is(":checked")) {
//            if (to == from) {
//                $("#submitbutton").attr("type", "submit");
//            } else {
//                $("#submitbutton").attr("type", "button");
//                if ($("input[name='toinventry']:checked").is(":checked")) {
//                    alert("Selected inventory not same. Please select same inventory");
//                    $(this).attr('checked', false);
//                } else {
//                    alert("Please select both inventory");
//                }
//            }
//        }
//    });

</script>