<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="container-fluid">
    <div class="row" style="margin-top: 40px;">
        <div class="col-md-12">
            <h2>Challan Number - <?php echo $challan_number?></h2>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="success_msg_div" style="display:none;">
                        <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                            <strong><span id="success_msg"></span></strong>
                        </div>
                    </div>
                    <div class="error_msg_div" style="display:none;">
                        <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                            <strong><span id="error_msg"></span></strong>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="">
                        <table class="table table-bordered table-hover table-striped" id="msl_challan_data">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Spare ID</th>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">Part Number</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Rate</th>
                                    <th class="text-center">GST Rate</th>
                                    <th class="text-center">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data as $key =>$row){ ?>
                                <tr style="text-align: center;">
                                    <td><?php echo $key +1; ?></td>
                                    <td><?php echo $row['spare_id']; ?></td>
                                    <td><?php echo $row['booking_id']; ?></td>
                                    <td><?php echo $row['part_number']; ?></td>
                                    <td><?php echo $row['description']; ?></td>
                                    <td><?php echo $row['quantity']; ?></td>
                                    <td><?php echo $row['rate']; ?></td>
                                    <td><?php echo $row['gst_rate']; ?></td>
                                    <td><a href="javascript:void(0)" id="item_<?php echo $row['spare_mapping_id'];?>" onclick="remove_challan_items(event, '<?php echo $row['spare_mapping_id'];?>', '<?php echo $row['spare_id'];?>');" class="btn btn-sm btn-danger">Remove</a></td>
                                    
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        table = $('#msl_challan_data').DataTable({
            processing: true,
            dom: 'lBfrtip',
            "lengthMenu": [[ 25, 50,100, -1], [ 25, 50, 100,"All"]],
            ordering: false,
            pageLength: 100,
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7]
                    },
                    
                    title: '<?php echo $challan_number."_details"?>'
                }
            ],
            select: true
          
        });
      
    });
    function remove_challan_items(e, spare_mapping_id, spare_id){
         
        swal({
            title: "Do You Want To Continue?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            closeOnConfirm: true

        },
        function(isConfirm) {
           if (isConfirm) {
                e = e || window.event;
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    beforeSend: function(){
                        $('body').loadingModal({
                            position: 'auto',
                            text: 'Loading Please Wait...',
                            color: '#fff',
                            opacity: '0.7',
                            backgroundColor: 'rgb(0,0,0)',
                            animation: 'wave'
                    });

                     var btn = document.getElementById('item_'+spare_mapping_id);
                        btn.disabled = true;
                        btn.innerText = 'Please wait...'

                        },
                    url: '<?php echo base_url();?>employee/spare_parts/remove_challan_items/'+spare_mapping_id +"/"+spare_id,
                    data:{},
                    success: function (response) {
                        console.log(response);
                        var data = jQuery.parseJSON(response);
                        if(data.status === true){
                            $('body').loadingModal('destroy');
                            swal("Thanks!", "Booking updated successfully!", "success");
                            alert(data.message);
                            location.reload();
                                
                        } else {

                            $('body').loadingModal('destroy');
                            swal("OOPS!", data.message, "error");
                            alert(data.message);
                            location.reload();
                            return false;
                        }
    
                    }
            
        });
        }
        });
    }
    
</script>