<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class M_location
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class M_location extends TDB_Model
{
	private $TAG = 'M_location';

	/**
	 * M_location constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

    /**
     * Function to add user last location.
     * This will add new location with status active and update old location to non active.
     *
     * @param string $id_user User ID
     * @param double $latitude user last position Latitude
     * @param double $longtitude user last position Longtitude
     *
     * @return bool result of query
     */
	public function insert_location($id_user, $latitude, $longtitude)
	{
		$date = date('Y-m-d H:i:s');
//		Generate query
//		Generate update query
		$this->db->where('ID_USER', $id_user);
		$this->db->where('STATUS', 1);
		$this->db->set('STATUS', 0);
		$this->db->from('USER_LOCATION');
		$query_update = $this->db->get_compiled_update();
//		Generate insert query
		$this->db->set('ID', $this->generate_id());
		$this->db->set('ID_USER', $id_user);
		$this->db->set('LATITUDE', $latitude);
		$this->db->set('LONGTITUDE', $longtitude);
		$this->db->set('DATE', $date);
		$this->db->set('STATUS', 1);
		$this->db->from('USER_LOCATION');
		$query_insert = $this->db->get_compiled_insert();

//		Run transaction
		$this->db->trans_start();
		$this->db->query($query_update);
		$this->db->query($query_insert);
		$this->db->trans_complete();

		return $this->db->trans_status();
	}
}
