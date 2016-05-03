<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

/*
 * @Class : myUpload Class
 * @Extends Class : class.upload by Collin Verot
 * @author : Phongthorn Kumkankaewy
 * 
 * This class use for extends class. upload by Collin Verot.
 * It's many functionaly and usefuls for upload any file.
 * This class has add some method to do often upload. 
 *
 */

require_once(APPPATH.'libraries/class.upload/class.upload.php');

class MyUpload extends upload{
	
	public function MyUpload(){
		
	}
	
	public function uploadFile($srcFile, $file_dst_path, $file_new_name){
		
		$this->upload($srcFile);
		
		if($this->uploaded){
			
			$this->file_new_name_body = $file_new_name;
			$this->process($file_dst_path);
			
			if($this->processed){
				return $this->file_dst_name;
			}else{
				return FALSE;
			}
		}
	}
	
	/*
	 * @Method : imgUploadRatioY
	 * @Param
	 * srcFile = destination of temp upload file
	 * file_dst_path = target destination upload file
	 * file_new_name = new name of file upload
	 * file_new_ext = change extension
	 * width = new dimension of width
	 *
	 * @Description : This method use for upload any image
	 * and resize image from your input new width. you can
	 * force new extension,too.
	 *
	 */
	public function imgUploadRatioY($srcFile, $file_dst_path, $file_new_name, $file_new_ext, $width){

		$this->upload($srcFile);
		
		if($this->uploaded){
			if($this->file_is_image){
				if($this->file_src_size < 2048000){
					
					$this->file_new_name_body = $file_new_name;
					$this->file_new_name_ext = $file_new_ext;
					$this->image_resize = TRUE;
					$this->file_force_extension = TRUE;
					$this->image_x = $width;
					$this->image_ratio_y = TRUE;
					$this->process($file_dst_path);
					
					if($this->processed){
						return $this->uploadFile = $this->file_dst_name;
					}else{
						return $this->error = 'เกิดความผิดพลาดกรุณาลองใหม่';
					}
				}else{
					return $this->error = 'ไฟล์มีขนาดใหญ่เกิน 2 MB';
				}
			}else{
				return $this->error = 'กรุณาใช้ไฟล์รูปภาพเท่านั้น';
			}
		}
	}
}
?>