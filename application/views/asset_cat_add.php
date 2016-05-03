<div id="content">    
    <div class="pageHeader">
    	<div><h3 class="header"><?php echo ':: '.$title.' ::';?></h3></div>
    </div>
    
    <!-- DATA LIST -->
    <div class="formData">
    	<form method="POST" action="<?php base_url().'asset/categoryAdd'?>">
        	<label>หมวดหมู่หลัก : <span class="txt-comment">ตัวอย่าง A, B, C</span></label>
            <input type="text" id="txt_type" name="txt_type">
        	<label>ชื่อประเภททรัพย์สิน : </label>
            <input type="text" id="txt_category" name="txt_category">
            <div class="submit">
                <input type="submit" name="submit" value="บันทึกข้อมูล">
                <input class="buttonBlue" type="button" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url().'asset/category';?>';">
            </div>
        </form>
    </div>
</div>