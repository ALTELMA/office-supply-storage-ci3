<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $title; ?></h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard
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
                <?php echo form_open('product/category/edit/' . $category->cat_id) ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>หมวดหมู่หลัก</label>
                            <input id="txt_type" class="form-control" name="txt_type" value="<?php echo $category->catType ? : ''; ?>" placeholder="หมวดหมู่หลัก">
                            <p class="help-block">ตัวอย่าง A - โต๊ะ, B - โต๊ะ, C - โต๊ะ</p>
                        </div>
                        <div class="form-group">
                            <label>ชื่อประเภททรัพย์สิน</label>
                            <input id="txt_category" class="form-control" name="txt_category" value="<?php echo $category->catName ? : ''; ?>"placeholder="ชื่อประเภททรัพย์สิน">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="submit" value="บันทึกข้อมูล">
                            <input type="button" class="btn btn-default" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url('product/category'); ?>';">
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
	</div>
</div>
<!-- /.row -->
