<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pertanyaan_model extends CI_Model {

    var $table      = 'ref_pertanyaan_kuis_ipp';
    var $tableAs    = 'ref_pertanyaan_kuis_ipp a';

    function __construct()
    {  
        parent::__construct();
    }

    function records($where = array(), $isTotal = 0)
    {
        $grup                      = $this->session->userdata['ADM_SESS']['admin_id_auth_user_group'];
        $alias['search_form']      = 'a.id_ref_form_ipp';
        $alias['search_nama']      = 'a.nama';
        $alias['search_kategori']  = 'a.id_kategori';
        $alias['search_indikator'] = 'a.id_ref_indikator_ipp';
        $alias['search_status']    = 'a.id_status_publish';
        
        
        query_grid($alias,$isTotal);
        $this->db->select("a.*, b.nama as nama_form, c.kode_indikator, e.name as status, d.nama as kategori");
        $this->db->join('ref_form_ipp b',"b.id = a.id_ref_form_ipp",'left');
        $this->db->join('ref_indikator_ipp c',"c.id_ref_indikator_ipp = a.id_ref_indikator_ipp",'left');
        $this->db->join('ref_kategori_pertanyaan_kuis_ipp d',"d.id = a.id_kategori",'left');
        $this->db->join('status_publish e',"e.id = a.status_publish",'left');
        $this->db->where("a.is_delete", 0);

        $query = $this->db->get($this->tableAs);


        if ($isTotal == 0)
        {
            $data = $query->result_array();
        }
        else
        {
            return $query->num_rows();
        }

        $ttl_row = $this->records($where, 1);

        return ddi_grid($data, $ttl_row);
    }

    function update($data, $id)
    {
        $where['id'] = $id;
        // $data['last_update'] = date('Y-m-d H:i:s');
        // $data['last_update_user'] = id_user();
        $data['user_id_modify'] = id_user();
        $data['modify_date'] = date('Y-m-d H:i:s');
        $this->db->update($this->table, $data, $where);
    }

    function insert($data)
    {
        $data['user_id_create'] = id_user();
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, array_filter($data));
        $id = $this->db->insert_id();

        return $id;
    }

    function findById($id)
    {
        $where['is_delete'] = 0;
        $where['a.id'] = $id;

        return $this->db->get_where($this->table.' a', $where)->row_array();
    }

    function findBy($where, $is_single_row=0, $is_total=0){
        $where['a.is_delete'] = 0;
        $this->db->select('a.*');
        $query = $this->db->get_where($this->tableAs, $where);
        if ($is_total == 1)
        {
            return $query->num_rows();
        }
        else
        {
            if ($is_single_row == 1) {
                return $query->row_array();
            }
            else 
            {
                return $query->result_array();
            }
        }
            
    }

    function findChild($id_parent){
        $where['is_delete'] = 0;
        $where['a.id_parent_pertanyaan'] = $id_parent;

        return $this->db->get_where($this->table.' a', $where)->row_array();
    }

    function fetchRow($where) {
        return $this->findBy($where,1);
    }

    function counter($where){
        $where['a.is_delete'] = 0;
        $this->db->select('a.*');
        return $this->db->get_where($this->tableAs, $where)->num_rows();
    }

    function delete($id)
    {
        $data['is_delete'] = 1;
        $this->update($data, $id);
    }


}
