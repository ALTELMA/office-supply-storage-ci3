<?php if(!defined('BASEPATH'))exit('No direct script access allowed');

class Product extends MY_Controller{

	public function __construct(){

		parent::__construct();

		// LOAD MODEL
		$this->load->model('userModel', '', TRUE);
		$this->load->model('productModel', '', TRUE);

		// LOAD LIBRARY
		$this->load->library('MyUpload');
		$this->load->library('pagination');
		$this->load->library('MyDateSystem');
		$this->load->library('MyExcel');
	}

	public function index()
	{
		if(!$this->session->userdata('userLogData')) {
			$this->data['title'] = 'ระบบฐานข้อมูลครุภัณฑ์และทรัพย์สินในสำนักงาน';
			$this->content = 'login';
			$this->layout('full-width-no-header');
		} else {
			redirect('product/listing', 'refresh');
		}
	}

	public function listing()
	{
		if(!$this->session->userdata('userLogData'))
			redirect('', 'refresh');

		// GET KEYWORD FROM SEARCH PANEL AND ADD TO SESSION
		if($this->input->post('searchSubmit') != NULL) {
			$search_sess = [
				'category_id'     => $this->input->post('assetCat'),
				'sub_category_id' => $this->input->post('assetSubCat'),
				'keyword'         => $this->input->post('txt_search')
			];
			$this->session->set_userdata('searchData',$search_sess);
		}

		// LOAD SESSION DATA
		$session_data = $this->session->userdata('userLogData');
		$session_search = $this->session->userdata('searchData');

		// SET KEY FOR QUERY DATA SEARCH
		$key = [$session_search['category_id'], $session_search['sub_category_id'], $session_search['keyword']];

		// LOAD DATA
		$categoryList = $this->productModel->getDataList('category');
		$cond = array('cat_id' => $session_search['category_id']); // SubCatCondition
		$subCategoryList = $this->productModel->getDataList('sub_category', $cond);

		// QUERY DATABASE FROM SEARCH
		$products = $this->productModel->getAssetDataList($key);

		$this->data['title'] = 'รายชื่อทรัพย์สินและครุภัณฑ์';
		$this->data['userID'] = $session_data['user_id'];
		$this->data['name'] = $session_data['name'];
		$this->data['categoryResult'] = $categoryList;
		$this->data['subCategoryResult'] = $subCategoryList;
		$this->data['products'] = $products;
		$this->data['cat_id'] = $session_search['category_id'];
		$this->data['subCat_id'] = $session_search['sub_category_id'];
		$this->data['keyword'] = $session_search['keyword'];

		$this->content = 'product/list';
		$this->layout();
	}

	// VIEW ASSET DETAIL
	public function view($id = NULL)
	{
		// LOAD SESSION DATA
		$session_data = $this->session->userdata('userLogData');

		// LOAD ASSET DATA AND SEND TO PAGE
		$assetData = $this->assetModel->getAssetRow($id);
		$cond = array('asset_id' => $id);
		$attachObj = $this->assetModel->getDataList('asset_attachment',$cond);

		$data['title'] = 'รายละเอียดข้อมูลคุรภัณฑ์';
		$data['userID'] = $session_data['user_id'];
		$data['name'] = $session_data['name'];
		$data['assetData'] = $assetData;
		$data['attachList'] = $attachObj;

		// LOAD PAGE
		$this->load->view('templates/header', $data);
		$this->load->view('templates/userPanel', $data);
		$this->load->view('asset_detail', $data);
		$this->load->view('templates/footer', $data);
	}

