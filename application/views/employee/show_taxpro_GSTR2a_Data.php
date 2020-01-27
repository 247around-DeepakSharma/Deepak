<style>
    #GSTR2a_table_filter{
        text-align: right;
    }
    .select2-results__option .wrap:before{
    font-family:fontAwesome;
    color:#999;
    content:"\f096";
    padding-right: 10px;
    }
    .select2-results__option[aria-selected=true] .wrap:before{
    content:"\f14a";
    }
</style>
<script src="<?php echo base_url();?>js/select2_multi_checkbox.js?v=<?=mt_rand()?>"></script>
<style type="text/css">
    #booking_form .form-group label.error {
    margin:4px 0 5px !important;
    width:auto !important;
    }
    #tabs ul{
    margin:0px;
    padding:0px;
    }
    #tabs li{
    list-style: none;
    float: left;
    position: relative;
    top: 0;
    margin: 1px .2em 0 0;
    border-bottom-width: 0;
    padding: 0;
    white-space: nowrap;
    border: 1px solid #2c9d9c;
    background: #d9edf7 url(images/ui-bg_glass_75_e6e6e6_1x400.png) 50% 50% repeat-x;
    font-weight: normal;
    color: #555555;
    border-top-right-radius: 4px;
    border-top-left-radius: 4px;
    border-bottom: 0px;
    }
    #tabs a{
    float: left;
    padding: .5em 1em;
    text-decoration: none;
    }
    .col-md-12 {
    padding: 10px;
    }
    .select2-container{
    width:100% !important;
    }
    .vertical-align{
    height:100%;
    padding-top: 1%
    }
    #warehouse_datatable_filter{
    text-align: right;
    }
    .form-horizontal .control-label{
    text-align: left;
    }
    .custom-sf-list{
    padding: 10px;        
    background-color: #fff;        
    } 
    .hide{
        display: none;
    }
    .radio-inline input[type=radio] {
    position: absolute;   
    margin-left: 10px;
}
</style>
   <div id="page-wrapper">
    <div class="row">
        <div class="clear"></div>
        <div class="panel panel-info">
            <div class="panel-heading" style="padding: 6px 7px 0px 10px;">
                <h3>GSTR2A Report<small style="float:right">Last Updated On : <?php echo $last_updated_data; ?></small></h3>
                <div class="clear"></div>
            </div>
            <div id="tabs" style="border:0px solid #fff;float:left;">
                <div class="col-md-12" style="">
                    <ul>
                        <li style="background:#fff"><a id="1" href="#tabs-1" onclick="load_form(this.id, 'vendor')"><span class="panel-title">Vendor</span></a></li>
                        <li><a id="2" href="#tabs-1" onclick="load_form(this.id, 'partner')"><span class="panel-title">Partner</span></a></li>
                        <li><a id="3" href="#tabs-1"  onclick="load_form(this.id, 'other')"><span class="panel-title">Other</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
            <div class="panel-body">
                <div class="col-md-12 ">
                    <div class="row">
                        <div class="form-inline">
                            <div class="form-group col-md-3">
                                <select class="form-control" id="state" name="state">
                                    <option value="1">Delhi</option>
                                    <option value="2">Uttar Pradesh</option>
                                </select>
                            </div>
                            <button class="btn btn-success col-md-2" id="get_gst_data">Submit</button>
                        </div>
                    </div>
                    <br>
                    <div id="container_1" class="form_container">
                        <input type="hidden" id="entity_type" value="vendor">
                        <table id="GSTR2a_table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <th>S No.</th>
                                <th>Invoice No.</th>
                                <th>Vendor Name</th>
                                <th>GST No.</th>
                                <th>Invoice Date</th>
                                <th>IGST Amt</th>
                                <th>CGST Amt</th>
                                <th>SGST Amt</th>
                                <th>Total Tax</th>
                                <th>Taxable Amt</th>
                                <th>Invoice Amt</th>
                                <th>Related Invoices</th>
                                <th>Generate CN</th>
                                <th>Reject Remark</th>
                            </thead>
                            <tbody>

                            </tbody>  
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
       </div>
    </div>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reject Remark <button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                </div>
                <div class="modal-body">
                       <div class="row">
                           <input type="hidden" id="gstr2a_table_id">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="reject_remarks">Remark *</label>
                                    <textarea class="form-control" id="reject_remarks" name="reject_remarks" placeholder="Enter Reject Remark...."></textarea>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="reject_submit()">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="cn_remarks_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Credit Note Remark <button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                </div>
                <div class="modal-body">
                       <div class="row">
                            <input type="hidden" id="cn_gstr2a_table_id">
                            <input type="hidden" id="cn_invoice_btn">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="reject_remarks">Remark *</label>
                                    <textarea class="form-control" id="cn_remarks" name="cn_remarks" placeholder="Enter Credit Note Remark...."></textarea>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="invoice_checksum" name="invoice_checksum">
                    <input type="hidden" id="taxpro_table_id" name="taxpro_table_id">
                    <input type="hidden" id="select_box_id" name="select_box_id">
                    <button type="button" class="btn btn-success" onclick="update_cn_remark()">Submit</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<script>
    $("#GSTR2a_table").bind("DOMSubtreeModified", function() { 
        $('#GSTR2a_table tr a.duplicate_row').each(function(){ $(this).closest('tr').css("background-color", "#90EE90");  });
        $('#GSTR2a_table tr a.inv_not_found').each(function(){ $(this).closest('tr').css("background-color", "#ffcfc4fa");  });
    });
    var GSTR2a_datatable;
    $(document).ready(function () {
        //datatables
        GSTR2a_datatable = $('#GSTR2a_table').DataTable({
            "processing": true, 
            "serverSide": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [],
            "pageLength": 25,
            dom: 'lBfrtip',
             buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'GSTR2A_Report',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,9,10],
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
                "url": "<?php echo base_url(); ?>employee/accounting/get_gst2ra_mapped_data",
                "type": "POST",
                data: function(d){
                    d.entity = $("#entity_type").val();
                    d.state = $("#state").val();
                }
            },
            "columnDefs": [
                {
                    "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12,13], 
                    "orderable": false, 
                }
            ],
            fnInitComplete: function (oSettings, response) {
              $('#GSTR2a_table tr a.duplicate_row').each(function(){ $(this).closest('tr').css("background-color", "#90EE90");  });
            },
            "drawCallback": function (oSettings, response) {
                $(".invoice_select").select2();
        
                $('.invoice_select').select2MultiCheckboxes({
                    templateSelection: function(selected, total) {
                      return "Selected " + selected.length + " of " + total;
                    }
                });
            } 
        });
    });
    
    
    function load_form(tab_id, entity){
        $("#entity_type").val(entity);
        var total_div  = 3;
        for(var i =1;i<=total_div;i++){
            if(i != tab_id){
                document.getElementById(i).style.background='#d9edf7';
            }
            else{
                document.getElementById(i).style.background='#fff';
            }
        }
        
        if($("#entity_type").val() === 'partner'){ 
            GSTR2a_datatable.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13]).visible(true);
            GSTR2a_datatable.columns( [12] ).visible( false );
            $(GSTR2a_datatable.column(2).header()).text('Partner Name');
        }
        else if($("#entity_type").val() === 'other'){ 
           GSTR2a_datatable.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13]).visible(true);
           GSTR2a_datatable.columns( [11,12] ).visible( false );
           $(GSTR2a_datatable.column(2).header()).text('Company Name');
        }
        else{
            GSTR2a_datatable.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13]).visible(true);
            $(GSTR2a_datatable.column(2).header()).text('Vendor Name');
        }
        GSTR2a_datatable.ajax.reload();
    }
    
    function reject(id){
          $("#gstr2a_table_id").val(id);
    }
    
    function reject_submit(){
        var id =  $("#gstr2a_table_id").val();
        var remarks = $("#reject_remarks").val();
        if(remarks){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/accounting/reject_taxpro_gstr2a',
                data: {id:id, remarks:remarks},
                success: function (data) {
                    console.log(data);
                    if(data==true){
                        GSTR2a_datatable.ajax.reload();
                        $('#myModal').modal('hide');
                    }
                    else{
                        alert("Error in rejecting data.");
                    }
                }
            });
        }
        else{
            alert("Please Enter Reject Remarks...");
        }
    }
    
    function generate_credit_note(id, btn){
        $("#taxpro_table_id").val($(btn).attr("data-id"));
        $("#select_box_id").val(id);
        $("#invoice_checksum").val($(btn).attr("data-checksum"));
        $("#cn_remarks").val("");
        $('#cn_remarks_modal').modal('toggle');
    }
    
    function update_cn_remark(){
        var cn_remark = $("#cn_remarks").val();
        var checksum = $("#invoice_checksum").val();
        var select_id = $("#select_box_id").val();
        var id = $("#taxpro_table_id").val();
        if(cn_remark){
            var parent_inv = [];
            var invoice_id = $("#selected_invoice_"+select_id).val();
            
            $("#selected_invoice_"+select_id).find(':selected').each(function(){
                parent_inv.push($(this).attr('data-parent-inv'));
            });
            
            if(invoice_id){
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/accounting/update_cn_by_taxpro_gstr2a',
                    data: {invoice_id:invoice_id, checksum:checksum, id:id, parent_inv:parent_inv, cn_remark:cn_remark},
                    success: function (data) {
                        if(data==true){
                            for (var index in invoice_id){ 
                                window.open("<?php echo base_url(); ?>employee/invoice/generate_gst_creditnote/"+invoice_id[index], '_blank');
                            } 
                            GSTR2a_datatable.ajax.reload();
                        }
                        else{
                            alert("Error in generating credit note.");
                        }
                        $('#cn_remarks_modal').modal('toggle');
                    }
                });
            }
            else{
                alert("Please Select Invoice");
            }
        }
        else{
            alert("Please Enter Remark");
        }
    }
    
    function check_tax_amount(tax_amount, select){
        var inv_tax = 0;
        //var considarable_tax1 = tax_amount-10;
        //var considarable_tax2 = tax_amount+10;
        
        $(select).find(':selected').each(function(){
            inv_tax = Number(inv_tax) + Number($(this).attr('data-tax'));
        });
        
        //console.log('inv_tax '+inv_tax);
        //console.log('considarable_tax1 '+considarable_tax1);
        //console.log('considarable_tax2 '+considarable_tax2);
        /*
        if(inv_tax >= considarable_tax1 && inv_tax <= considarable_tax2){ 
            $(select).closest("tr").find("button").attr("disabled", false);
        }
        else{ 
            $(select).closest("tr").find("button").attr("disabled", true);
        }
        */
        if(inv_tax > 0){
            $(select).closest("tr").find("button").attr("disabled", false);
        }
        else{
            $(select).closest("tr").find("button").attr("disabled", true);
        }
    }
    
    
    //function to get gst data on basis of filter
     $('#get_gst_data').on('click',function(){
        var state_id = $('#state').val();
        if(state_id && state_id > 0){
        GSTR2a_datatable.ajax.reload(null, false);
        }else{
            alert("Please Select State");
        }
    });
 
</script>