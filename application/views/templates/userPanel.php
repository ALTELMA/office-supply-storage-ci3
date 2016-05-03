<!-- USER PANEL -->
<div id="userPanel">
    <div class="left">ยินดีต้อนรับ <span class="strong"><?php echo $name;?></span></div>
    <div class="right">
        <!--<a href="<?php echo base_url().'user/view/'.$userID;?>">ข้อมูลผู้ใช้ระบบ</a>-->
        <a href="<?php echo site_url('user/logout');?>">ออกจากระบบ</a>
    </div>
</div>

<!-- USER TOOLS -->
<div id="userTool">
	<span>เมนูใช้งานระบบ</span>
    <a href="<?php echo site_url('asset');?>">[ รายการทรัพย์สิน ]</a>
    <a href="<?php echo site_url('asset/category/list');?>">[ ประเภททรัพย์สิน ]</a>
    <a href="<?php echo site_url('asset/report');?>">[ ออกรายงาน ]</a>
</div>