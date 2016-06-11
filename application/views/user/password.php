<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $title; ?></h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> <a href="<?php echo base_url('dashboard/index'); ?>">แดชบอร์ด</a>
            </li>
            <li class="active">
                <i class="fa fa-user"></i> <a href="<?php echo base_url('user/view/' . $user->user_id); ?>">ข้อมูลผู้ใช้งานระบบ</a>
            </li>
            <li class="active">
                <i class="fa fa-pencil"></i> <?php echo $title; ?>
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->

<?php if($this->session->userdata('error') != null): ?>
    <div class="alert alert-danger">
        <?php echo $this->session->userdata('error'); ?>
    </div>
<?php endif; ?>

<?php
if($this->session->userdata('error') != null) {
    $this->session->unset_userdata('error');
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <?php echo form_open('user/updatePassword/' . $user->user_id) ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>รหัสผ่านเดิม</label>
                            <input type="password" id="txt_current_password" class="form-control" name="txt_current_password" placeholder="รหัสผ่านเดิม">
                        </div>
                        <div class="form-group">
                            <label>รหัสผ่านใหม่</label>
                            <input type="password" id="txt_new_password" class="form-control" name="txt_new_password" placeholder="รหัสผ่านใหม่">
                        </div>
                        <div class="form-group">
                            <label>ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" id="txt_confirm_password" class="form-control" name="txt_confirm_password" placeholder="ยืนยันรหัสผ่านใหม่">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="submit" value="บันทึกข้อมูล">
                            <input type="button" class="btn btn-default" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url('user/view/' . $user->user_id); ?>';">
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
