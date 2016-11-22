<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>A PHP Error was encountered</h4>

<p>Severity: <?php echo $severity; ?></p>
<p>Message:  <?php echo $message; ?></p>
<p>Filename: <?php echo $filepath; ?></p>
<p>Line Number: <?php echo $line; ?></p>

</div>

<?php 

    $file = fopen(FCPATH."/application/logs/error_" . date('Y-m-d') . ".txt", "a+") or die("Unable to open file!");
    $res = 0;
    system(" chmod 777 ".FCPATH."/application/logs/error_" . date('Y-m-d') . ".txt", $res);
    fwrite($file, "\n A PHP Error was encountered: ".date('Y-m-d H:i:s')."\n");
    fwrite($file, "Severity: " . print_r($severity, TRUE)."\n");
    fwrite($file, "Message: " . print_r($message, TRUE)."\n");
    fwrite($file, "Filename: " . print_r($filepath, TRUE)."\n");
    fwrite($file, "Line Number: " . print_r($line, TRUE)."\n");
    
    
    fclose($file);

?>