<?php
/**
 * Class M_user
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class M_user extends TDB_Model
{
	private $TAG = 'M_user';

	/**
	 * M_user constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function create($id, $username, $email, $password, $full_name, $phone, $gender, $avatar, $identity_number,
		$identity_picture, $address, $device_id, $status = 2, $type = 1)
	{
		$date = date('Y-m-d H:i:s');
		$password_hash = password_hash($password, PASSWORD_BCRYPT);
		if (!$password_hash) throw new Exception('Failed hashing password');
		$data = array(
			'ID' => $id,
			'USERNAME' => $username,
			'EMAIL' => $email,
			'PASSWORD' => $password_hash,
			'FULL_NAME' => $full_name,
			'PHONE' => $phone,
			'GENDER' => $gender,
			'AVATAR' => $avatar,
			'IDENTITY_NUMBER' => $identity_number,
			'IDENTITY_PICTURE' => $identity_picture,
			'ADDRESS' => $address,
			'DEVICE_ID' => $device_id,
			'TYPE' => $type,
			'STATUS' => $status
		);
		$this->set_creator($id, $date);
		$this->set_updater($id, $date);
		$result = $this->db->insert('USER', $data);

		return $result;
	}

	public function check_username($username)
	{
		$this->db->select('ID');
		$this->db->from('USER');
		$this->db->where('USERNAME', $username);
		$result = $this->db->get();

		$cas_use = true;
		if (!empty($result->result())) $cas_use = false;

		return $cas_use;
	}

	public function get($username = null, $device_id = null, $status = null, $id = null, $type = null)
	{
		$this->db->select('ID, USERNAME, EMAIL, PASSWORD, FULL_NAME, PHONE, GENDER, AVATAR, IDENTITY_NUMBER, IDENTITY_PICTURE, ADDRESS, DEVICE_ID, ID_CREATE, DATE_CREATE, ID_UPDATE, DATE_UPDATE, STATUS, TYPE');
		if (!empty($username)) $this->db->where('USERNAME', $username);
		if (!empty($device_id)) $this->db->where('DEVICE_ID', $device_id);
		if (!empty($id)) $this->db->where('ID', $id);
		if (!is_null($status)) {
		    if(is_array($status)) $this->db->where_in('STATUS', $status);
		    else $this->db->where('STATUS', $status);
        }
		if (!is_null($type)) {
		    if(is_array($type)) $this->db->where_in('TYPE', $type);
		    else $this->db->where('TYPE', $type);
        }
		$this->db->from('USER');
        $result = $this->db->get();

		return $result->result();
	}

    public function count_not_active()
    {
        $this->db->from('USER');
        $this->db->where('TYPE', 1);
        $this->db->where('STATUS', 2);
        return $this->db->count_all_results();
	}

    public function count_rejected()
    {
        $this->db->from('USER');
        $this->db->where('TYPE', 1);
        $this->db->where('STATUS', 3);
        return $this->db->count_all_results();
	}

    public function update_status($id, $status, $id_updater = null)
    {
        $this->db->set('STATUS', $status);
        $this->db->where('ID', $id);
        if(empty($id_updater)) $id_updater = $id;
        $this->set_updater($id_updater);
        $result = $this->db->update('USER');

        return $result;
	}

	public function update_device_id($id, $device_id = null)
	{
		$this->db->set('DEVICE_ID', $device_id);
		$this->set_updater($id);
		$this->db->where('ID', $id);
		$result = $this->db->update('USER');

		return $result;
	}

    public function delete_pure($id)
    {
        $this->db->where('ID', $id);
        $this->db->delete('USER');
	}

}
