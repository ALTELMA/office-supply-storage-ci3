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
                <?php echo form_open('product/subcategory/add') ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>ประเภทของทรัพย์สิน</label>
                            <select id="assetCat" class="form-control" name="assetCat">
                                <option value="">เลือกประเภทของทรัพย์สิน</option>
                                <?php foreach($categoryList as $categoryData): ?>
                                    <option value="<?php echo $categoryData->cat_id; ?>"><?php echo $categoryData->catName; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ชื่อหมวดหมู่ทรัพย์สิน</label>
                            <input id="txt_subcategory" class="form-control" name="txt_subcategory" placeholder="ชื่อหมวดหมู่ทรัพย์สิน">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="submit" value="บันทึกข้อมูล">
                            <input type="button" class="btn btn-default" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url('product/subcategory'); ?>';">
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
	</div>
</div>
<!-- /.row -->
