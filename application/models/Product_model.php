<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private function date2db($input)
    {
        if (!empty($input)) {
            $splitDate = explode('/', $input);
            $output = $splitDate[2].'-'.$splitDate[1].'-'.$splitDate[0];
        } else {
            $output = '0000-00-00';
        }

        return $output;
    }

    public function count($keyword)
    {
        $query = $this->db->get('assets');

        if (!empty($keyword)) {
            $this->db->where("(
                assets.name LIKE '%keyword%'
                OR assets.email LIKE '%keyword%'
                OR assets.tel LIKE '%keyword%'
            )");
        }

        return $query->num_rows();
    }

    public function all($keyword, $sort, $order, $start, $length)
    {
        if (!empty($keyword)) {
            $this->db->where("(
                assets.name LIKE '%keyword%'
                OR assets.email LIKE '%keyword%'
                OR assets.tel LIKE '%keyword%'
            )");
        }

        $this->db->join('asset_status', 'assets.status = asset_status.status_id');

        if ($sort != '' && $order != '') {
            $this->db->order_by('assets.' . $sort, $order);
        }

        $this->db->where('deleted_at', NULL);
        $query = $this->db->get('assets', $length, $start);

        return $query->result();
    }

    // public function find($id)
    // {
    //     $query = $this->db->where('id', $id)->get('customers');
    //
    //     return $query->row();
    // }
    //
    // public function create($inputs)
    // {
    //     $data = [
    //         'name' => $inputs['name'],
    //         'address' => $inputs['address'],
    //         'tel' => $inputs['tel'],
    //         'email' => $inputs['email'],
    //         'created_at' => date('Y-m-d H:i:s'),
    //         'updated_at' => date('Y-m-d H:i:s'),
    //     ];
    //
    //     $this->db->insert('customers', $data);
    //
    //     return $this->db->insert_id();
    // }
    //
    // public function update($id, $inputs)
    // {
    //     $data = [
    //         'name' => $inputs['name'],
    //         'address' => $inputs['address'],
    //         'tel' => $inputs['tel'],
    //         'email' => $inputs['email'],
    //         'created_at' => date('Y-m-d H:i:s'),
    //         'updated_at' => date('Y-m-d H:i:s'),
    //     ];
    //
    //     $this->db->where('id', $id)->update('customers', $data);
    // }
    //
    // public function delete($id, $softdelete = true)
    // {
    //     if ($softdelete) {
    //         $this->db->where('id', $id)->update('customers', ['deleted_at' => date('Y-m-d H:i:s')]);
    //     } else {
    //         $this->db->where('id', $id)->delete('customers');
    //     }
    // }

    // GET REGULAR DATA LIST
    public function getDataList($table, $cond = null)
    {
        if (empty($cond)) {
            $this->db->select()->from($table);
        } else {
            $this->db->select()->from($table)->where($cond);
        }

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    // GET REGULAR LIMIT DATA LIST
    public function getDataLimit($table, $sort, $sortType, $perPage, $limitPage)
    {
        $this->db->select()->from($table);
        $this->db->order_by($sort, $sortType);
        $this->db->limit($perPage, $limitPage);

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    // GET REGULAR LIMIT DATA LIST
    public function getDataLimitFilter($table, $filter, $sort, $sortType, $perPage, $limitPage)
    {
        $this->db->select()->from($table);
        $this->db->where($filter);
        $this->db->order_by($sort, $sortType);
        $this->db->limit($perPage, $limitPage);

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    // GET REGULAR DATA COUNT
    public function getDataCount($table, $cond = null)
    {
        $this->db->select()->from($table);
        if (!empty($cond)) {
            $this->db->where($cond);
        }

        return $this->db->count_all_results();
    }

    // GET REGULAR SPECIFIC DATA COLUMN
    public function getDataRow($table, $field, $value)
    {
        $cond = array($field => $value);
        $this->db->select()->from($table)->where($cond);
        $query = $this->db->get();

        return $query->row();
    }

    // UPDATE REGULAR DATA
    public function updateData($table, $data, $field, $value)
    {
        $cond = array($field => $value);
        $this->db->update($table, $data, $cond);
    }

    // DELETE REGULAR DATA
    public function delDataRow($table, $field, $value)
    {
        $cond = array($field => $value);
        $this->db->delete($table, $cond);
    }

    // GET ASSET COUNT DATA FOR CHECK EXIST CODE
    public function getAssetCheck($code1, $code2)
    {
        $this->db->select()->from('asset');
        $this->db->where('code', $code1);
        $this->db->where('code !=', $code2);

        return $this->db->count_all_results();
    }

    // GET ASSET SEARCH DATA LIST
    public function getAssetDataList($key = [])
    {
        $this->db->select()->from('asset');
        $this->db->join('asset_status', 'asset.status = asset_status.status_id', 'left outer');

        // ADD CONDITION TO SEARCH DATA
        if (!empty($key[0]) && !empty($key[1]) && !empty($key[2])) {
            $this->db->where('cat_id', $key[0])->where('sub_cat_id', $key[1])->like('code', $key[2])->or_like('detail', $key[2]);
        } elseif (!empty($key[0]) && !empty($key[1]) && empty($key[2])) {
            $this->db->where('cat_id', $key[0])->where('sub_cat_id', $key[1]);
        } elseif (!empty($key[0]) && empty($key[1]) && !empty($key[2])) {
            $this->db->where('cat_id', $key[0]);
        } elseif (!empty($key[0]) && empty($key[1]) && empty($key[2])) {
            $this->db->like('cat_id', $key[0]);
        } elseif (empty($key[0]) && empty($key[1]) && !empty($key[2])) {
            $this->db->like('code', $key[2])->or_like('detail', $key[2]);
        }

        $this->db->order_by('code', 'ASC');

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getAssetCount($key)
    {
        $this->db->select()->from('asset');
        $this->db->join('asset_status', 'asset.status = asset_status.status_id', 'left outer');

        // ADD CONDITION TO SEARCH DATA
        if (!empty($key[0]) && !empty($key[1]) && !empty($key[2])) {
            $this->db->where('cat_id', $key[0])->where('sub_cat_id', $key[1])->like('code', $key[2])->or_like('detail', $key[2]);
        } elseif (!empty($key[0]) && !empty($key[1]) && empty($key[2])) {
            $this->db->where('cat_id', $key[0])->where('sub_cat_id', $key[1]);
        } elseif (!empty($key[0]) && empty($key[1]) && !empty($key[2])) {
            $this->db->where('cat_id', $key[0]);
        } elseif (!empty($key[0]) && empty($key[1]) && empty($key[2])) {
            $this->db->like('cat_id', $key[0]);
        } elseif (empty($key[0]) && empty($key[1]) && !empty($key[2])) {
            $this->db->like('code', $key[2])->or_like('detail', $key[2]);
        }

        return $this->db->count_all_results();
    }

    // GET ASSET DATA 2 REPORT
    public function getAssetReportList($key)
    {
        $this->db->select('asset.id AS asset_id, category.catType AS catType, category.catName AS catName, sub_category.subTypeName AS subTypeName, asset.detail AS detail, asset.code AS code, asset.value as value, asset.soldDate as soldDate, asset.warrantyStartDate as startDate, asset.warrantyEndDate as endDate, responseUser as owner, department.departmentName as depName, asset.locationStorage as location, asset_status.statusName as statName, asset.remark as remark')->from('asset');
        $this->db->join('category', 'asset.cat_id = category.cat_id', 'left outer');
        $this->db->join('sub_category', 'asset.sub_cat_id = sub_category.id', 'left outer');
        $this->db->join('asset_status', 'asset.status = asset_status.status_id', 'left outer');
        $this->db->join('department', 'asset.responseDepartment = department.department_id', 'left outer');

        // ADD CONDITION TO SEARCH DATA
        if (!empty($key[0]) && !empty($key[1]) && !empty($key[2])) {
            $this->db->where('asset.cat_id', $key[0])->where('asset.sub_cat_id', $key[1])->like('asset.code', $key[2])->or_like('asset.detail', $key[2]);
        } elseif (!empty($key[0]) && !empty($key[1]) && empty($key[2])) {
            $this->db->where('asset.cat_id', $key[0])->where('asset.sub_cat_id', $key[1]);
        } elseif (!empty($key[0]) && empty($key[1]) && !empty($key[2])) {
            $this->db->where('asset.cat_id', $key[0]);
        } elseif (!empty($key[0]) && empty($key[1]) && empty($key[2])) {
            $this->db->like('asset.cat_id', $key[0]);
        } elseif (empty($key[0]) && empty($key[1]) && !empty($key[2])) {
            $this->db->like('asset.code', $key[2])->or_like('asset.detail', $key[2]);
        }

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    // GET ASSET DATA ROW
    public function getAssetRow($id)
    {
        if (!empty($id)) {
            $cond = array('id' => $id);
            $this->db->select()->from('asset')
            ->join('asset_status', 'asset.status = asset_status.status_id', 'left')
            ->join('department', 'asset.responseDepartment = department.department_id', 'left')
            ->where($cond);

            $query = $this->db->get();

            if ($query->num_rows() == 1) {
                return $query->row();
            } else {
                return false;
            }
        }

        return false;
    }

    // ADD ASSET DATA TO DATABASE
    public function assetAdd($thumb, $resize)
    {

        // CONFIG ABOUT DATE
        $date = date('Y-m-d H:i:s');
        $getSoldDate = $this->input->post('txt_soldDate');
        $getWarrantyFrom = $this->input->post('warrantyFrom');
        $getWarrantyTo = $this->input->post('warrantyTo');

        // SETUP INPUT DATA BEFORE PUT ON ARRAY
        $cat_id = $this->input->post('assetCat');
        $subCat_id = $this->input->post('assetSubCat');
        $assetCode = $this->input->post('txt_code');
        $detail = $this->input->post('txt_detail');
        $value = $this->input->post('txt_value');
        $soldDate = !empty($getSoldDate)?$this->date2db($this->input->post('txt_soldDate')):'0000-00-00';
        $warrantyStart = !empty($getWarrantyFrom)?$this->date2db($this->input->post('warrantyFrom')):'0000-00-00';
        $warrantyEnd = !empty($getWarrantyTo)?$this->date2db($this->input->post('warrantyTo')):'0000-00-00';
        $responseUser = $this->input->post('txt_responseUser');
        $responseDepartment = $this->input->post('txt_department');
        $locationStorage = $this->input->post('txt_location');
        $status = $this->input->post('txt_status');
        $IsApproved = $this->input->post('IsApproved');
        $remark = $this->input->post('txt_remark');

        // PUT ALL DATA TO ARRAY
        $insertData = array(
                        'cat_id' => $cat_id,
                        'sub_cat_id' => $subCat_id,
                        'assetThumbPic' => $thumb,
                        'assetFullPic' => $resize,
                        'code' => $assetCode,
                        'detail' => $detail,
                        'value' => $value,
                        'soldDate' => $soldDate,
                        'warrantyStartDate' => $warrantyStart,
                        'warrantyEndDate' => $warrantyEnd,
                        'responseUser' => $responseUser,
                        'responseDepartment' => $responseDepartment,
                        'locationStorage' => $locationStorage,
                        'status' => $status,
                        'IsApproved' => $IsApproved,
                        'updateDate' => $date,
                        'remark' => $remark
                    );
        // INSERT DATA
        $this->db->insert('asset', $insertData);
    }

    // UPDATE ASSET DATA
    public function assetUpdate($id, $thumb, $resize)
    {

        // CONFIG ABOUT DATE
        $date = date('Y-m-d H:i:s');
        $getSoldDate = $this->input->post('txt_soldDate');
        $getWarrantyFrom = $this->input->post('warrantyFrom');
        $getWarrantyTo = $this->input->post('warrantyTo');

        // SETUP INPUT DATA BEFORE PUT ON ARRAY
        $cat_id = $this->input->post('assetCat');
        $subCat_id = $this->input->post('assetSubCat');
        $assetCode = $this->input->post('txt_code');
        $detail = $this->input->post('txt_detail');
        $value = $this->input->post('txt_value');
        $soldDate = !empty($getSoldDate)?$this->date2db($this->input->post('txt_soldDate')):'0000-00-00';
        $warrantyStart = !empty($getWarrantyFrom)?$this->date2db($this->input->post('warrantyFrom')):'0000-00-00';
        $warrantyEnd = !empty($getWarrantyTo)?$this->date2db($this->input->post('warrantyTo')):'0000-00-00';
        $responseUser = $this->input->post('txt_responseUser');
        $responseDepartment = $this->input->post('txt_department');
        $locationStorage = $this->input->post('txt_location');
        $status = $this->input->post('txt_status');
        $IsApproved = $this->input->post('IsApproved');
        $remark = $this->input->post('txt_remark');

        // UPDATE COND
        $updateCond = array('id' => $id);
        $updateData = [
            'cat_id' => $cat_id,
            'sub_cat_id' => $subCat_id,
            'assetThumbPic' => $thumb,
            'assetFullPic' => $resize,
            'code' => $assetCode,
            'detail' => $detail,
            'value' => $value,
            'soldDate' => $soldDate,
            'warrantyStartDate' => $warrantyStart,
            'warrantyEndDate' => $warrantyEnd,
            'responseUser' => $responseUser,
            'responseDepartment' => $responseDepartment,
            'locationStorage' => $locationStorage,
            'status' => $status,
            'IsApproved' => $IsApproved,
            'updateDate' => $date,
            'remark' => $remark
        ];

        // UPDATE DATA
        $this->db->update('asset', $updateData, $updateCond);
    }

    // DELETE ASSET DATA
    public function assetDelete($id)
    {
        if (!empty($id)) {

            // PATH
            $thumbPath = str_replace(SELF, '', FCPATH).'assets/img/asset_image/thumb/';
            $resizePath = str_replace(SELF, '', FCPATH).'assets/img/asset_image/resize/';

            // LOAD DATA
            $assetData = $this->getAssetRow($id);

            // DELETE FILES
            if ($assetData->assetThumbPic != '') {
                @unlink($thumbPath.$assetData->assetThumbPic);
            }
            if ($assetData->assetFullPic != '') {
                @unlink($resizePath.$assetData->assetFullPic);
            }

            // DELETE DATA
            $delCond = array('id' => $id);
            $this->db->delete('asset', $delCond);
        } else {
            return false;
        }
    }

    // ASSET ATTACH FILE
    public function assetAttachAdd($attach)
    {
        $insertData = array(
                    'asset_id' => $this->uri->segment(4),
                    'fileName' => $this->input->post('txt_filename'),
                    'filePath' => $attach,
                    'remark' => $this->input->post('txt_remark')
                    );
        $this->db->insert('asset_attachment', $insertData);
    }

    // ASSET ATTACH FILE
    public function assetAttachUpdate($id, $asset_id, $attach)
    {
        $cond = array('id' => $id);
        $updateData = array(
                    'asset_id' => $asset_id,
                    'fileName' => $this->input->post('txt_filename'),
                    'filePath' => $attach,
                    'remark' => $this->input->post('txt_remark')
                    );
        $this->db->update('asset_attachment', $updateData, $cond);
    }

    // ASSET ATTACH DELETE
    public function assetAttachDel($id)
    {
        $path = str_replace(SELF, '', FCPATH).'assets/upload/';

        // LOAD ASSET ATTACH
        $attachData = $this->getDataRow('asset_attachment', 'id', $id);
        if ($attachData->filePath != '') {
            @unlink($path.$attachData->filePath);
        }

        $delCond = array('id' => $id);
        $this->db->delete('asset_attachment', $delCond);
        redirect('product/view/'.$attachData->asset_id, 'refresh');
    }

    // ADD ASSET CATEGORY
    public function categoryAdd()
    {
        $dataInsert = array(
                        'catType' => $this->input->post('txt_type'),
                        'catName' => $this->input->post('txt_category')
                    );
        $this->db->insert('category', $dataInsert);
    }

    public function categoryUpdate($id)
    {
        $cond = array('cat_id' => $id);
        $dataUpdate = array(
                        'catType' => $this->input->post('txt_type'),
                        'catName' => $this->input->post('txt_category')
                    );
        $this->db->update('category', $dataUpdate, $cond);
    }

    // ADD ASSET SUB CATEGORY
    public function subCategoryAdd()
    {
        $dataInsert = [
            'cat_id' => $this->input->post('assetCat'),
            'subTypeName' => $this->input->post('txt_subcategory')
        ];
        $this->db->insert('sub_category', $dataInsert);
    }

    public function subCategoryUpdate($id)
    {
        $cond = array('id' => $id);
        $dataUpdate = [
            'cat_id' => $this->input->post('assetCat'),
            'subTypeName' => $this->input->post('txt_subcategory')
        ];
        $this->db->update('sub_category', $dataUpdate, $cond);
    }

    public function getDataValue($id)
    {
        $products = $this->db->select_sum('value')->get('asset');
        $totalValue = $products->row();

        return $totalValue->value;
    }
}