	// ADD ASSET DATA
	public function add()
	{
		// LOAD SESSION DATA
		$session_data = $this->session->userdata('userLogData');

		// LOAD DATA FROM DATABASE
		$categoryList = $this->productModel->getDataList('category');
		$cond = array('cat_id' => 1); // SubCatCondition
		$subCategoryList = $this->productModel->getDataList('sub_category', $cond);
		$statusList = $this->productModel->getDataList('asset_status');
		$departmentList = $this->productModel->getDataList('department');

		$thumb = $resize = '';
		if($this->input->post('asset_add') != null) {

			// CONFIG DESTINATION PATH
			$thumbPath = str_replace(SELF,'',FCPATH).'assets/images/asset_image/thumb';
			$resizePath = str_replace(SELF,'',FCPATH).'assets/images/asset_image/resize';
			$thumbName = 'asset_cover_thumb'.date('YmdHis');
			$resizeName = 'asset_cover_resize'.date('YmdHis');

			// THUMB IMAGE 100x100
			if (isset($_FILES['asset_img'])) {
				if($_FILES['asset_img']['tmp_name']) {
					$thumb = $this->myupload->imgUploadRatioY($_FILES['asset_img'], $thumbPath, $thumbName, 'gif', 100);
				} else {
					$thumb = '';
					$this->myupload->error = '';
				}

				// RESIZE IMAGE 400x400
				if($_FILES['asset_img']['tmp_name']) {
					$resize = $this->myupload->imgUploadRatioY($_FILES['asset_img'], $resizePath, $resizeName, 'gif', 400);
				} else {
					$resize = '';
					$this->myupload->error = '';
				}
			}

			// INSERT DATA
			$this->productModel->assetAdd($thumb,$resize);
			redirect('product/listing', 'refresh');
		}

		$this->data['title'] = 'เพิ่มข้อมูลทรัพย์สินและครุภัณฑ์';
		$this->data['userID'] = $session_data['user_id'];
		$this->data['name'] = $session_data['name'];
		$this->data['categoryResult'] = $categoryList;
		$this->data['subCategoryResult'] = $subCategoryList;
		$this->data['statusResult'] = $statusList;
		$this->data['departmentResult'] = $departmentList;

		$this->content = 'product/add';
		$this->layout();
	}

	// ASSET EDIT DATA
	public function edit($id = null)
	{
		if(!empty($id)) {

			// LOAD SESSION DATA
			$session_data = $this->session->userdata('userLogData');

			// LOAD DATA FROM DATABASE
			$product = $this->productModel->getAssetRow($id);
			$categoryList = $this->productModel->getDataList('category');
			$cond = array('cat_id' => $product->cat_id); // SubCatCondition
			$subCategoryList = $this->productModel->getDataList('sub_category',$cond);
			$statusList = $this->productModel->getDataList('asset_status');
			$departmentList = $this->productModel->getDataList('department');

			// CONFIG DATA SEND TO PAGE
			$this->data['title'] = 'แก้ไขข้อมูลทรัพย์สินและครุภัณฑ์';
			$this->data['userID'] = $session_data['user_id'];
			$this->data['name'] = $session_data['name'];
			$this->data['categoryResult'] = $categoryList;
			$this->data['subCategoryResult'] = $subCategoryList;
			$this->data['statusResult'] = $statusList;
			$this->data['departmentResult'] = $departmentList;
			$this->data['product'] = $product;

			// DO ACTION EDIT WHEN SUBMIT
			if($this->input->post('asset_edit') != null) {
				$thumb = $resize = '';
				if(!empty($_FILES['asset_img']['tmp_name'])) {
					// CONFIG DESTINATION PATH
					$thumbPath = str_replace(SELF,'',FCPATH).'assets/images/asset_image/thumb';
					$resizePath = str_replace(SELF,'',FCPATH).'assets/images/asset_image/resize';
					$thumbName = 'asset_cover_thumb'.date('YmdHis');
					$resizeName = 'asset_cover_resize'.date('YmdHis');

					// THUMB IMAGE 100x100
					if($_FILES['asset_img']['tmp_name']) {
						@unlink($thumbPath.$product->assetThumbPic);
						$thumb = $this->myupload->imgUploadRatioY($_FILES['asset_img'], $thumbPath, $thumbName, 'gif', 100);
					} else {
						$thumb = '';
						$this->myupload->error = '';
					}

					// RESIZE IMAGE 400x400
					if($_FILES['asset_img']['tmp_name']) {
						@unlink($resizePath.$product->assetFullPic);
						$resize = $this->myupload->imgUploadRatioY($_FILES['asset_img'], $resizePath, $resizeName, 'gif', 400);
					} else {
						$resize = '';
						$this->myupload->error = '';
					}
				} else {
					$thumb = $product->assetThumbPic;
					$resize = $product->assetFullPic;
				}

				$this->productModel->assetUpdate($product->id, $thumb, $resize);
				redirect('product/listing', 'refresh');
			}

			// LOAD PAGE
			$this->content = 'product/edit';
			$this->layout();
		} else {
			redirect('product/listing', 'refresh');
		}
	}

