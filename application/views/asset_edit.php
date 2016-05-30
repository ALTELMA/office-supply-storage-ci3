<div id="content">
    <div class="pageHeader">
    	<div><h3 class="header"><?php echo ':: '.$title.' ::';?></h3></div>
        <div></div>
    </div>

    <!-- DATA FORM ADD -->
    <div class="formData">
    	<form method="POST" enctype="multipart/form-data" action="<?php base_url().'asset/add'?>">
        	<div class="formRow">
            	<span>ประเภทของทรัพย์สิน : </span>
            	<select id="assetCat" name="assetCat">
                	<?php
                    	foreach($categoryResult as $categoryData){
							if($categoryData->cat_id == $assetData->cat_id){$seleced = 'selected';}else{$seleced = '';}
							echo '<option value=\''.$categoryData->cat_id.'\' '.$seleced.'>'.$categoryData->catName.'</option>';
						}
					?>
                </select>
            </div>
            <div class="formRow">
            	<span>ประเภทย่อยของทรัพย์สิน : </span>
            	<select id="assetSubCat" name="assetSubCat">
                	<?php
                    	foreach($subCategoryResult as $subCategoryData){
							if($subCategoryData->id == $assetData->cat_sub_id){$seleced = 'selected';}else{$seleced = '';}
							echo '<option value=\''.$subCategoryData->id.'\' '.$seleced.'>'.$subCategoryData->subTypeName.'</option>';
						}
					?>
                </select>
            </div>
        	<label>รหัสทรัพย์สิน : <span id="existCode" class="txt-warning"></span></label>
            <input type="text" id="txt_code" name="txt_code" value="<?php echo $assetData->code;?>">
            <div class="formRow">
            	<span>รูปภาพทรัพย์สิน : </span>
                <input type="file" name="asset_img">
                <?php
					if(!empty($assetData->assetFullPic)){
						$previewImg = array(
									'class' => 'border',
									'src' => base_url().'assets/images/asset_image/resize/'.$assetData->assetFullPic,
									'width' => 400,
									'height' => 400,
									'alt' => 'preview_asset_image'
									);
					}else{
						$previewImg = array(
									'class' => 'border',
									'src' => base_url().'assets/images/templates/no_image.gif',
									'width' => 400,
									'height' => 400,
									'alt' => 'preview_asset_image'
									);
					}
					echo img($previewImg);
				?>
            </div>
            <label>รายละเอียดทรัพย์สิน : </label>
            <input type="text" id="txt_detail" name="txt_detail" value="<?php echo $assetData->detail;?>">
            <label>ราคาทรัพย์สิน : </label>
            <input type="text" id="txt_value" name="txt_value" value="<?php echo $assetData->value;?>">
            <label>วันที่จัดซื้อ : </label>
            <input type="text" id="txt_soldDate" class="dateSelect" name="txt_soldDate" value="<?php echo $this->mydatesystem->restoreDate('-',$assetData->soldDate);?>">
            <label>ระยะเวลารับประกัน : </label>
            <input type="text" class="date" id="warrantyFrom" name="warrantyFrom" placeholder="จาก" value="<?php echo $this->mydatesystem->restoreDate('-',$assetData->warrantyStartDate);?>">
            <input type="text" class="date" id="warrantyTo" name="warrantyTo" placeholder="ถึง" value="<?php echo $this->mydatesystem->restoreDate('-',$assetData->warrantyEndDate);?>">
            <label>ผู้รับผิดชอบ</label>
            <input type="text" id="txt_responseUser" name="txt_responseUser" value="<?php echo $assetData->responseUser;?>">
            <div class="formRow">
                <span>แผนกผู้รับผิดชอบ : </span>
                <select id="txt_department" name="txt_department">
                <?php
                    foreach($departmentResult as $departmentData){
						if($departmentData->department_id == $assetData->department_id){$seleced = 'selected';}else{$seleced = '';}
                        echo '<option value=\''.$departmentData->department_id.'\' '.$seleced.'>'.$departmentData->departmentName.'</option>';
                    }
                ?>
                </select>
            </div>
            <label>ที่เก็บ/สถานที่ตั้งทรัพย์สิน : </label>
            <input type="text" id="txt_location" name="txt_location" value="<?php echo $assetData->locationStorage;?>">
            <div class="formRow">
                <span>สถานะทรัพย์สิน : </span>
                <select id="txt_status" name="txt_status">
                <?php
                    // GET ASSET STATUS
                    foreach($statusResult as $statusData){
						if($statusData->status_id == $assetData->status){$seleced = 'selected';}else{$seleced = '';}
                        echo '<option value=\''.$statusData->status_id.'\'>'.$statusData->statusName.'</option>';
                    }
                ?>
                </select>
            </div>
            <div class="formRow">
                <span>การอนุมัติข้อมูล :</span>
                <input type="radio" name="IsApproved" value="1"> อนุมัติ
                <input type="radio" name="IsApproved" value="0"> ไม่อนุมัติ
            </div>
            <label>หมายเหตุ <span class="txt-comment">***ถ้ามี</span></label>
            <textarea id="txt_remark" name="txt_remark"><?php echo $assetData->remark;?></textarea>
            <div class="submit">
            	<input type="submit" name="asset_edit" value="บันทึกข้อมูล">
                <input type="button" class="buttonBlue" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url().'asset'?>';">
                <input type="hidden" id="getAssetID" value="<?php echo $assetData->code;?>">
            </div>
        </form>
    </div>
</div>
