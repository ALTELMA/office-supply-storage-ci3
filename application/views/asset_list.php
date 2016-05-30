<div id="content">
    <div class="pageHeader">
    	<?php
        	$pageHeaderIcon = array(
							'src' => '../assets/img/templates/'
							);
		?>
    	<div class="left"><h3 class="header"><?php echo ':: '.$title.' ::';?></h3></div>
        <div class="right">
        	<a href="<?php echo base_url().'asset/add';?>">เพิ่มข้อมูล</a>
        </div>
        <div class="clr"></div>
    </div>

    <!-- SEARCH BOX -->
    <div class="searchBox">
        <form method="POST" action="<?php echo base_url().'asset/page';?>">
        	<label>ค้นหาข้อมูล : </label>
            <select id="assetCat" name="assetCat">
            	<option value="">เลือกประเภทหลัก</option>
            <?php
                foreach($categoryResult as $categoryData){
					if($cat_id == $categoryData->cat_id){$select = 'selected';}else{$select = '';}
					echo '<option value=\''.$categoryData->cat_id.'\' '.$select.'>'.$categoryData->catName.'</option>';
				}
			?>
            </select>
            <select id="assetSubCat" name="assetSubCat">
            	<option value="">เลือกประเภทย่อย</option>
            <?php
                foreach($subCategoryResult as $subCategoryData){
					if($subCat_id == $subCategoryData->id){$select = 'selected';}else{$select = '';}
					echo '<option value=\''.$subCategoryData->id.'\' '.$select.'>'.$subCategoryData->subTypeName.'</option>';
				}
			?>
            </select>
            <input type="text" name="txt_search" placeholder="ระบุคำที่ใช้ค้นหา" value="<?php echo $keyword;?>">
            <input type="submit" class="buttonBlue" name="searchSubmit" value="ค้นหา">
        </form>
    </div>

    <!-- DATA LIST -->
    <div class="dataList">
    <?php
		if($assetResult){
			$page = $this->uri->segment(3) != NULL?$this->uri->segment(3):'';

    		foreach($assetResult as $assetData){

				// CONFIG DATA FROM DATABASE
				// THUMB
				if(!empty($assetData->assetFullPic)){
					$assetThumb = array(
								'src' => base_url().'assets/images/asset_image/thumb/'.$assetData->assetThumbPic,
								'width' => 100,
								'height' => 100,
								'alt' => 'asset_thumb_pic',
								'title' => 'รูปภาพครุภัณฑ์'
								);
				}else{
						$assetThumb = array(
								'src' => base_url().'assets/img/templates/no_image.gif',
								'width' => 100,
								'height' => 100,
								'alt' => 'asset_thumb_pic',
								'title' => 'ไม่มีรูปภาพ'
								);
				}

				// ASSET STATUS TXT COLOR
				if($assetData->status == 1){
					$txt_status = '<span class=\'txt-valid\'>'.$assetData->statusName.'</span>';
				}else{
					$txt_status = '<span class=\'txt-warning\'>'.$assetData->statusName.'</span>';
				}

				$approveIcon = $assetData->IsApproved == 1?base_url().'assets/img/templates/valid.gif':base_url().'assets/img/templates/invalid.gif';
	?>
                <div class="dataRow">
                    <div class="dataPicCol"><a href="<?php echo base_url().'asset/view/'.$assetData->id.'/'.$page;?>"><?php echo img($assetThumb);?></a></div>
                    <div class="dataNameCol">
                        <p><?php echo $assetData->code;?></p>
                        <?php
							// DETAIL
							if(!empty($assetData->detail)){
								echo ' <p>รายละเอียด : '.$assetData->detail.'</p>';
							}

							// SOLD DATE
							if($assetData->soldDate != '0000-00-00'){
								echo '<p> วันที่จัดซื้อ : '.$this->mydatesystem->Thaidate($assetData->soldDate, 2).'</p>';
							}

							// WARRANTY DATE
                        	if($assetData->warrantyStartDate != '0000-00-00' && $assetData->warrantyEndDate != '0000-00-00'){
								echo '<p> วันที่รับประกัน : '.$this->mydatesystem->Thaidate($assetData->warrantyStartDate, 2).' - '.$this->mydatesystem->ThaiDate($assetData->warrantyEndDate, 2).'</p>';
							}

							// ASSET VALUE
							if(!empty($assetData->value)){
								$value = number_format($assetData->value,2).'&nbsp;<span class=\'txt-comment\'>(รวม VAT 7%)</span>';
								echo '<p> ราคา : '.$value.'</p>';
							}

							// ASSET STATUS
							echo '<p> สถานะ : '.$txt_status.'</p>';

							// ASSET REMARK
							if(!empty($assetData->remark)){echo '<p> หมายเหตุ : <span class="txt-warning">'.$assetData->remark.'</span></p>';}
						?>
                    </div>
                    <div class="dataManageCol">
                        <a href="<?php echo base_url().'asset/verify/'.$assetData->id;?>"><img src="<?php echo $approveIcon;?>" width="24" title="การอนุมัติ"></a>
                        <a href="<?php echo base_url().'asset/edit/'.$assetData->id.'/'.$page;?>"><img src="<?php echo base_url().'assets/img/templates/edit.gif'?>" width="24" title="แก้ไข"></a>
                        <a href="<?php echo base_url().'asset/del/'.$assetData->id.'/'.$page;?>" onClick="return confirm('คุณต้องการลบข้อมูลนี้?');"><img src="<?php echo base_url().'assets/img/templates/del.gif'?>" width="24" title="ลบ"></a>
                    </div>
                    <div class="clr"></div>
                </div>
    <?php
			}
		}else{
			echo '<div class="dataRow"><p class=\'txt-warning center\'>:: ไม่พบข้อมูลที่ค้นหา ::</p></div>';
		}
	?>
    <!-- PAGINATION -->
	<?php echo $this->pagination->create_links();?>
    </div>
</div>
