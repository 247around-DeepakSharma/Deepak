<style>
    .ui-jqgrid {font-size:120%;}
    .ui-jqgrid tr.jqgrow td {font-size:120%;}
    ui-grid{ width:100% !important; }
    #delmodlist{margin-left:30% !important;margin-top:15% !important;}
    #editmodlist{margin-left:30% !important;margin-top:2% !important;}
    .ui-widget-header{background: #d9edf7 !important;height:40px; color:#31708f;}
    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default{font-size:14px;}
    #editmodlist{width:450px!important;}
    #FrmGrid_list{width:450px;}
    .EditTable td textarea {width: 300px;height: auto;}
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
            url: '<?php echo base_url() ?>employee/vendor/get_vandor_escalation_policy_rates_template/', //another controller function for generating data
            mtype: "post", //Ajax request type. It also could be GET
            datatype: "json", //supported formats XML, JSON or Arrray
            colNames: ['Escalation Reason', 'Entity', 'Process Type','Sms to Owner','Sms to POC','Sms Body', 'Active'], //Grid column headings
            colModel: [
                {name: 'escalation_reason', index: 'escalation_reason',edittype: "textarea", width: 450, align: "left", editable: true, editrules:{required:true}},
                {name: 'entity', index: 'entity',edittype: "text", width: 250, align: "left", editable: true, editrules:{required:true}},
                {name: 'process_type', index: 'process_type',edittype: "text", width: 200, align: "left", editable: true},
                {name: 'sms_to_owner', index: 'sms_to_owner',edittype: "checkbox", width: 200, align: "left", editable: true,editrules:{required:true}},
                {name: 'sms_to_poc', index: 'sms_to_poc',edittype: "checkbox", width: 200, align: "left", editable: true,editrules:{required:true}},
                {name: 'sms_body', index: 'sms_body',edittype: "textarea", width: 400, align: "left", editable: true},
                {name: 'active', index: 'active',edittype: "checkbox", width: 200, align: "center", editable: true,editrules:{required:true}},
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
            caption: "Vendor Escalation Policy",
            editurl: '<?php echo base_url() ?>employee/vendor/update_vandor_escalation_policy_template/', //another controller function for add, edit , delete data
        }).navGrid('#pager', {edit: true, add: true, del: true});

    });
</script>