	// ASSET UPDATE APPROVE DATA
	public function verify($id){

		if(!empty($id)){
			// LOAD DATA
			$assetObj = $this->productModel->getDataRow('asset','id', $id);
			$approveData = empty($assetObj->IsApproved)? 1 : 0 ;

			$updateData = array('IsApproved' => $approveData);
			$this->productModel->updateData('asset', $updateData, 'id', $id);
			redirect('product/listing','refresh');
		}else{
			redirect('product/listing','refresh');
		}
	}

	// ASSET DELETE DATA
	public function del($id){

		if(!empty($id))
		{
			$this->productModel->assetDelete($id);
			redirect('product/listing', 'refresh');
		}else{
			redirect('product/listing', 'refresh');
		}
	}

	// ASSETS MAIN CATEGORY LIST
	public function category($page = NULL, $id = NULL)
	{
		// LOAD SESSION DATA
		$session_data = $this->session->userdata('userLogData');

		if($page == NULL || $page == 'list') {

			// LOAD DATA FROM DATABASE
			$categories = $this->productModel->getDataList('category');

			$this->data['title'] = 'ประเภทของข้อมูลทรัพย์สินและครุภัณฑ์';
			$this->data['userID'] = $session_data['user_id'];
			$this->data['name'] = $session_data['name'];
			$this->data['categories'] = $categories;

			$this->content = 'category/list';
			$this->layout();

		} elseif($page == 'view') {

			if(!empty($id)){

				// CONFIG PAGINATION
				$cond = array('cat_id' => $id);

				// LOAD DATA
				$categoryObj = $this->productModel->getDataRow('category', 'cat_id', $id);
				$subCatList = $this->productModel->getDataList('sub_category', $cond);

				$data['title'] = 'รายละเอียดประเภทครุภัณฑ์';
				$data['userID'] = $session_data['user_id'];
				$data['name'] = $session_data['name'];
				$data['categoryData'] = $categoryObj;
				$data['subCategoryResult'] = $subCatList;

				$this->load->view('templates/header', $data);
				$this->load->view('templates/userPanel', $data);
				$this->load->view('asset_cat_detail', $data);
				$this->load->view('templates/footer', $data);
			}

		} elseif($page == 'add') {

			if($this->input->post('submit')){
				$this->productModel->categoryAdd();
				redirect('product/category');
			}

			// CONFIG DATA SEND TO VIEW
			$data['title'] = 'เพิ่มข้อมูลประเภทของทรัพย์สินและครุภัณฑ์';
			$data['userID'] = $session_data['user_id'];
			$data['name'] = $session_data['name'];

			$this->load->view('templates/header', $data);
			$this->load->view('templates/userPanel', $data);
			$this->load->view('asset_cat_add', $data);
			$this->load->view('templates/footer', $data);

		// EDIT ASSET CATEGORY
		} elseif($page == 'edit') {

			if(!empty($id)){

				// LOAD CATEGORY DATA
				$categoryObj = $this->productModel->getDataRow('category', 'cat_id', $id);

				if($this->input->post('submit')){
					$this->productModel->categoryUpdate($id);
					redirect('product/category');
				}

				// CONFIG DATA SEND TO VIEW
				$data['title'] = 'เพิ่มข้อมูลประเภทของทรัพย์สินและครุภัณฑ์';
				$data['userID'] = $session_data['user_id'];
				$data['name'] = $session_data['name'];
				$data['categoryData'] = $categoryObj;

				$this->load->view('templates/header', $data);
				$this->load->view('templates/userPanel', $data);
				$this->load->view('asset_cat_edit', $data);
				$this->load->view('templates/footer', $data);
				}

		// DEL ASSET CATEGORY
		} elseif($page == 'del') {

			if(!empty($id)){
				$this->productModel->delDataRow('category', 'cat_id', $id);
				redirect('product/category', 'refresh');
			}
		}
	}

