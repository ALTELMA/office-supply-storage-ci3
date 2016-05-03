<div id="content">
    <div class="pageHeader">
    	<div class="left"><h3 class="header"><?php echo ':: '.$title.' ::';?></h3></div>
        <div class="right">
        	<a href="<?php echo base_url().'asset/category/add';?>">เพิ่มข้อมูล</a>
        </div>
        <div class="clr"></div>
    </div>

    <!-- DATA LIST -->
    <div class="dataList">
    <?php
		// ASSET CATEGORY LIST
		if($categoryResult){
			foreach($categoryResult as $category){

				// CONFIG IMAGE
				$edit_icon = array('src' => base_url().'assets/img/templates/edit.gif', 'width' => 16, 'alt' => 'edit', 'title' => 'แก้ไข');
				$del_icon = array('src' => base_url().'assets/img/templates/del.gif', 'width' => 16, 'alt' => 'del', 'title' => 'ลบ');

				echo '<div class=\'dataRow\'>';
				echo '<div class=\'left\'><a href=\''.base_url().'asset/category/view/'.$category->cat_id.'\'>'.$category->catType.' - '.$category->catName.'</a></div>';
				echo '<div class=\'right\'>';
				echo '<a href='.base_url().'asset/category/edit/'.$category->cat_id.'>'.img($edit_icon).'</a>&nbsp;';
				echo '<a href='.base_url().'asset/category/del/'.$category->cat_id.' onClick="return confirm(\'คุณต้องการลบข้อมูลนี้?\');">'.img($del_icon).'</a>';
				echo '</div>';
				echo '<div class=\'clr\'></div>';
				echo '</div>';
			}
		}
		// PAGINATION
        echo $this->pagination->create_links();
	?>
    </div>
</div>