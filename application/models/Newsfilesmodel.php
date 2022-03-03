<?php
class Newsfilesmodel extends  CI_Model
{
	var $table = 'news_files';
	var $tableAs = 'news_files a';	
	function __construct()
	{
		parent::__construct();
	}

	function insert($data)
	{
		$data['create_date'] 	= date('Y-m-d H:i:s');
		$data['user_id_create'] = id_user();
		$this->db->insert($this->table, array_filter($data));
		return $this->db->insert_id();
	}
	function update($data, $id)
	{
		$where['id'] = $id;
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table, $data, $where);
		return $id;
	}
	function updateAll($data, $id)
	{
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');

		$where['id'] = $id;
		$this->db->update($this->table, $data, $where);

		$getLang = $this->db->select('id_lang, id_parent_lang')->get_where($this->table, array('id' => $id))->row_array();
		if ($getLang['id_lang'] == 1) {
			$whereNext['id_parent_lang'] = $id;
		} else {
			$whereNext['id'] = $getLang['id_parent_lang'];
		}

		$this->db->update($this->table, $data, $whereNext);
		return $id;
	}
	function updateByOther($data, $where)
	{
		$data['user_id_modify'] = id_user();
		$data['modify_date'] 	= date('Y-m-d H:i:s');
		$this->db->update($this->table, $data, $where);
		return $id;
	}
	function delete($id)
	{
		$delete = $this->db->delete($this->table, "id = " . $id);
		return $delete;
	}

	function findById($id)
	{
		$where['a.id'] = $id;
		$where['is_delete'] = 0;
		return 	$this->db->get_where($this->table . ' a', $where)->row_array();
	}
	function findBy($where, $is_single_row = 0)
	{
		$where['is_delete'] = 0;
		$this->db->select('*');
		if ($is_single_row == 1) {
			return 	$this->db->get_where($this->tableAs, $where)->row_array();
		} else {
			return 	$this->db->get_where($this->tableAs, $where)->result_array();
		}
	}
	function listFiles($where, $is_single_row = 0)
	{
		$where['is_delete'] = 0;
		$this->db->select('id as idFile, name as name_file, filename, hits as hits_file, id_lang, user_id_create, user_id_modify, create_date, modify_date, is_delete, rownum()-1 as rowNum');
		if ($is_single_row == 1) {
			return 	$this->db->get_where($this->tableAs, $where)->row_array();
		} else {
			return 	$this->db->get_where($this->tableAs, $where)->result_array();
		}
	}


	function fetchRow($where)
	{
		return $this->findBy($where, 1);
	}

}