	// ASSET SUB CATEGORY
	public function subCategory($page = NULL, $id = NULL){

		// LOAD SESSION DATA
		$session_data = $this->session->userdata('userLogData');

		if($page == 'add'){
			if($this->input->post('submit')){
				$this->assetModel->subCategoryAdd($id);
				redirect('asset/category/view/'.$id);
			}

			// CONFIG DATA SEND TO VIEW
			$data['title'] = 'เพิ่มข้อมูลประเภทย่อยของทรัพย์สินและครุภัณฑ์';
			$data['userID'] = $session_data['user_id'];
			$data['name'] = $session_data['name'];

			$this->load->view('templates/header', $data);
			$this->load->view('templates/userPanel', $data);
			$this->load->view('asset_subcat_add', $data);
			$this->load->view('templates/footer', $data);

		// EDIT ASSET SUB CATEGORY DATA
		}elseif($page == 'edit'){

			if(!empty($id)){

				// LOAD ASSET SUB CATEGORY DATA
				$subCategoryObj = $this->assetModel->getDataRow('sub_category','id',$id);

				// EDIT ASSET SUB CATEGORY DATA
				if($this->input->post('submit')){
					$this->assetModel->subCategoryUpdate($id);
					redirect('asset/category/view/'.$subCategoryObj->cat_id);
				}

				// CONFIG DATA SEND TO VIEW
				$data['title'] = 'เพิ่มข้อมูลประเภทย่อยของทรัพย์สินและครุภัณฑ์';
				$data['userID'] = $session_data['user_id'];
				$data['name'] = $session_data['name'];
				$data['subCategoryData'] = $subCategoryObj;

				$this->load->view('templates/header', $data);
				$this->load->view('templates/userPanel', $data);
				$this->load->view('asset_subcat_edit', $data);
				$this->load->view('templates/footer', $data);
			}

		// DELETE ASSET SUB CATEGORY DATA
		}elseif($page == 'del'){

			if(!empty($id)){
				// LOAD ASSET SUB CATEGORY DATA
				$subCategoryObj = $this->assetModel->getDataRow('sub_category','id',$id);
				$this->assetModel->delDataRow('sub_category','id', $id);
				redirect('asset/category/view/'.$subCategoryObj->cat_id, 'refresh');
			}
			redirect('asset/category/', 'refresh');
		}
	}

