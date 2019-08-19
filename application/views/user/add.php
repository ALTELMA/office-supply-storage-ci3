<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $title; ?></h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> <a href="<?php echo base_url('dashboard/index'); ?>">แดชบอร์ด</a>
            </li>
            <li class="active">
                <i class="fa fa-user"></i> <a href="<?php echo base_url('user/listings/'); ?>">ข้อมูลผู้ใช้งานระบบ</a>
            </li>
            <li class="active">
                <i class="fa fa-pencil"></i> <?php echo $title; ?>
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <?php echo form_open('user/create') ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>ชื่อผู้ใช้</label>
                            <input id="txt_username" class="form-control" name="txt_username" value="" placeholder="ชื่อผู้ใช้">
                        </div>
                        <div class="form-group">
                            <label>ชื่อ-นามสกุล</label>
                            <input id="txt_name" class="form-control" name="txt_name" value="" placeholder="ชื่อ-นามสกุล">
                        </div>
                        <div class="form-group">
                            <label>รหัสผ่าน</label>
                            <input type="password" id="txt_password" class="form-control" name="txt_password" placeholder="รหัสผ่าน">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="submit" value="บันทึกข้อมูล">
                            <input type="button" class="btn btn-default" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url('user/lisitngs/'); ?>';">
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
