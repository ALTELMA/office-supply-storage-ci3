<div id="content">
    <div class="pageHeader">
    	<div><h3 class="header"><?php echo ':: '.$title.' ::';?></h3></div>
    </div>

    <!-- DATA FORM ADD -->
    <div class="formData">
    	<form method="POST" enctype="multipart/form-data" action="<?php base_url().'asset/add'?>">
        	<div class="formRow">
            	<span>ประเภทของทรัพย์สิน : </span>
            	<select id="assetCat" name="assetCat">
                	<?php
                    	foreach($categoryResult as $categoryData){
							echo '<option value=\''.$categoryData->cat_id.'\'>'.$categoryData->catName.'</option>';
						}
					?>
                </select>
            </div>
            <div class="formRow">
            	<span>ประเภทย่อยของทรัพย์สิน : </span>
            	<select id="assetSubCat" name="assetSubCat">
                	<?php
                    	foreach($subCategoryResult as $subCategoryData){
							echo '<option value=\''.$subCategoryData->id.'\'>'.$subCategoryData->subTypeName.'</option>';
						}
					?>
                </select>
            </div>
        	<label>รหัสทรัพย์สิน : <span id="existCode" class="txt-warning"></span></label>
            <input type="text" id="txt_code" name="txt_code">
            <div class="formRow">
            	<span>รูปภาพทรัพย์สิน : </span>
                <input type="file" name="asset_img">
            </div>
            <label>รายละเอียดทรัพย์สิน : </label>
            <input type="text" id="txt_detail" name="txt_detail">
            <label>ราคาทรัพย์สิน : </label>
            <input type="text" id="txt_value" name="txt_value">
            <label>วันที่จัดซื้อ : </label>
            <input type="text" id="txt_soldDate" class="dateSelect" name="txt_soldDate">
            <label>ระยะเวลารับประกัน : </label>
            <input type="text" class="date" id="warrantyFrom" name="warrantyFrom" placeholder="จาก">
            <input type="text" class="date" id="warrantyTo" name="warrantyTo" placeholder="ถึง">
            <label>ผู้รับผิดชอบ</label>
            <input type="text" id="txt_responseUser" name="txt_responseUser">
            <div class="formRow">
                <span>แผนกผู้รับผิดชอบ : </span>
                <select id="txt_department" name="txt_department">
                <?php
                    foreach($departmentResult as $departmentData){
                        echo '<option value=\''.$departmentData->department_id.'\'>'.$departmentData->departmentName.'</option>';
                    }
                ?>
                </select>
            </div>
            <label>ที่เก็บ/สถานที่ตั้งทรัพย์สิน : </label>
            <input type="text" id="txt_location" name="txt_location">
            <div class="formRow">
                <span>สถานะทรัพย์สิน : </span>
                <select id="txt_status" name="txt_status">
                <?php
                    // GET ASSET STATUS
                    foreach($statusResult as $statusData){
                        echo '<option value=\''.$statusData->status_id.'\'>'.$statusData->statusName.'</option>';
                    }
                ?>
                </select>
            </div>
            <div class="formRow">
                <span>การอนุมัติข้อมูล :</span>
                <input type="radio" name="IsApproved" value="1"> อนุมัติ <input type="radio" name="IsApproved" value="0" checked> ไม่อนุมัติ
            </div>
            <label>หมายเหตุ <span class="txt-comment">***ถ้ามี</span></label>
            <textarea id="txt_remark" name="txt_remark"></textarea>
            <div class="submit">
            	<input type="submit" name="asset_add" value="บันทึกข้อมูล">
                <input type="button" class="buttonBlue" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url().'asset'?>';">
            </div>
        </form>
    </div>
</div>