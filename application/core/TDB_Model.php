<?php
/**
 * Class TDB_Model
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class TDB_Model extends CI_Model
{

	/**
	 * TDB_Model constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

    /**
     * Function to generate ID.
     * This use mysql UUID.
     *
     * @return string generated ID
     * @throws \Exception if cant create ID
     */
	public function generate_id()
	{
		$this->db->select('UUID() as id', false);
		$result = $this->db->get();
		$result = $result->result();
		if (empty($result)) throw new Exception('Something wrong with database');

		return $result[0]->id;
	}

    /**
     * Function to add set data of creator.
     * In every table has Creator data so we can call this function to generate that query.
     *
     * @param string $id_creator id of user creator
     * @param string|null $date Date of create
     */
	protected function set_creator($id_creator, $date = null)
	{
		if (empty($date)) $date = date('Y-m-d H:i:s');
		$this->db->set('ID_CREATE', $id_creator);
		$this->db->set('DATE_CREATE', $date);
	}

    /**
     * Function to add set data of updater.
     * In every table has Updater data so we can call this function to generate that query.
     *
     * @param string $id_updater id of user update
     * @param string|null $date Date of update
     */
	protected function set_updater($id_updater, $date = null)
	{
		if (empty($date)) $date = date('Y-m-d H:i:s');
		$this->db->set('ID_UPDATE', $id_updater);
		$this->db->set('DATE_UPDATE', $date);
	}
}
