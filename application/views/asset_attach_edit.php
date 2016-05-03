<div id="content">    
    <div class="pageHeader">
    	<div><h3 class="header"><?php echo ':: '.$title.' ::';?></h3></div>
    </div>
    
    <!-- DATA LIST -->
    <div class="formData">
    	<form method="POST" enctype="multipart/form-data" action="<?php base_url().'asset/attach/edit'?>">
        	<label>ชื่อไฟล์แนบ : </label>
            <input type="text" id="txt_filename" name="txt_filename" value="<?php echo $attachData->fileName;?>">
        	<label>ไฟล์ : </label>
            <input type="file" id="uploadFile" name="uploadFile">
            <?php if($attachData->filePath != NULL){echo '<span class=\'txt-warning\'>[ '.$attachData->filePath.' ]</span>';}?>
            <label>หมายเหตุ <span class="txt-comment">***ถ้ามี</span></label>
            <textarea id="txt_remark" name="txt_remark"></textarea>
            <div class="submit">
                <input type="submit" name="submit" value="บันทึกข้อมูล">
                <input class="buttonBlue" type="button" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url().'asset/view/'.$attachData->asset_id;?>';">
            </div>
        </form>
    </div>
</div>