<table id></table>
<script>
    $.getJSON("<?php echo base_url(); ?>sample.json", function (data) {
    $.each(data, function (index, value) {
       console.log(value);
        
    });
});
</script>