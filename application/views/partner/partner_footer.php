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
        var regex = new RegExp("^[a-zA-Z0-9- ]+$");
        if(regex.test(searched_text)){
            if(searched_text.length >= 9){
                return true;
            }else{
                alert("Enter Atleast 8 Character");
                return false;
            }
        }else{
            alert("Special character not allowed");
            return false;
        }

    }
</script>
<script type="text/javascript" src="https://blackmelon.atlassian.net/s/d41d8cd98f00b204e9800998ecf8427e-T/v7ee31/b/4/a44af77267a987a660377e5c46e0fb64/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?locale=en-US&collectorId=502b806b">
</script>
</html>