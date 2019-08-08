<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">ผู้ใช้งานระบบ</h1>
        <ol class="breadcrumb">
            <li class="active">
                <i class="fa fa-dashboard"></i> <a href="<?php echo base_url('dashboard/index'); ?>">แดชบอร์ด</a>
            </li>
            <li class="active">
                <i class="fa fa-cube"></i> ผู้ใช้งานระบบ
            </li>
        </ol>
    </div>
</div>
<!-- /.row -->

<div class="row">
	<div class="col-md-12 text-right">
		<a href="<?php echo base_url('user/add'); ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> เพิ่มผู้ใช้</a>
	</div>
</div>

<br>

<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table id="dataTables" class="table table-bordered table-hover">
				<thead>
					<tr>
                        <th>#</th>
						<th>ชื่อผู้ใช้</th>
						<th>ชื่อ-นามสกุล</th>
						<th>วันที่ล็อคอินล่าสุด</th>
                        <th>วันที่สร้าง</th>
						<th width="10%"></th>
					</tr>
				</thead>
				<tbody>
				<?php if($users): ?>
					<?php foreach($users as $user): ?>
						<tr>
							<td><?php echo $user->user_id; ?></td>
							<td><?php echo $user->username; ?></td>
                            <td><?php echo $user->name; ?></td>
                            <td><?php echo $user->registDate; ?></td>
                            <td><?php echo $user->lastLoginDate; ?></td>
							<td align="center">
								<?php if($user->IsApproved == 1): ?>
									<a href="<?php echo base_url('user/verify/' . $user->user_id ); ?>" class="btn btn-success btn-xs">
										<i class="fa fa-check"></i>
									</a>
								<?php else: ?>
									<a href="<?php echo base_url('user/verify/' . $user->user_id ); ?>" class="btn btn-danger btn-xs">
									<i class="fa fa-exclamation-triangle"></i>
								</a>
								<?php endif ?>
								<a href="<?php echo base_url('user/view/' . $user->user_id ); ?>" class="btn btn-info btn-xs">
									<i class="fa fa-eye"></i>
								</a>
								<a href="<?php echo base_url('user/edit/' . $user->user_id ); ?>" class="btn btn-warning btn-xs">
									<i class="fa fa-pencil"></i>
								</a>
								<a href="<?php echo base_url('user/delete/' . $user->user_id) ;?>" onClick="return confirm('คุณต้องการลบข้อมูลนี้?');" class="btn btn-danger btn-xs">
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
