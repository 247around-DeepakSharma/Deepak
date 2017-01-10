<style>
    .ui-jqgrid {font-size:120%;}
    .ui-jqgrid tr.jqgrow td {font-size:120%;}
    ui-grid{ width:100% !important; }
    #delmodlist{margin-left:30% !important;margin-top:15% !important;}
    #editmodlist{margin-left:30% !important;margin-top:2% !important;}
    .ui-widget-header{background: #4CBA90 !important;height:40px;}
    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default{font-size:14px;}
</style>
<div class="container-fluid">
    <div class="container">
        <div class="clear">
            <table id="list"></table><!--Grid table-->
            <div id="pager"></div>  <!--pagination div-->
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#list").jqGrid({
            url: '<?php echo base_url() ?>employee/vendor/get_active_tax_rates_template/', //another controller function for generating data
            mtype: "post", //Ajax request type. It also could be GET
            datatype: "json", //supported formats XML, JSON or Arrray
            colNames: ['tax_code', 'state', 'product_type', 'rate','from_date','to_date','active'], //Grid column headings
            colModel: [
                {name: 'tax_code', index: 'tax_code',edittype: "text", width: 150, align: "left", editable: true, editrules:{required:true}},
                {name: 'state', index: 'state',edittype: "text", width: 350, align: "left", editable: true, editrules:{required:true}},
                {name: 'product_type', index: 'product_type',edittype: "text", width: 200, align: "left", editable: true},
                {name: 'rate', index: 'rate',edittype: "text", width: 200, align: "left", editable: true},
                {name: 'from_date', index: 'from_date',edittype: "text", width: 200, align: "left", editable: true,editoptions: {dataInit: function(element) {$(element).datepicker({dateFormat: 'yy-mm-dd'})}}},
                {name: 'to_date', index: 'to_date',edittype: "text", width: 200, align: "left", editable: true,editoptions: {dataInit: function(element) {$(element).datepicker({dateFormat: 'yy-mm-dd'})}}},
                {name: 'active', index: 'active',edittype: "checkbox", width: 100, align: "center", editable: true}
            ],
            rowNum: 10,
            width: 1150,
            height: "100%",
            //height: 300,
            rowList: [10, 20, 30],
            pager: '#pager',
            sortname: 'id',
            viewrecords: true,
            rownumbers: true,
            gridview: true,
            caption: "TAX RATE TEMPLATE",
            editurl: '<?php echo base_url() ?>employee/vendor/update_tax_rate_template/', //another controller function for add, edit , delete data
        }).navGrid('#pager', {edit: true, add: true, del: true});

    });
</script>
