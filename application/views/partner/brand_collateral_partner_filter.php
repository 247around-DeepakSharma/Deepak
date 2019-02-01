<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<style>.dataTables_filter{display: none;}</style>
<div class="right_col" role="main">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="container" >
                <form method="POST" action="#">
                    
                    <div class="form-group" id="partner_holder">
                        <select class="form-control" name="partner" id="partner">
                            <option value="option_holder">Select Partner</option>
                            <?php
                                foreach($partnerArray as $partnerID=>$partnerName){
                                    echo ' <option value="'.$partnerID.'">'.$partnerName.'</option>';
                                }
                                ?>
                        </select>
                    </div>
                                   
                    <div class="form-group">
                        <button type="button" class="btn btn-small btn-success" id="search" onclick="validform()">Search</button>
                    </div>
                </form>
            </div>
            <div class="x_panel" style="height: auto;">
                <table id="brand_collateral_partner" class="table table-striped table-bordered">
                    <thead>
                       <tr>
                            <th>S.N</th>
                            <th>Document Type</th>
                            <th>Appliance</th>
                            <th>Brand</th>
                            <th>Request Type</th>
                            <th>File</th>
                            <th>Description</th>
                            <!--<th>Delete <button onclick="delete_collatrals()"><i class="fa fa-trash" aria-hidden="true"></i></button></th>-->
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function validform(){
       var partner = $("#partner option:selected").val();
       if(partner!=='option_holder')
        {
            ad_table.ajax.reload( function ( json ) {} );
         }
        else
        {
           alert("Please Select Partner ");
           return false;;
        }
    }
     function getMultipleSelectedCheckbox(fieldName){
        var checkboxes = document.getElementsByName(fieldName);
        var vals = "";
        length = checkboxes.length;
        for (var i=0;i<length;i++) 
        {
            if (checkboxes[i].checked) 
            {
                vals += "'"+checkboxes[i].value+"',";
            }
        }
        return vals;
    }
    function delete_collatrals(){
        collatrelsID = getMultipleSelectedCheckbox("coll_id[]");
        if(collatrelsID){
            $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/deactivate_brand_collateral',
            data: {collateral_id:collatrelsID},
            success: function (data) {
                alert(data);
            }
        });
        }
    }
    var ad_table;
        ad_table = $('#brand_collateral_partner').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "deferLoading": 0,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": baseUrl+"/employee/partner/brandCollateralPartner",
                "type": "POST",
                "data": function(d){
                  d.partner_id = $("#partner option:selected").val();
               }
            },
            "columnDefs": [
                {
                    "targets": [0,5,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
        });
</script>