	// ASSET ATTACH FILE
	public function attach($page = NULL, $id = NULL){

		if($page == 'add'){
			if(!empty($id)){

				// LOAD SESSION DATA
				$session_data = $this->session->userdata('userLogData');

				if($this->input->post('submit') != NULL){

					if($_FILES['uploadFile']['tmp_name']){

						$Path = str_replace(SELF,'',FCPATH).'assets/upload';
						$fileName = rand(000000,999999).date('YmdHis');
						$attach = $this->myupload->uploadFile($_FILES['uploadFile'],$Path,$fileName);
					}else{
						$attach = '';
					}

					$this->assetModel->assetAttachAdd($attach);
					redirect('asset/view/'.$id,'refresh');
				}

				// CONFIG DATA SEND TO PAGE
				$data['title'] = 'เพิ่มไฟล์สำหรับครุภัณฑ์';
				$data['userID'] = $session_data['user_id'];
				$data['name'] = $session_data['name'];

				// LOAD PAGE
				$this->load->view('templates/header', $data);
				$this->load->view('templates/userPanel', $data);
				$this->load->view('asset_attach_add', $data);
				$this->load->view('templates/footer', $data);
			}else{
				redirect('asset','refresh');
			}

		// ATTACH EDIT
		}elseif($page == 'edit'){
			if(!empty($id)){

				// LOAD SESSION DATA
				$session_data = $this->session->userdata('userLogData');

				// LOAD ASSET ATTACH DATA
				$attachObj = $this->assetModel->getDataRow('asset_attachment','id',$id);

				if($this->input->post('submit') != NULL){
					if($_FILES['uploadFile']['tmp_name']){
						$Path = str_replace(SELF,'',FCPATH).'assets/upload';
						$fileName = rand(000000,999999).date('YmdHis');
						$attach = $this->myupload->uploadFile($_FILES['uploadFile'],$Path,$fileName);
					}else{
						$attach = $attachObj->filePath;
					}

					$this->assetModel->assetAttachUpdate($id,$attachObj->asset_id,$attach);
					redirect('asset/view/'.$attachObj->asset_id,'refresh');
				}

				// CONFIG DATA SEND TO PAGE
				$data['title'] = 'แก้ไขไฟล์สำหรับครุภัณฑ์';
				$data['userID'] = $session_data['user_id'];
				$data['name'] = $session_data['name'];
				$data['attachData'] = $attachObj;

				// LOAD PAGE
				$this->load->view('templates/header', $data);
				$this->load->view('templates/userPanel', $data);
				$this->load->view('asset_attach_edit', $data);
				$this->load->view('templates/footer', $data);
			}else{
				redirect('asset','refresh');
			}
		}elseif($page == 'del'){
			$this->assetModel->assetAttachDel($id);
		}
	}

	public function report(){

		// LOAD SESSION DATA
		$session_data = $this->session->userdata('userLogData');
		$session_search = $this->session->userdata('searchData');

		$key = array($session_search['category_id'],$session_search['sub_category_id'],$session_search['keyword']);

		// LOAD DATA
		$assetObj = $this->assetModel->getAssetReportList($key);

		$this->myexcel->setActiveSheetIndex(0);

		// HEADER
		$this->myexcel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
		$this->myexcel->getActiveSheet()->setCellValue('B1', 'หมวด');
		$this->myexcel->getActiveSheet()->setCellValue('C1', 'ประเภทหลัก');
		$this->myexcel->getActiveSheet()->setCellValue('D1', 'ประเภทย่อย');
		$this->myexcel->getActiveSheet()->setCellValue('E1', 'รายละเอียด');
		$this->myexcel->getActiveSheet()->setCellValue('F1', 'รหัสครุภัณฑ์');
		$this->myexcel->getActiveSheet()->setCellValue('G1', 'ราคา');
		$this->myexcel->getActiveSheet()->setCellValue('H1', 'วันที่จัดซื้อ');
		$this->myexcel->getActiveSheet()->setCellValue('I1', 'วันที่เริ่มประกัน');
		$this->myexcel->getActiveSheet()->setCellValue('J1', 'วันที่หมดประกัน');
		$this->myexcel->getActiveSheet()->setCellValue('K1', 'ผู้รับผิดชอบ');
		$this->myexcel->getActiveSheet()->setCellValue('L1', 'แผนกที่รับผิดชอบ');
		$this->myexcel->getActiveSheet()->setCellValue('M1', 'สถานที่จัดเก็บ');
		$this->myexcel->getActiveSheet()->setCellValue('N1', 'สถานะ');
		$this->myexcel->getActiveSheet()->setCellValue('O1', 'หมายเหตุ');

		// SET COLUMN WIDTH
		$this->myexcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$this->myexcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$this->myexcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$this->myexcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$this->myexcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);

