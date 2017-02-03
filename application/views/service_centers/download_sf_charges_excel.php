<style src="<?php echo base_url() ?>css/jquery.dynatable.css"></style> 
<script src="<?php echo base_url() ?>js/jquery.dynatable.js"></script>

<div id="page-wrapper" >
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading"><center style="font-size:130%;">SERVICE CENTER PAY OUT (CALL CHARGES)</center></div>
    </div>
    <table class="table table-condensed table-bordered" id="sc_charges_table">
        <thead>
            <tr>
                <th class="jumbotron">S.N.</th>
                <th class="jumbotron"style="text-align: center" >SC CODE</th>
                <th class="jumbotron"style="text-align: center" >PRODUCT</th>
                <th class="jumbotron"style="text-align: center" >CATEGORY</th>
                <th class="jumbotron"style="text-align: center" >CAPACITY</th>
                <th class="jumbotron"style="text-align: center" >SERVICE CATEGORY</th>
                <th class="jumbotron"style="text-align: center" >VENDOR BASIC CHARGE</th>
                <th class="jumbotron"style="text-align: center" >VENDOR TAX BASIC CHARGE</th>
                <th class="jumbotron"style="text-align: center" >VENDOR TOTAL</th>
                <th class="jumbotron"style="text-align: center" >CUSTOMER NET PAYABLE</th>
                <th class="jumbotron"style="text-align: center" >PROOF OF DELIVERY</th>

            </tr>
        </thead>
        <tbody>

            <?php foreach ($final_array as $key => $value) { ?>		
                <tr>
                    <td ><?php echo ($key + 1) . '.' ?></td>
                    <td style="text-align: center;"><?php echo $value['sc_code'] ?></td>
                    <td style="text-align: center;"><?php echo $value['product'] ?></td>
                    <td style="text-align: center;"><?php echo $value['category'] ?></td>
                    <td style="text-align: center;"><?php echo $value['capacity'] ?></td>
                    <td style="text-align: center;"><?php echo $value['service_category'] ?></td>
                    <td style="text-align: center;"><?php echo $value['vendor_basic_charges'] ?></td>
                    <td style="text-align: center;"><?php echo $value['vendor_tax_basic_charges'] ?></td>
                    <td style="text-align: center;"><?php echo $value['vendor_total'] ?></td>
                    <td style="text-align: center;"><?php echo $value['customer_net_payable'] ?></td>
                    <td style="text-align: center;"><?php echo $value['pod'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var dynatable = $('#sc_charges_table').dynatable();
        $('.dynatable-per-page-select').after("<span style='float:left;margin-top:-57px !important;margin-left:70px;'>Records Per Page</span>")
    });
</script>

<style type="text/css">

    .dynatable-search{float:right;margin-bottom:10px}
    .dynatable-pagination-links{float:right}
    .dynatable-record-count{display:block;padding:5px 0}
    .dynatable-pagination-links li,.dynatable-pagination-links span{display:inline-block}
    .dynatable-page-break,.dynatable-page-link{display:block;padding:5px 7px}
    .dynatable-page-link{cursor:pointer}
    .dynatable-active-page,.dynatable-disabled-page{cursor:text}
    .dynatable-active-page:hover,.dynatable-disabled-page:hover{text-decoration:none}
    .dynatable-active-page{background:#006a72;border-radius:5px;color:#fff}
    .dynatable-active-page:hover{color:#fff}
    .dynatable-disabled-page,.dynatable-disabled-page:hover{background:0 0;color:#999}

    .dynatable-search {
        float: right;
        margin-bottom: 10px;
        margin-top: -57px;
        margin-right: 10px;
    }
    .dynatable-per-page-label,.dynatable-per-page-select{
        float: left;
        margin-bottom: 10px;
        margin-top: -57px;
        margin-left: 15px;
    }
</style>