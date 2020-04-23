<div id="page-wrapper">
  <div class="row">
      <div style="margin:20px;">
          <h1><?php if (isset($query)) {
    echo "Edit Blog";
} else {
    echo "Add Blog";
} ?></h1><hr>

          <form class="form-horizontal" id ="booking_form" action="<?php echo base_url()?>employee/blogs" method="POST" enctype="multipart/form-data">

      	  <div>
              <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php if (isset($query[0]['id'])){echo $query[0]['id'];}?>">
              <?php echo form_error('id'); ?>
          </div>

          <div>
      	  <div style="float:left;width:33%;" class="form-group <?php if( form_error('title') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="title" class="col-md-2">Title:</label>
            <div class="col-md-2">
              <input style="width:800px;" type="text" class="form-control"  name="title" value = "<?php if (isset($query[0]['title'])){echo $query[0]['title'];}?>" required>
              <?php echo form_error('title'); ?>
            </div>
          </div>

      	  <div style="width:33%;" class="form-group <?php if( form_error('url') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="url" class="col-md-2">URL:</label>
            <div class="col-md-2">
              <input style="width:800px;" type="text" class="form-control"  name="url" value = "<?php if (isset($query[0]['url'])){echo $query[0]['url'];}?>" required>
              <?php echo form_error('url'); ?>
            </div>
          </div>
          </div>


          <div>
      	  <div style="width:33%;" class="form-group <?php if( form_error('description') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="description" class="col-md-2">Description:</label>
            <div class="col-md-2">
                <textarea style="height:80px;width:800px;" type="text" class="form-control"  name="description" value = "<?php if (isset($query[0]['description'])) {
                        echo $query[0]['description'];
                }?>"><?php
                if (isset($query[0]['description'])) {
                    echo $query[0]['description'];
                }?></textarea>
                <?php echo form_error('description'); ?>
            </div>
          </div>
          </div>

          <div>
      	  <div style="float:left;width:45%;" class="form-group <?php if( form_error('keyword') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="keyword" class="col-md-2">Keyword:</label>
            <div class="col-md-2">
              <input style="width:350px;" type="text" class="form-control"  name="keyword" value = "<?php if (isset($query[0]['keyword'])){echo $query[0]['keyword'];}?>" >
              <?php echo form_error('keyword'); ?>
            </div>
          </div>

      	  <div style="float:left;width:45%;" class="form-group <?php if( form_error('author') ) { echo 'has-error';} ?>">
            <label style="width:100px;" for="author" class="col-md-2">Author:</label>
            <div class="col-md-2">
                <input style="width:320px;" type="text" class="form-control"  name="author"
                       value = "<?php if (isset($query[0]['author'])) {
    echo $query[0]['author'];
} ?>">
<?php echo form_error('author'); ?>
            </div>
          </div>

      	  <div style="width:33%;" class="form-group <?php if( form_error('content') ) { echo 'has-error';} ?>">
            <label style="width:150px;" for="content" class="col-md-2">Content:</label>
            <div class="col-md-2">
                <textarea style="width:800px;height:600px;" type="text" class="form-control"  name="content"><?php
                    if (isset($query[0]['content'])) {
                        echo trim($query[0]['content']);}
                ?></textarea>
<?php echo form_error('content'); ?>
            </div>
          </div>
          </div>


          <div>
      	  <div style="float:left;width:50%;" class="form-group <?php if( form_error('file_input') ) { echo 'has-error';} ?>">
              <label style="width:150px;" for="file_input" class="col-md-2">Feature picture name:</label>
              <div class="col-md-2">
                <input style="width:400px;" type="text" class="form-control"  name="file_input" value = "<?php if (isset($query[0]['file_input'])) {
    echo $query[0]['file_input'];
} ?>">
<?php echo form_error('file_input'); ?>
            </div>
          </div>

      	  <div style="float:left;width:40%;" class="form-group <?php if( form_error('alternate_text') ) { echo 'has-error';} ?>">
            <label style="width:120px;" for="alternate_text" class="col-md-2">Alternate Text:</label>
            <div class="col-md-2">
              <input style="width:240px;" type="text" class="form-control"  name="alternate_text" value = "<?php if (isset($query[0]['alternate_text'])){echo $query[0]['alternate_text'];}?>">
              <?php echo form_error('alternate_text'); ?>
            </div>
          </div>
          </div>

          <div style="float:left;width:65%;" class="form-group">
          	<center>
                    <input type="Submit" value="<?php if (isset($query[0]['id'])) {
    echo "Update Blog";
} else {
    echo "Save Blog";
} ?>" class="btn btn-primary">
                </center>
          </div>

        </form>
      </div>
  </div>
</div>