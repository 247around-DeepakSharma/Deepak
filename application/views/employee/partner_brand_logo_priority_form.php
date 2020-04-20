<div id="page-wrapper" >
    <div class="container-fluid" >
        <div class="panel panel-info">
            <div class="panel-heading">
                <h5>Set Partner Brand Logo Priority
                    <button type="button" class="btn btn-primary" onclick="save_priority()" style="float: right; margin-top: -10px;">Save Priority</button>
                </h5>
            </div>
            <div class="panel-body">
                <div class="alert alert-success alert-dismissible" role="alert" id="success_div" style="display: none">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong id="sucess_msg"></strong>
                </div>
                <div class="alert alert-danger alert-dismissible" role="alert" id="error_div" style="display: none">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong id="error_msg"></strong>
                </div>
                <table class="table table-striped table-bordered" id="priority_table">
                    <thead>
                        <tr>
                            <th>Partner Name</th>
                            <th>Brand Logo</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($data)){
                        foreach ($data as $key=>$value) { 
                        ?>
                        <tr>
                            <td class="partner_logo_id" style="display: none"><?php echo $value['id']; ?></td>
                            <td><?php echo $value['public_name']; ?></td>
                            <td><img style="width: 120px; height: 80px;" src="<?php echo "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$value['partner_logo'];?>"></td>
                        </tr>
                        <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    var set_priority = false;
    $(document).ready(function(){
        $( "#priority_table tbody" ).sortable({
            update: function( ) {
               set_priority = true;
            }
        });
    });
    
    function save_priority(){
        if(set_priority){
            var priority_array = [];
            $("#priority_table tbody tr").each(function(index){
                var priority = index + 1;
                var data = {
                   partner_brand_logo_id : $(this).find("td:eq(0)").text(),
                   priority : priority
                };
                priority_array.push(data);
            });
            
            $.ajax({
                method : "post",
                url : "<?php echo base_url(); ?>employee/partner/save_partner_logo_priority",
                data : {priority_array : priority_array},
                success : function(response){
                    response = JSON.parse(response);
                    if(response.status){
                       $("#success_div").css("display", "block");
                       $("#error_div").css("display", "none"); 
                       $("#sucess_msg").text(response.message);
                    }
                    else{
                       $("#error_div").css("display", "block"); 
                       $("#success_div").css("display", "none");
                       $("#error_msg").text(response.message);
                    }
                    set_priority = false;
                }
            });
        }
        else{
            alert("No changes made");
        }
    }
</script>
