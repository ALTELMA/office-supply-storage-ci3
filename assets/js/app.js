// JavaScript Document

$(document).ready(function(){

	// =======================================================================
	// COMMON FUNCTION
	// =======================================================================
	var getFullURL = window.location.href;
	var getURLSplit = getFullURL.split('/');
	var getRootURL = getURLSplit[0]+'/'+getURLSplit[1]+'/'+getURLSplit[2]+'/';

	// =======================================================================
	// DATEPICKER
	// =======================================================================

	// INITIAL FUNCTION
	$('input[type=text].dateSelect').datepicker({
		changeMonth: true,
		changeYear: true
	});

	// WARRNTY DURATION SELECT
	$('#warrantyFrom').datepicker({
		changeMonth: true,
		changeYear: true,
		numberOfMonths: 3,
		onClose: function( selectedDate ) {
			$("#warrantyTo").datepicker("option", "minDate", selectedDate);
		}
	});
	// END
	$('#warrantyTo').datepicker({
		changeMonth: true,
		changeYear: true,
		numberOfMonths: 3,
		onClose: function( selectedDate ) {
			$("#warrantyFrom").datepicker("option", "maxDate", selectedDate);
		}
	});


	// ASSET CHECK CODE
	$('#txt_code').change(function(){

		var code1 = $(this).val();
		var code2 = $('#getAssetID').val();
		if(code2 == null){code2 = '';}

		$.ajax({
			url: getRootURL+'OfficeEquipmentManager/asset/ajax',
			data: 'req=chkCode&code1='+code1+'&code2='+code2,
			cache: false,
			type: 'POST',
			success: function(res){
				if(res > 0){
					$('#existCode').html('รหัสทรัพย์สินซ้ำ');
					$("input[type=submit]").attr("disabled", true);
				}else{
					$('#existCode').html('');
					$("input[type=submit]").attr("disabled", false);
				}
			}
		});
	});

	// ==========================================================================
	// ASSET SELECT CATEGORY
	// ==========================================================================
	$('#assetCat').change(function(){

		var cat_id = $(this).val();

		$.ajax({
			url: getRootURL+'OfficeEquipmentManager/asset/ajax',
			data: 'req=subCat&cat_id='+cat_id,
			cache: false,
			type: 'POST',
			success: function(res){
				$('#assetSubCat').html(res);
			}
		});
	});
});