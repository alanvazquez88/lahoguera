<?php

class Model_tables extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}


	public function getTablesOrders(){

			$this->db->distinct('tables.table_name');
			$this->db->from('tables');
			$this->db->join('orders', 'orders.table_id = tables.id', 'left');
			$this->db->group_by('tables.table_name');
			$this->db->order_by('tables.id', 'ASC');
			//$query = $this->db->get();
			$query = $this->db->get()->result_array();
			foreach ($query as $table) {
					if($table['paid_status'] == 1){
							$table['id'] = 0;
					}

			}

			return $query;
			//return $query->result_array();
	}

	public function getTableData($id = null)
	{
		if($id) {
			//$sql = "SELECT * FROM tables WHERE id = ?";

			$this->db->where($id);
			$this->db->join('orders', 'orders.table_id = tables.id');
			$this->db->from('tables');
		//	$query = $this->db->query($sql, array($id));
			$query = $this->db->get();
			return $query->row_array();
		}

		// if admin all data
		$user_id = $this->session->userdata('id');
		if($user_id == 1) {
			$sql = "SELECT * FROM tables ORDER BY id ASC";
			$this->db->join('orders', 'orders.table_id = tables.id');
			$query = $this->db->query($sql);

			 return $query->result_array();
		}
		else {
			$this->load->model('model_users');
			$user_data = $this->model_users->getUserData($user_id);
			$sql = "SELECT * FROM tables WHERE store_id = ? ORDER BY id DESC";
			$this->db->join('orders', 'orders.table_id = tables.id');
			$query = $this->db->query($sql, array($user_data['store_id']));
			return $query->result_array();
		}

		// else store wise


	}

	public function create($data = array())
	{
		if($data) {
			$create = $this->db->insert('tables', $data);
			return ($create == true) ? true : false;
		}
	}

	public function update($id = null, $data = array())
	{
		$this->db->where('id', $id);
		$update = $this->db->update('tables', $data);

		return ($update == true) ? true : false;
	}

	public function remove($id = null)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('tables');
			return ($delete == true) ? true : false;
		}
	}

	public function getActiveTable($table = null)
	{


		if(isset($table)){
			$user_id = $this->session->userdata('id');
			if($user_id == 1) {
				$sql = "SELECT * FROM tables WHERE available = ? AND active = ? AND table_name = ?";
				$query = $this->db->query($sql, array(1, 1, $table));
				return $query->result_array();
			}
			else {
				$this->load->model('model_users');
				$user_data = $this->model_users->getUserData($user_id);
				$sql = "SELECT * FROM tables WHERE store_id = ? AND available = ? AND active = ? AND table_name = ? ORDER BY id DESC";
				$query = $this->db->query($sql, array($user_data['store_id'], 1, 1, $table));
				return $query->result_array();
			}
		}else{
			$user_id = $this->session->userdata('id');
			if($user_id == 1) {
				$sql = "SELECT * FROM tables WHERE available = ? AND active = ?";
				$query = $this->db->query($sql, array(1, 1));
				return $query->result_array();
			}
			else {
				$this->load->model('model_users');
				$user_data = $this->model_users->getUserData($user_id);
				$sql = "SELECT * FROM tables WHERE store_id = ? AND available = ? AND active = ? ORDER BY id DESC";
				$query = $this->db->query($sql, array($user_data['store_id'], 1, 1));
				return $query->result_array();
			}
		}



	}

	public function countTotalTables()
	{
		$sql = "SELECT * FROM tables WHERE active = ?";
		$query = $this->db->query($sql, array(1));
		return $query->num_rows();
	}

	public function countAvailTables()
	{
		$sql = "SELECT * FROM tables WHERE available = 1";
		$query = $this->db->query($sql, array(1));
		return $query->num_rows();
	}


}
