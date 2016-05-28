	</div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script>
	var	base_url = "<?php echo base_url(); ?>";
</script>
<?php

$scripts = [
	'jquery'              => base_url('assets/js/jquery.min.js'),
	'jquery-ui'           => base_url('assets/js/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js'),
	'datepicker'          => base_url('assets/js/jquery-ui-1.10.2.custom/development-bundle/ui/jquery.ui.datepicker.js'),
	'datepicker-lang-th'  => base_url('assets/js/jquery-ui-1.10.2.custom/development-bundle/ui/i18n/jquery.ui.datepicker-th.js'),
	'bootstrap'           => base_url('assets/js/bootstrap.min.js'),
	'metisMenu'           => base_url('assets/js/metisMenu.min.js'),
	'sb-admin-2'          => base_url('assets/js/sb-admin-2.js'),
	'raphael'             => base_url('assets/js/raphael.min.js'),
	'datatable'           => base_url('assets/js/jquery.dataTables.min.js'),
	'datatable-bootstrap' => base_url('assets/js/dataTables.bootstrap.min.js'),
	'morris'              => base_url('assets/js/morris.min.js'),
	'app'                 => base_url('assets/js/app.js'),
];

foreach ($scripts as $script) {
	echo "<script src='" . $script . "'></script>";
}

?>

<script>
	$(document).ready(function() {
		$('#dataTables').DataTable({
			responsive: true
		});
	});
</script>