<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $title; ?></h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> <a href="<?php echo base_url('dashboard/index'); ?>">แดชบอร์ด</a>
            </li>
            <li class="active">
                <i class="fa fa-cube"></i> <a href="<?php echo base_url('product/listing'); ?>">รายการทรัพย์สิน</a>
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
            <div class="panel-heading">เพิ่มข้อมูล</div>
            <div class="panel-body">
                <?php echo form_open_multipart('product/attach/add/' . $product->id) ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>ชื่อไฟล์แนบ</label>
                            <input id="txt_filename" class="form-control" name="txt_filename" placeholder="รหัสทรัพย์สิน">
                        </div>
                        <div class="form-group">
                            <label>รูปภาพทรัพย์สิน</label>
                            <input type="file" id="uploadFile" name="uploadFile">
                        </div>
                        <div class="form-group">
                            <label>หมายเหตุ <span class="txt-comment">***ถ้ามี</span></label>
                            <textarea id="txt_remark" class="form-control" name="txt_remark"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="submit" value="บันทึกข้อมูล">
                            <input type="button" class="btn btn-default" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url('product/view/' . $product->id); ?>';">
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
