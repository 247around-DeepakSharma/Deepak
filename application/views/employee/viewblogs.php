<div style="width:100%;" id="page-wrapper">
    <div class="row">
        <div style="margin:10px;">
            <h1>Blogs</h1><hr>
            <table  class="table table-striped table-bordered">

                <tr>
                    <th>S No.</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Keyword</th>
                    <th>Description</th>
                    <th colspan="2">Action</th>
                </tr>

                <?php $count = 1; ?>
                <?php foreach ($query as $key => $row) { ?>
                    <tr>
                        <td><?php echo $count;
                $count++;
                    ?>.</td>
                        <td><a href="<?php echo base_url(); ?>employee/blogs/editblog/<?= $row['id']; ?>"><?= $row['title']; ?></a></td>
                        <td><?= $row['author']; ?></td>
                        <td><?= $row['keyword']; ?></td>
                        <td><?= $row['description']; ?></td>
                        <td><?php
                            if ($row['published'] == 1) {
                                echo "<a id='edit' class='btn btn-small btn-primary' "
                                . "href=" . base_url() . "employee/blogs/unpublish/$row[id]>Unpublish</a>";
                            } else {
                                echo "<a id='edit' class='btn btn-small btn-success' "
                                . "href=" . base_url() . "employee/blogs/publish/$row[id]>Publish</a>";
                            }
                            ?>
                        </td>
                        <td><?php
                            echo "<a id='edit' class='btn btn-small btn-danger' "
                            . "href=" . base_url() . "employee/blogs/delete/$row[id]>Delete</a>";
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
            </table>

            <div>
                <center><a href="<?php echo base_url(); ?>employee/blogs/addblog"><input class="btn btn-primary" type="Button" value="Add Blog"></a></center>
            </div>
        </div>
    </div>
</div>
