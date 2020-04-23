<style>
    #dealer_list_filter{
        float: right;
    }
</style>
<div  id="page-wrapper">
    <div class="row">
        <div class="row">
            <h1 class="col-md-6 col-sm-12 col-xs-12">Dealers</h1>
        
        <?php if($this->session->userdata('user_group') != 'closure'){?>
            <div class="col-md-6 col-sm-12 col-xs-12" style="margin-top: 20px;margin-bottom: 10px;">
            <a href="<?php echo base_url();?>employee/dealers/add_dealers_form"><input class="btn btn-primary pull-right" type="Button" value="Add Dealer"></a>
        </div>
        <?php }?>
        </div>
        <?php
        if ($this->session->flashdata('success_msg')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('success_msg') . '</strong>
                    </div>';
        }
        if ($this->session->flashdata('error_msg')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('error_msg') . '</strong>
                    </div>';
        }
        ?>
        <hr>
        <div class="row">
            <div class="dealer_listing container-fluid">
                <table id="dealer_list" class="table table-bordered table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Dealer Name</th>
                            <th>Dealer Phone Number</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Partner-Appliance-Brands</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        
</div>
<?php if ($this->session->flashdata('success_msg')) {$this->session->unset_userdata('success_msg');} ?>
<?php if ($this->session->flashdata('error_msg')) {$this->session->unset_userdata('error_msg');} ?>
<script type="text/javascript">
    var dealer_id = '<?php echo $dealer_id;?>';
    $(document).ready(function () {
         
        //datatables
        $('#dealer_list').DataTable({
            "processing": true, 
            "serverSide": true,
            "order": [],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, 500], [10, 25, 50, 100, 500]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'bank_transactions',
                    exportOptions: {
                       columns: [0,1,2,3,4,5,6],//,7,8,9,10
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/dealers/get_dealers",
                "type": "POST",
                "data": {"dealer_id": dealer_id}
                
            },
            "columnDefs": [
                {
                    "targets": [0,3,4,5,6], 
                    "orderable": false 
                }
            ]
            
        });
    });
    
    
</script>