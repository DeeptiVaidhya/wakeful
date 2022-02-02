<?php //print_r($data) ?>
<div class='row'>
    <?php if (isset($data) && $data['title'] != '') { ?>
    <div class="control-label col-md-3 col-sm-3 col-xs-12">
        <h5>Title</h5>
    </div>
    <div class = "col-md-9 col-sm-9 col-xs-12" ><h5><?php echo $data['title'] ?></h5></div>
    <?php }?>
      <?php if (isset($data) && $data['files_id'] != '') { ?>
    <div class="control-label col-md-3 col-sm-3 col-xs-12">
        <h5>Generated Audio</h5>
    </div>
    <div class = "col-md-9 col-sm-9 col-xs-12" ><?php echo get_file($data['files_id']) ?></div>
						
    <?php } ?>
    <?php if (isset($data) && $data['tip'] != '') { ?>
    <div class="control-label col-md-3 col-sm-3 col-xs-12">
        <h5>Tip</h5>
    </div>
    <div class = "col-md-9 col-sm-9 col-xs-12" ><h5><?php echo $data['tip'] ?></h5></div>
    <?php } ?>
    <?php if (isset($data) && $data['script'] != '') { ?>
    <div class="control-label col-md-3 col-sm-3 col-xs-12">
        <h5>Script</h5>
    </div>
    
    <div class = "col-md-9 col-sm-9 col-xs-12" ><h5><?php echo $data['script'] ?></h5></div>
    <?php  }?>
  
</div>    

<!--//                        var obj = { title: 'Title', tip: 'Tip',script: 'Script'};
//                        $.each(result.data, function(i, v) {
//                            if (obj[i] != undefined && v != null) {
//                                content += '<div class="control-label col-md-3 col-sm-3 col-xs-12">' +
//                                    '<h5>' + obj[i] + '</h5></div>';
//                                content += '<div class = "col-md-9 col-sm-9 col-xs-12" ><h5>' + v + '</h5></div>';
//                            }
//                        });
//                        content += "</div>";-->