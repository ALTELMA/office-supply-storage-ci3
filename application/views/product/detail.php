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
        <div class="text-right">
            <a href="<?php echo base_url('product/attach/add/' . $product->id);?>" class="btn btn-primary">
                <i class="fa fa-plus"></i> เพิ่มไฟล์
            </a>
            <a href="<?php echo base_url('product/edit/' . $product->id);?>" class="btn btn-warning">
                <i class="fa fa-pencil"></i> แก้ไขข้อมูล
            </a>
            <a href="<?php echo base_url('product');?>" class="btn btn-default">
                <i class="fa fa-chevron-left"></i> ย้อนกลับ
            </a>
        </div>
    </div>
</div>

<br>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">รายละเอียดทรัพย์สิน</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php
                            if(!empty($product->assetFullPic)) {
                                $assetImage = [
                                    'src' => base_url('assets/img/asset_image/resize/' . $product->assetFullPic),
                                    'class' => 'img-thumbnail img-responsive',
                                    'width' => 400,
                                    'height' => 400,
                                    'alt' => $product->code,
                                ];
                            } else {
                                $assetImage = [
                                    'src' => base_url('assets/images/templates/no_image.gif'),
                                    'class' => 'img-thumbnail img-responsive',
                                    'width' => 400,
                                    'height' => 400,
                                    'alt' => $product->code,
                                ];
                            }
                            echo img($assetImage);
                        ?>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tbody>
                                <?php if(!empty($product->code)): ?>
                                <tr>
                                    <td><label>รหัสครุภัณฑ์: </label></td>
                                    <td><?php echo $product->code; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($product->detail)): ?>
                                <tr>
                                    <td><label>รายละเอียดครุภัณฑ์: </label></td>
                                    <td><?php echo $product->detail; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($product->responseUser)): ?>
                                <tr>
                                    <td><label>ผู้รับผิดชอบ: </label></td>
                                    <td><?php echo $product->responseUser; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($product->departmentName)): ?>
                                <tr>
                                    <td><label>แผนกผู้รับผิดชอบ: </label></td>
                                    <td><?php echo $product->departmentName; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($product->locationStorage)): ?>
                                <tr>
                                    <td><label>ที่เก็บ/สถานที่ตั้งทรัพย์สิน: </label></td>
                                    <td><?php echo $product->locationStorage; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($product->soldDate != '0000-00-00'): ?>
                                <tr>
                                    <td><label>ที่เก็บ/สถานที่ตั้งทรัพย์สิน: </label></td>
                                    <td><?php echo $this->mydatesystem->Thaidate($product->soldDate, 2); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($product->value)): ?>
                                <tr>
                                    <td><label>ราคา: </label></td>
                                    <td><?php echo number_format($product->value); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($product->warrantyStartDate != '0000-00-00' && $product->warrantyEndDate != '0000-00-00'): ?>
                                <tr>
                                    <td><label>วันที่รับประกัน: </label></td>
                                    <td><?php echo $this->mydatesystem->Thaidate($product->warrantyStartDate, 1) . ' - ' . $this->mydatesystem->Thaidate($product->warrantyEndDate, 2); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($product->UpdateDate != '0000-00-00'): ?>
                                <tr>
                                    <td><label>วันที่แก้ไขข้อมูลล่าสุด: </label></td>
                                    <td><?php echo $this->mydatesystem->Thaidate($product->UpdateDate, 1); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($product->status)): ?>
                                <tr>
                                    <td><label>สถานะ: </label></td>
                                    <td>
                                        <?php if($product->status == 1): ?>
                                            <?php echo "<span class='label label-success'>" . $product->statusName . "</span>"; ?>
                                        <?php else: ?>
                                            <?php echo "<span class='label label-danger'>" . $product->statusName . "</span>"; ?>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if(!empty($product->remark)): ?>
                                <tr>
                                    <td><label>หมายเหตุ: </label></td>
                                    <td><?php echo $product->remark; ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
        <div class="panel-heading">ไฟล์แนบ</div>
            <div class="panel-body">
                <table id="dataTables" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ชื่อไฟล์</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($attachList > 0): ?>
                            <?php foreach($attachList as $attach): ?>
                            <tr>
                                <td><a href="<?php echo base_url('product/upload/' . $product->id);?>"><?php echo $attach->fileName; ?></a></td>
                                <td width="10%" class="text-center">
                                    <a href="<?php echo base_url('product/attach/edit/' . $attach->id ); ?>" class="btn btn-warning btn-xs">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a href="<?php echo base_url('product/attach/del/' . $attach->id) ;?>" onClick="return confirm('คุณต้องการลบข้อมูลนี้?');" class="btn btn-danger btn-xs">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center">ไม่มีข้อมูล</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
