<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class M_type
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class M_type extends TDB_Model
{
	private $TAG = 'M_type';

	/**
	 * M_type constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function create($vehicle, $name, $status = 1)
	{
		if ($vehicle !== 1 && $vehicle !== 2) throw new Exception('Vehicle type not supported');
		$this->db->set('VEHICLE', $vehicle);
		$this->db->set('NAME', $name);
		$this->db->set('STATUS', $status);
		$this->db->set('ID', $this->generate_id());
		$this->db->from('HELP_TYPE');

		return $this->db->insert();
	}

	public function get($vehicle = null, $status = null)
	{
		$this->db->select('ID, VEHICLE, NAME, STATUS');
		if (!is_null($vehicle) && ($vehicle !== 1 && $vehicle !== 2)) $this->db->where('VEHICLE', $vehicle);
		if (!is_null($status)) $this->db->where('STATUS', $status);
		$this->db->order_by('VEHICLE', 'ASC');
		$this->db->order_by('STATUS', 'DESC');
		$this->db->from('HELP_TYPE');
		$result = $this->db->get();

		return $result->result();
	}


}
