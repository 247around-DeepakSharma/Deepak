<div class = "container"> 
    <div class="panel-group">
        <div class="panel ">
            <div class=" ">
                 <h1>Channel List </h1>  
                <a class="btn btn-primary pull-right btn-md" href ="<?php echo base_url();?>employee/partner/add_channel">Add Channel</a>
            </div>
            <div class="panel-body">
                <table class = "table table-condensed table-bordered table-striped table-responsive" id="channellist">
                    <thead>
                     
                                         <tr>
                        <th>S No</th>
                        <th>Partner Name</th>
                        <th>Channel Name</th>
                        
                        <th>Create Date</th>
                        <th>Action</th>
                        
                    </tr>   

                    </thead>

                <?php
                if (!empty($fetch_data)) {
                    foreach ($fetch_data as $key => $row) {
                        ?>
                        <tr> 
                            <td><?php echo ($key +1) ?></td>
                            <td><?php if($row['public_name'] == null){ echo 'All'; }else{ echo $row['public_name']; }?>
                            <td><?php echo $row['channel_name']; ?></td>
                            <td><?php echo date('d-m-y', strtotime($row['create_date'])); ?></td>
                            <td> <a class="btn btn-primary btn-sm" href ="<?php echo base_url();?>employee/partner/update_channel/<?php echo $row['id'];?>">Update</a></td>
                        </tr>

    <?php }
} else { ?>
                    <tr>
                        <td>"no data found"</td>
                    </tr>
                    <?php
                }
                ?>
                    </table> 
            </div>
        </div>
    </div> 
</div>






                 
</body>



<script>
    


$('#channellist').DataTable({
"processing": true, 
"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
"serverSide": false,  
 "dom": 'lBfrtip',
                "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',

                    title: 'Channel List', 
                    exportOptions: { 
                       columns: [0,1,2],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            select: {
                style: 'multi'
            },
            
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,

            "order": [], 
            "pageLength": 25,
            "ordering": false,
                
            "deferRender": true   


});




</script>

<style type="text/css">
    
    .dataTables_length {
    width: 12% !important;
}

.dataTables_filter{

    float: right !important;
}


         @media (min-width: 1200px){
.container {
    width: 100% !important;
}}
</style>
</html>

