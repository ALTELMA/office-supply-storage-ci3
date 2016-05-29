<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $title; ?></h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> Dashboard
            </li>
            <li class="active">
                <i class="fa fa-cubes"></i> <?php echo $title; ?>
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->

<div class="row">
	<div class="col-md-12 text-right">
		<a href="<?php echo base_url('product/subcategory/add'); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> เพิ่มข้อมูล</a>
	</div>
</div>

<br>

<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table id="dataTables" class="table table-bordered table-hover">
				<thead>
					<tr>
						<th>ชื่อหมวดหมู่ทรัพย์สิน</th>
						<th width="10%"></th>
					</tr>
				</thead>
				<tbody>
				<?php if($categories): ?>
					<?php foreach($categories as $category): ?>
						<tr>
							<td><?php echo $category->subTypeName; ?></td>
							<td align="center">
								<a href="<?php echo base_url('product/subcategory/edit/' . $category->id ); ?>" class="btn btn-warning btn-xs">
									<i class="fa fa-pencil"></i>
								</a>
								<a href="<?php echo base_url('product/subcategory/del/' . $category->id) ;?>" onClick="return confirm('คุณต้องการลบข้อมูลนี้?');" class="btn btn-danger btn-xs">
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