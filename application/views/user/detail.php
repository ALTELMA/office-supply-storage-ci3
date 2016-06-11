<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $title; ?></h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> <a href="<?php echo base_url('dashboard/index'); ?>">แดชบอร์ด</a>
            </li>
            <li class="active">
                <i class="fa fa-user"></i> <?php echo $title; ?>
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-12">
        <div class="text-right">
            <a href="<?php echo base_url('user/edit/' . $user->user_id);?>" class="btn btn-warning">
                <i class="fa fa-pencil"></i> แก้ไขข้อมูล
            </a>
            <a href="<?php echo base_url('user/password/' . $user->user_id);?>" class="btn btn-info">
                <i class="fa fa-key"></i> เปลี่ยนรหัสผ่าน
            </a>
            <a href="<?php echo base_url('user/view/' . $user->user_id);?>" class="btn btn-default">
                <i class="fa fa-chevron-left"></i> ย้อนกลับ
            </a>
        </div>
    </div>
</div>

<br>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>ชื่อผู้ใช้</td>
                            <td><?php echo $user->username; ?></td>
                        </tr>
                        <tr>
                            <td>ชื่อ-นามสกุล</td>
                            <td><?php echo $user->name; ?></td>
                        </tr>
                        <tr>
                            <td>วันที่ล็อคอินล่าสุด</td>
                            <td><?php echo $this->mydatesystem->Thaidate($user->lastLoginDate, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