		// SET ALIGNMENT
		$this->myexcel->getActiveSheet()->getStyle('A1:O1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$i = 2;
		foreach($assetObj as $assetList) {

			// CONFIG SOLD DATE
			$txtSoldDate = $assetList->soldDate > '0000-00-00 00:00:00'?$this->mydatesystem->thaiDate($assetList->soldDate, 1):'';

			// CONFIG WARRNTY DATE
			$txtStartDate = $assetList->startDate > '0000-00-00'?$this->mydatesystem->thaiDate($assetList->startDate, 1):'';
			$txtEndDate = $assetList->endDate > '0000-00-00'?$this->mydatesystem->thaiDate($assetList->endDate, 1):'';
			$value = !empty($assetList->value)?$assetList->value:0;

			$this->myexcel->getActiveSheet()->setCellValue('A' . $i, $assetList->asset_id);
			$this->myexcel->getActiveSheet()->setCellValue('B' . $i, $assetList->catType);
			$this->myexcel->getActiveSheet()->setCellValue('C' . $i, $assetList->catName);
			$this->myexcel->getActiveSheet()->setCellValue('D' . $i, $assetList->subTypeName);
			$this->myexcel->getActiveSheet()->setCellValue('E' . $i, $assetList->detail);
			$this->myexcel->getActiveSheet()->setCellValue('F' . $i, $assetList->code);
			$this->myexcel->getActiveSheet()->setCellValue('G' . $i, number_format($value,2));
			$this->myexcel->getActiveSheet()->setCellValue('H' . $i, $txtSoldDate);
			$this->myexcel->getActiveSheet()->setCellValue('I' . $i, $txtStartDate);
			$this->myexcel->getActiveSheet()->setCellValue('J' . $i, $txtEndDate);
			$this->myexcel->getActiveSheet()->setCellValue('K' . $i, $assetList->owner);
			$this->myexcel->getActiveSheet()->setCellValue('L' . $i, $assetList->depName);
			$this->myexcel->getActiveSheet()->setCellValue('M' . $i, $assetList->location);
			$this->myexcel->getActiveSheet()->setCellValue('N' . $i, $assetList->statName);
			$this->myexcel->getActiveSheet()->setCellValue('O' . $i, $assetList->remark);

			$this->myexcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->myexcel->getActiveSheet()->getStyle('D'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$i++;
		}

		// RENAME WORKSHEET
		$this->myexcel->getActiveSheet()->setTitle('dpAssetReport');

		// Redirect output to a client’s web browser (Excel2007)
		$filename = date('YmdHis').rand(000000,999999);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($this->myexcel,'Excel5');
		$objWriter->save('php://output');

		exit;
	}

	// ==================================================================
	//  AJAX FUNCTION TO LOAD DYNAMIC DATA TO PAGE
	// ==================================================================
	public function ajax(){

		$callData = $this->input->post('req');

		// CATEGORY AND SUB CATEGORY
		if($callData == 'subCat'){

			// LOAD SESSION
			$session_data = $this->session->userdata('searchData');
			$cat_id = $session_data['category_id'];

			$cat_id = $this->input->post('cat_id');
			$cond = array('cat_id' => $cat_id);
			$subCategoryList = $this->productModel->getDataList('sub_category',$cond);

			if($cat_id != 0){
				echo '<option value="">เลือกประเภทย่อยของทรัพย์สิน</option>';
				foreach($subCategoryList as $subCategoryData){
					echo '<option value=\''.$subCategoryData->id.'\'>'.$subCategoryData->subTypeName.'</option>';
				}
			}else{
				echo '<option value="">เลือกประเภทย่อยของทรัพย์สิน</option>';
			}
		}elseif($callData == 'chkCode'){

			// COUNT DATA AND CHECK IT
			$code1 = $this->input->post('code1') != NULL?$this->input->post('code1'):'';
			$code2 = $this->input->post('code2') != NULL?$this->input->post('code2'):'';
			$chkData = $this->productModel->getAssetCheck($code1,$code2);

			echo $chkData;
		}
	}

	// ==================================================================
	//  END AJAX PROCESS
	// ==================================================================

}

/* End of file main.php */
/* Location : ./application/contollers/main.php */
