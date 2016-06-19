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
			<table id="dataTables" class="table table-bordered table-hover">
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
				<tbody>
				<?php if($products): ?>
					<?php foreach($products as $product): ?>
						<tr>
							<td><?php echo $product->code; ?></td>
							<td><?php echo $product->detail; ?></td>
							<td>
								<?php if($product->soldDate != '0000-00-00'): ?>
									<?php echo $this->mydatesystem->Thaidate($product->soldDate, 2); ?></td>
								<?php else: ?>
									<?php echo "-"; ?>
								<?php endif ?>
							<td>
								<?php if($product->warrantyStartDate != '0000-00-00' && $product->warrantyEndDate != '0000-00-00'): ?>
									<?php echo $this->mydatesystem->Thaidate($product->warrantyStartDate, 2) . ' - ' . $this->mydatesystem->ThaiDate($product->warrantyEndDate, 2); ?></td>
								<?php else: ?>
									<?php echo "-"; ?>
								<?php endif ?>
							</td>
							<td>
								<?php if(!empty($product->value)): ?>
									<?php echo number_format($product->value); ?>
								<?php else: ?>
									<?php echo "-"; ?>
								<?php endif ?>
							</td>
							<td>
								<?php if($product->status == 1): ?>
									<?php echo "<span class='label label-success'>" . $product->statusName . "</span>"; ?>
								<?php else: ?>
									<?php echo "<span class='label label-danger'>" . $product->statusName . "</span>"; ?>
								<?php endif ?>
							</td>
							<td>
								<?php if(!empty($product->remark)): ?>
									<?php echo $product->remark; ?>
								<?php else: ?>
									<?php echo "-"; ?>
								<?php endif ?>
							</td>
							<td align="center">
								<?php if($product->IsApproved == 1): ?>
									<a href="<?php echo base_url('product/verify/' . $product->id ); ?>" class="btn btn-success btn-xs">
										<i class="fa fa-check"></i>
									</a>
								<?php else: ?>
									<a href="<?php echo base_url('product/verify/' . $product->id ); ?>" class="btn btn-danger btn-xs">
									<i class="fa fa-exclamation-triangle"></i>
								</a>
								<?php endif ?>
								<a href="<?php echo base_url('product/view/' . $product->id ); ?>" class="btn btn-info btn-xs">
									<i class="fa fa-eye"></i>
								</a>
								<a href="<?php echo base_url('product/edit/' . $product->id ); ?>" class="btn btn-warning btn-xs">
									<i class="fa fa-pencil"></i>
								</a>
								<a href="<?php echo base_url('product/del/' . $product->id) ;?>" onClick="return confirm('คุณต้องการลบข้อมูลนี้?');" class="btn btn-danger btn-xs">
									<i class="fa fa-trash"></i>
								</a>
							</td>
						</tr>
					<?php endforeach ?>
				<?php endif ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<!-- /.row -->
