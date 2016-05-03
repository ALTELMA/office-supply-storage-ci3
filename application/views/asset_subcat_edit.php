<div id="content">    
    <div class="pageHeader">
    	<div><h3 class="header"><?php echo ':: '.$title.' ::';?></h3></div>
    </div>
    
    <!-- DATA LIST -->
    <div class="formData">
    	<form method="POST" action="<?php base_url().'asset/subCategory/add'?>">
        	<label>ชื่อประเภททรัพย์สินย่อย : </label>
            <input type="text" id="txt_subcategory" name="txt_subcategory" value="<?php echo $subCategoryData->subTypeName;?>">
            <div class="submit">
            <input type="submit" name="submit" value="บันทึกข้อมูล">
            <input class="buttonBlue" type="button" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url().'asset/category'?>';">
            </div>
        </form>
    </div>
</div>