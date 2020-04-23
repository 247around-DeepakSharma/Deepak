<style>
    .ui-jqgrid {font-size:120%;}
    .ui-jqgrid tr.jqgrow td {font-size:120%;}
    .ui-grid{ width:100% !important; }
    .ui-widget-header{background: #d9edf7 !important;height:40px; color:#31708f;}
    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default{font-size:14px;}
    #editmodlist{width:80%!important;}
    .ui-jqdialog-content input.FormElement{width: 100%;}
    .ui-widget ui-widget-content ui-corner-all ui-jqdialog jqmID2{left: 300px;}
</style>
<div class="container">
    <div class="clear">
        <table id="list"></table><!--Grid table-->
        <div id="pager"></div>  <!--pagination div-->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#list").jqGrid({
            url: '<?php echo base_url() ?>employee/booking/get_non_verified_appliance_template/',
            mtype: "post", 
            datatype: "json", 
            colNames: ['Product Description','Category', 'Capacity','Brand'],
            colModel: [
                {name: 'product_description', index: 'product_description',edittype: "text", width: 500, align: "left", editable: false},
                {name: 'category', index: 'category',edittype: "text", width: 50, align: "left", editable: true},
                {name: 'capacity', index: 'capacity',edittype: "text", width: 50, align: "left", editable: true},
                {name: 'brand', index: 'brand',edittype: "text", width: 50, align: "left", editable: true}
            ],
            rowNum: 10,
            width: 1150,
            height: "100%",
            rowList: [10, 20, 30],
            pager: '#pager',
            sortname: 'id',
            viewrecords: true,
            rownumbers: true,
            gridview: true,
            caption: "Appliance Description TEMPLATE",
            editurl: '<?php echo base_url() ?>employee/booking/update_appliance_description_template/'
        }).navGrid('#pager', 
                {
                    edit: true, 
                    add: false, 
                    del: false
                },
                {
                    afterSubmit:function(){
                        alert("Details Updated Successfully");
                        return [true,"",""]
                    },
                    closeAfterEdit: true
                }
                
            );

    });
</script>
