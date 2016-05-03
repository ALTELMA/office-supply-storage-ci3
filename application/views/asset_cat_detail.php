<div id="content">    
    <div class="pageHeader">
    	<div class="left"><h3 class="header"><?php echo ':: '.$title.' [ '.$categoryData->catType.'-'.$categoryData->catName.' ] ::';?></h3></div>
        <div class="right">
        	<a href="<?php echo base_url().'asset/subCategory/add/'.$categoryData->cat_id;?>">เพิ่มข้อมูล</a>
        </div>
        <div class="clr"></div>
    </div>
        
    <!-- DATA LIST -->
    <div class="dataList">
    	<?php
			if($subCategoryResult){
				foreach($subCategoryResult as $subCategory){
					
					// CONFIG IMAGE
					$edit_icon = array('src' => base_url().'assets/img/templates/edit.gif', 'width' => 16, 'alt' => 'edit', 'title' => 'แก้ไข');
					$del_icon = array('src' => base_url().'assets/img/templates/del.gif', 'width' => 16, 'alt' => 'del', 'title' => 'ลบ');
					
					echo '<div class=\'dataRow\'>';
					echo '<div class=\'left\'>'.$subCategory->subTypeName.'</div>';
					echo '<div class=\'right\'>';
					echo '<a href='.base_url().'asset/subCategory/edit/'.$subCategory->id.'>'.img($edit_icon).'</a> ';
					echo '<a href='.base_url().'asset/subCategory/del/'.$subCategory->id.'>'.img($del_icon).'</a>';
					echo '</div>';
					echo '<div class=\'clr\'></div>';
					echo '</div>';
				}
			}
        // PAGINATION
        echo $this->pagination->create_links();?>
    </div>
</div>