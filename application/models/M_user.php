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

	public function get($username = null, $device_id = null, $status = null)
	{
		$this->db->select('ID, USERNAME, EMAIL, PASSWORD, FULL_NAME, PHONE, GENDER, AVATAR, IDENTITY_NUMBER, IDENTITY_PICTURE, ADDRESS, DEVICE_ID, ID_CREATE, DATE_CREATE, ID_UPDATE, DATE_UPDATE, STATUS, TYPE');
		if (!empty($username)) $this->db->where('USERNAME', $username);
		if (!empty($device_id)) $this->db->where('DEVICE_ID', $device_id);
		if (!is_null($status)) $this->db->where('STATUS', $status);
		$this->db->from('USER');
		$result = $this->db->get();

		return $result->result();
	}

	public function update_device_id($id, $device_id = null)
	{
		$this->db->set('DEVICE_ID', $device_id);
		$this->set_updater($id);
		$this->db->where('ID', $id);
		$result = $this->db->update('USER');

		return $result;
	}

}
