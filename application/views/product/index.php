<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            รายการทรัพย์สิน
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> <a href="<?php echo base_url('dashboard/index'); ?>">แดชบอร์ด</a>
            </li>
            <li class="active">
                <i class="fa fa-cube"></i> รายการทรัพย์สิน
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->

<div class="row">
	<div class="col-md-12 text-right">
		<a href="<?php echo base_url('product/add'); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> เพิ่มข้อมูล</a>
	</div>
</div>

<br>

<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table id="productDataTable" class="table table-bordered table-hover">
				<thead>
					<tr>
						<th>รหัส</th>
						<th>รายละเอียด</th>
						<th>วันที่จัดซื้อ</th>
						<th>วันที่รับประกัน</th>
						<th>ราคา</th>
						<th>สถานะ</th>
						<th>หมายเหตุ</th>
						<th width="10%"></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
<!-- /.row -->
