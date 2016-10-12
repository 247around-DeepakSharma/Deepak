<style>
    .ui-jqgrid {font-size:120%;}
    .ui-jqgrid tr.jqgrow td {font-size:120%;}
    ui-grid{ width:100% !important; }
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
            url: '<?php echo base_url() ?>employee/vendor/get_active_sms_template/', //another controller function for generating data
            mtype: "post", //Ajax request type. It also could be GET
            datatype: "json", //supported formats XML, JSON or Arrray
            colNames: ['Tag', 'Template', 'Comments', 'Active'], //Grid column headings
            colModel: [
                {name: 'tag', index: 'tag',edittype: "text", width: 150, align: "left", editable: true},
                {name: 'template', index: 'template',edittype: "textarea", width: 450, align: "left", editable: true},
                {name: 'comments', index: 'comments',edittype: "textarea", width: 200, align: "left", editable: true},
                {name: 'active', index: 'active',edittype: "checkbox", width: 50, align: "center", editable: true},
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
            caption: "SMS TEMPLATE",
            editurl: '<?php echo base_url() ?>employee/vendor/update_sms_template/', //another controller function for add, edit , delete data
        }).navGrid('#pager', {edit: true, add: true, del: true});

    });
</script>