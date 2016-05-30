<div id="content">
    <div class="pageHeader">
    	<div class="left"><h3 class="header"><?php echo ':: '.$title.' ::';?></h3></div>
        <div class="right">
        	<a href="<?php echo base_url().'asset/attach/add/'.$assetData->id;?>">เพิ่มไฟล์</a>
        	<a href="<?php echo base_url().'asset/edit/'.$assetData->id;?>">แก้ไขข้อมูล</a>
            <a href="<?php echo base_url().'asset/page/'.$this->uri->segment(4);?>">ย้อนกลับ</a>
        </div>
        <div class="clr"></div>
    </div>
    
    <div class="assetDetail">
    	<div class="imgBox">
    	<?php
        	// LOAD IMAGE
			if(!empty($assetData->assetFullPic)){
				$assetImage = array(
							'src' => base_url().'assets/images/asset_image/resize/'.$assetData->assetFullPic,
							'width' => 400,
							'height' => 400,
							'alt' => 'asset_resize_pic',
							);
			}else{
				$assetImage = array(
							'src' => base_url().'assets/img/templates/no_image.gif',
							'width' => 400,
							'height' => 400,
							'alt' => 'asset_resize_pic',
							);
			}
			echo img($assetImage);
		?>
        </div>
        <div class="descriptionBox">
            <div class="detailBox">
                <div class="detailRow">
                    <div class="detailLeftCol">รหัสครุภัณฑ์</div>
                    <div class="detailRightCol"><?php echo $assetData->code;?></div>
                    <div class="clr"></div>
                </div>
                <?php
                    // DETAIL
                    if(!empty($assetData->detail)){
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>รายละเอียดครุภัณฑ์</div>';
                        echo '<div class=\'detailRightCol\'>'.$assetData->detail.'</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
					
                    // RESPONSE USER
                    if(!empty($assetData->responseUser)){
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>ผู้รับผิดชอบ</div>';
                        echo '<div class=\'detailRightCol\'>'.$assetData->responseUser.'</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                    
                    // RESPONSE DEPARTMENT
                    if(!empty($assetData->responseDepartment)){
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>แผนกผู้รับผิดชอบ</div>';
                        echo '<div class=\'detailRightCol\'>'.$assetData->departmentName.'</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                    
                    // LOCATION STORAGE
                    if(!empty($assetData->locationStorage)){
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>ที่เก็บ/สถานที่ตั้งทรัพย์สิน</div>';
                        echo '<div class=\'detailRightCol\'>'.$assetData->locationStorage.'</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                
                    // ASSET SOLD DATE
                    if($assetData->soldDate != '0000-00-00'){
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>วันที่จัดซื้อ</div>';
                        echo '<div class=\'detailRightCol\'>'.$this->mydatesystem->Thaidate($assetData->soldDate, 2).'</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                    
                    // ASSET VALUE
                    if(!empty($assetData->value)){
						$value = number_format($assetData->value,2);
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>ราคา</div>';
                        echo '<div class=\'detailRightCol\'>'.$value.' บาท</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                    
                    // ASSET WAARNTY DATE
                    if($assetData->warrantyStartDate != '0000-00-00' && $assetData->warrantyEndDate != '0000-00-00'){
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>วันที่รับประกัน</div>';
                        echo '<div class=\'detailRightCol\'>'.$this->mydatesystem->Thaidate($assetData->warrantyStartDate, 1).' - '.$this->mydatesystem->Thaidate($assetData->warrantyEndDate, 2).'</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                    
                    // ASSET UPDATE DATE
                    if($assetData->UpdateDate != '0000-00-00'){
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>วันที่แก้ไขข้อมูลล่าสุด</div>';
                        echo '<div class=\'detailRightCol\'>'.$this->mydatesystem->Thaidate($assetData->UpdateDate, 1, TRUE).'</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                    
                    // ASSET STATUS
                    if(!empty($assetData->status)){
                        
                        // ASSET STATUS TXT COLOR
                        if($assetData->status == 1){
                            $txt_status = '<span class=\'txt-valid\'>'.$assetData->statusName.'</span>';
                        }else{
                            $txt_status = '<span class=\'txt-warning\'>'.$assetData->statusName.'</span>';
                        }
                        
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>สถานะ</div>';
                        echo '<div class=\'detailRightCol\'>'.$txt_status.'</div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                    
                    // ASSET REMARK
                    if(!empty($assetData->remark)){
                        echo '<div class=\'detailRow\'>';
                        echo '<div class=\'detailLeftCol\'>หมายเหตุ</div>';
                        echo '<div class=\'detailRightCol\'><span class=\'txt-warning\'>'.$assetData->remark.'</span></div>';
                        echo '<div class=\'clr\'></div>';
                        echo '</div>';
                    }
                ?>
            </div>
            <?php
            	if($attachList > 0){
			?>
            <div class="assetAttachList">
                <h3>รายการไฟล์แนบ</h3>
                <?php
					
					// CONFIG IMAGE
					$edit = array(
							'src' => base_url().'assets/img/templates/edit.gif',
							'width' => 16,
							'alt' => 'edit',
							'title' => 'แก้ไข'
							);
					$del = array(
							'src' => base_url().'assets/img/templates/del.gif',
							'width' => 16,
							'alt' => 'edit',
							'title' => 'ลบ'
							);
					
					$i = 0;
                	foreach($attachList as $attachData){
						$mod = $i%2;
						if($mod != 0){$class = 'style=\'background:#CCE6FF;\';';}else{$class = '';}
				?>
                	<div class="attachRow" <?php echo $class;?>>
                    	<div class="left">
                        <?php
                        	if($attachData->filePath != ''){
								echo '<a href='.base_url().'assets/upload/'.$attachData->filePath.'>'.$attachData->fileName.'</a>';
							}else{
								echo '<span class=\'txt-warning\'>'.$attachData->fileName.'</span>';
							}
						?>
                        </div>
                        <div class="right">
                        	<a href="<?php echo base_url().'asset/attach/edit/'.$attachData->id;?>"><?php echo img($edit);?></a>
                        	<a href="<?php echo base_url().'asset/attach/del/'.$attachData->id;?>"><?php echo img($del);?></a>
                        </div>
                        <div class="clr"></div>
                    </div>
                <?php
				$i++;
					}
				?>
            </div>
            <?php
				}
			?>
        </div>
        <div class="clr"></div>
    </div>
</div>
