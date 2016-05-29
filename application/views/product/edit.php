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
                <?php echo form_open('product/edit/' . $product->id) ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>ประเภทของทรัพย์สิน</label>
                            <select id="assetCat" class="form-control" name="assetCat">
                                <option value="">เลือกประเภทของทรัพย์สิน</option>
                            <?php foreach($categoryResult as $categoryData): ?>
                                <option value="<?php echo $categoryData->cat_id; ?>" <?php echo $categoryData->cat_id == $product->cat_id ? "selected" : ''; ?>>
                                    <?php echo $categoryData->catName; ?>
                                </option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ประเภทย่อยของทรัพย์สิน</label>
                            <select id="assetSubCat" class="form-control" name="assetSubCat">
                                <option value="">เลือกประเภทย่อยของทรัพย์สิน</option>
                            <?php foreach($subCategoryResult as $subCategoryData): ?>
                                <option value="<?php echo $subCategoryData->cat_id; ?>" <?php echo $subCategoryData->id == $product->sub_cat_id ? "selected" : ''; ?>><?php echo $subCategoryData->subTypeName; ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>รหัสทรัพย์สิน</label>
                            <input id="txt_code" class="form-control" name="txt_code" value="<?php echo $product->code ? : ''; ?>" placeholder="รหัสทรัพย์สิน">
                        </div>
                        <div class="form-group">
                            <label>รูปภาพทรัพย์สิน</label>
                            <input type="file" name="asset_img">
                        </div>
                        <div class="form-group">
                            <label>รายละเอียดทรัพย์สิน</label>
                            <input type="text" id="txt_detail" class="form-control" name="txt_detail" value="<?php echo $product->detail ? : ''; ?>" placeholder="รายละเอียดทรัพย์สิน">
                        </div>
                        <div class="form-group">
                            <label>ราคาทรัพย์สิน</label>
                            <input id="txt_value" class="form-control" name="txt_value" value="<?php echo $product->value ? : ''; ?>" placeholder="ราคาทรัพย์สิน">
                        </div>
                        <div class="form-group">
                            <label>สถานะทรัพย์สิน</label>
                            <select id="txt_status" class="form-control" name="txt_status">
                                <option value="">เลือกสถานะทรัพย์สิน</option>
                            <?php foreach($statusResult as $statusData): ?>
                                <option value="<?php echo $statusData->status_id; ?>" <?php echo $statusData->status_id == $product->status ? "selected" : ''; ?>><?php echo $statusData->statusName; ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>การอนุมัติข้อมูล</label>
                            <label class="radio-inline">
                                <input type="radio" name="IsApproved" value="1" <?php echo !empty($product->IsApproved) && $product->IsApproved == 1 ? 'checked' : ''; ?>> อนุมัติ
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="IsApproved" value="0" <?php echo empty($product->IsApproved) ? 'checked' : ''; ?>> ไม่อนุมัติ
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>วันที่จัดซื้อ</label>
                            <input type="text" id="txt_soldDate" class="form-control dateSelect" name="txt_soldDate" value="<?php echo $product->soldDate ? $this->mydatesystem->restoreDate('-',$product->soldDate) : ''; ?>" placeholder="วันที่จัดซื้อ">
                        </div>
                        <div class="form-group">
                            <label>ระยะเวลารับประกัน</label>
                            <input type="text" class="form-control" id="warrantyFrom" name="warrantyFrom" value="<?php echo $product->warrantyStartDate ? $this->mydatesystem->restoreDate('-',$product->warrantyStartDate) : ''; ?>" placeholder="จาก">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="warrantyTo" name="warrantyTo" value="<?php echo $product->warrantyEndDate ? $this->mydatesystem->restoreDate('-',$product->warrantyEndDate) : ''; ?>" placeholder="ถึง">
                        </div>
                        <div class="form-group">
                            <label>ผู้รับผิดชอบ</label>
                            <input id="txt_responseUser" class="form-control" name="txt_responseUser" value="<?php echo $product->responseUser ? : ''; ?>" placeholder="ผู้รับผิดชอบ">
                        </div>
                        <div class="form-group">
                            <label>แผนกผู้รับผิดชอบ</label>
                            <select id="txt_department" class="form-control" name="txt_department">
                                <option value="">เลือกแผนกผู้รับผิดชอบ</option>
                            <?php foreach($departmentResult as $departmentData): ?>
                                <option value="<?php echo $departmentData->department_id; ?>" <?php echo $departmentData->department_id == $product->responseDepartment ? "selected" : ''; ?>><?php echo $departmentData->departmentName; ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>ที่เก็บ/สถานที่ตั้งทรัพย์สิน</label>
                            <input id="txt_location" class="form-control" name="txt_location" value="<?php echo $product->locationStorage ? : ''; ?>" placeholder="ที่เก็บ/สถานที่ตั้งทรัพย์สิน">
                        </div>
                        <div class="form-group">
                            <label>หมายเหตุ</label>
                            <textarea id="txt_remark" class="form-control" value="<?php echo $product->remark ? : ''; ?>" name="txt_remark"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" name="asset_edit" value="บันทึกข้อมูล">
                            <input type="button" class="btn btn-default" value="ยกเลิก" onClick="window.location.href = '<?php echo base_url('product/listing'); ?>';">
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
