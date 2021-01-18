<html>
    <style>
        body{
            text-shadow: 0.5px 1px 1px;
            font-family: Verdana;
        }

        .borderless td , .borderless th{
            border: none !important;
            padding: 20px !important;
        }    

        .borderless th {
            color : grey;
        }

        .borderless {
            background: #fff;
            padding:20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }
        .table-heading{
            text-align:center;
            font-size:25px;
            color:#32b1b0;
        }

        .page-heading{
            text-align: center;
            font-size:30px;
            margin-top:100px;
            color:#8c8c8c;
        }
    </style>
    <head>
        <!-- Bootstrap -->
        <link href="<?php echo base_url() ?>css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body style="background: #f2f2f2;"> 
        <div class="page-heading"><?php echo $msg; ?></div>        
    </body>  
</html>