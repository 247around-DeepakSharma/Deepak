<!-- footer content -->
<footer>
    <div class="pull-right">
        <a href="#">247AROUND</a>
    </div>
    <div class="clearfix"></div>
</footer>
<!-- /footer content -->

<!-- JS -->
<script src="<?php echo base_url() ?>js/bootstrap.min.js"></script>
<!-- bootstrap-progressbar -->
<script src="<?php echo base_url() ?>js/bootstrap-progressbar.min.js"></script>  
<!-- Custom Theme Scripts -->
<script src="<?php echo base_url() ?>js/dashboard_custom.js"></script>
 <!-- iCheck -->
<script src="<?php echo base_url() ?>assest/iCheck/icheck.min.js"></script>
<!-- Datatable JS-->
<script type="text/javascript" src="<?php echo base_url() ?>assest/DataTables/datatables.min.js"></script>
<script type="text/javascript">
    
    function submit_button() {
        var phone = $("#phone_number").val();

        if (phone.length !== 10) {
            return false;

        }
        intRegex = /^[6-9]{1}[0-9]{9}$/;
        if (intRegex.test(phone))
        {
            return true;
        } else {
            return false;
        }
    }
    
    function checkStringLength() {
        var searched_text = $("#searched_text").val();
        if (searched_text.length < 9) {
            alert("Enter Atleast 8 Character For Booking ID");
            return false;
        }

    }
</script>
</html>