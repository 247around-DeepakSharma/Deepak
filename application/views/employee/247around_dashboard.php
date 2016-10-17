<style type="text/css">
    #noty_topCenter_layout_container{margin-top:40px !important;}
</style>
<div class="container-fluid">
    <div class="container">
        <h2>247around Dashboard</h2><br>

        <table class="table">
            <thead>
                <tr class="info">
                    <th>Description</th>
                    <th>Count</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($query as $key => $value) { ?>
                    <tr class="success">
                        <td>
                            <?php echo $value['description'] ?>
                        </td>
                        <td><?php echo $data[$key][0]['count'] ?> </td>

                    </tr>
                <?php } ?>


            </tbody>
        </table>

    </div>
</div>
