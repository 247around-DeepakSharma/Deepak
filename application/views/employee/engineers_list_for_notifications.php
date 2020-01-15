
<div  id="page-wrapper">
    <div class="row">
      <div >
       
        <h2>Engineer Details</h2>
        <br>

        <div style="">
            <button style="margin-left:20px" class="btn btn-primary pull-right"  data-toggle="modal" data-target="#myModal" id="send_notifications">Send Notification</button>
        </div>
        <div style="">
            <button style="margin-left:20px" class="btn btn-primary pull-right" id="show_filter">Show</button>
        </div>
        <div style="margin-left: 10px;" class="pull-right">
             <select class="form-control" id="service_centers_id">
                 
             </select>
        </div>
         <?php if($this->session->userdata('update_success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('update_success') . '</strong>
                    </div>';
                    }
        ?>
        <table  class="table table-striped table-bordered" id="engineer_datatable">
          <thead>
            <tr>
            <th>No.</th>
            <th>Name</th>
            <th>State</th>
            <th>City</th>
            <th>Engineer Name</th>
            <th>Appliances</th>
            <th>Mobile</th>
            <th>Alternate Mobile</th>
           <th>Notify</th>

<!--                <th>Delete</th>-->
            </tr>
            </thead>
           <tbody></tbody>

          
       
        </table>


        
      </div>
    </div>
</div> 



<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>



<script>


    function get_service_centers_list(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url();  ?>employee/service_centers/get_service_centers_list',
            data:{ is_micro_wh : 1 },
            success: function (response) {
                $("#service_centers_id").append(response);
               // $("#select2-service_centers_id-result-zsev-Select Service Centres") 
                $("#service_centers_id").select2();               
            }
        });
    }

    get_service_centers_list();

    var engineer_datatable = "";

    engineer_datatable = $('#engineer_datatable').DataTable({
        processing: true, //Feature control the processing indicator.
        serverSide: true, //Feature control DataTables' server-side processing mode.
        order: [], //Initial no order.
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        // Load data for the table's content from an Ajax source
        dom: 'lBfrtip',
        buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'engineers',
                    exportOptions: {
                       columns: [1,2,3,4,5,6],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
        ajax: {
            url: "<?php echo base_url(); ?>employee/engineer/get_engineer_details_for_notification",
            type: "POST",
            data: {service_center_id:$("#service_center_id").val()}
        },
        //Set column definition initialisation properties.
        columnDefs: [
            {
                "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8], //first column / numbering column
                "orderable": false //set not orderable
            }
        ]
    });
    $("#engineer_datatable_filter").addClass("pull-right");


$("#show_filter").click(function(){

//alert($("#service_centers_id").val());   
engineer_datatable.ajax.reload(null, false);

});


$("#send_notifications").click(function(){


if($(".send_notification:checked[name='token']").length>0){

    $( ".send_notification:checked[name='token']" )
  .map(function() {

   console.log($(this).data("check_firebase"));
   // return this.id;
  });


}else{

    alert("Please select at least one engineer");

}





});



 
</script>