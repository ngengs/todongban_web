<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Location
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Location extends TDB_Controller
{

	/**
	 * Location constructor.
	 */
	public function __construct()
	{
		parent::__construct(true);
		$this->load->model('m_location');
	}

    /**
     * API for update user last location
     *
     * access: public. need authorization header
     * method: post
     *
     * Input needed:
     * double latitude position latitude
     * double longtitude position longtitude
     */
	public function index_post()
	{
		$this->log->write_log('debug', 'Location: index_post:');
		if (!$this->check_access()) $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
		$latitude = (double)$this->input->post('latitude');
		$longtitude = (double)$this->input->post('longtitude');
		if (empty($longtitude) || empty($latitude)) {
			$this->response_error(STATUS_CODE_SERVER_ERROR, 'Data not complete');
		}
		$user = $this->get_user();
		$result = $this->m_location->insert_location($user->ID, $latitude, $longtitude);
		if ($result) {
			$this->response('Success Update Location');
		} else $this->response_error(STATUS_CODE_SERVER_ERROR, 'Fail update location');
	}
}
