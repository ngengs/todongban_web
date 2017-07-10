<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class User
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 *
 */
class User extends TDB_Controller
{
    private $TAG = 'User';

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct(true);
    }

    /**
     * API for login/create token
     *
     * access: public. no need authorization header
     * method: post
     *
     * Input needed:
     * string username Username of user to check
     * string password Password of user to check
     * string device_id Device id of user
     */
    public function sign_in_post()
    {
        $this->log->write_log('debug', $this->TAG . ': sign_in: ');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $device_id = $this->input->post('device_id');
        if (empty($username) || empty($password) || empty($device_id)) {
            $this->response_error(STATUS_CODE_NOT_FOUND, 'Data not complete');
        }
        $this->load->model('m_user');
        $user = $this->m_user->get($username);
        if (empty($user)) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Username not found');
        }
        $user = $user[0];
        if (!password_verify($password, $user->PASSWORD)) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Username and password not match');
        }
        if (!$this->m_user->update_device_id($user->ID, $device_id)) {
            $this->log->write_log('error', $this->TAG . ':  sing_in: Failed update device id');
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Something wrong with server');
        }
        $user->DEVICE_ID = $device_id;
        if ($user->TYPE != 1) $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Hanya untuk pengguna biasa');

        switch ($user->STATUS) {
            case 1:
                $this->response($this->create_token(array($user->USERNAME, $user->DEVICE_ID)));
                break;
            case 2:
                $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Under verification');
                break;
            case 3:
                $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Verification failed');
                break;
            case 0:
            default:
                $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'User not found');
                break;
        }
    }

    /**
     * API for register new user
     *
     * access: public. no need authorization header
     * method: post
     *
     * Input needed:
     * string username Username of user
     * string email Email of user
     * string password Password of user
     * string full_name Full name of user
     * string phone Phone of user
     * int gender Gender of user (1: man, 2: woman)
     * string identity_number Identity number of user
     * string address Address of user to check
     * string device_id Device id of user
     * file identity_picture Image of identity user
     * file avatar Image of profile picture user
     */
    public function sign_up_post()
    {
        $this->log->write_log('debug', $this->TAG . ': sign_up: ');
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $full_name = $this->input->post('full_name');
        $phone = $this->input->post('phone');
        $gender = $this->input->post('gender');
        $identity_number = $this->input->post('identity_number');
        $address = $this->input->post('address');
        $device_id = $this->input->post('device_id');
        if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($phone)
            || empty($gender)
            || empty($identity_number)
            || empty($address)
            || empty($device_id)
        ) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        if (strlen($username) > 16) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Username length exceeded');
        }
        $this->load->model('m_user');
        $id = $this->m_user->generate_id();
        if (!$this->m_user->check_username($username)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Username exist');
        }

        $this->load->helper('string');
        $this->load->library('upload');

        $avatar_name = 'AVA_' . random_string('alnum', 16);
        $upload_path = FCPATH . '/uploads';
        if (!is_dir($upload_path)) mkdir($upload_path);
        $upload_path = $upload_path . '/' . $username;
        if (!is_dir($upload_path)) mkdir($upload_path);

        $upload_config = array(
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|png',
            'overwrite' => true,
            'file_ext_tolower' => true
        );
        $upload_config['file_name'] = $avatar_name;
        $this->upload->initialize($upload_config);
        if (!$this->upload->do_upload('avatar')) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Failed upload profile picture');
        }

        $identity_name = 'IDP_' . random_string('alnum', 16);
        $upload_config['file_name'] = $identity_name;
        $this->upload->initialize($upload_config);
        if (!$this->upload->do_upload('identity_picture')) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Failed upload identity picture');
        }
        $result = $this->m_user->create($id,
                                        $username,
                                        $email,
                                        $password,
                                        $full_name,
                                        $phone,
                                        $gender,
                                        $avatar_name,
                                        $identity_number,
                                        $identity_name,
                                        $address,
                                        $device_id);
        if ($result) {
            $this->load->model('m_type');
            $this->load->model('m_config');
            $help_type = $this->m_type->get();
            $help_type_id = array();
            foreach ($help_type as $key => $value) {
                $help_type_id[] = $value->ID;
            }
            $this->m_config->create($id, $help_type_id);

            $this->response(
                array('message' => 'Success create user', 'token' => $this->create_token(array($username, $device_id)))
            );
        } else {
            $this->log->write_log('error', $this->TAG . ':  sign_up: Inserting data error');
            $this->response_error(STATUS_CODE_SERVER_ERROR, 'Something wrong when create user');
        }
    }

    /**
     * API for signout user
     *
     * access: private. need authorization header
     * method: get
     *
     */
    public function sign_out_get()
    {
        $this->log->write_log('debug', $this->TAG . ': sign_out: ');
        if (!$this->check_access()) $this->response_404();
        $this->load->model('m_user');
        $user = $this->get_user();
        $result = $this->m_user->update_device_id($user->ID, null);

        if ($result) {
            $this->response_error(0, 'Success logout');
        } else $this->response_error(1, 'Failed logout');
    }

    public function check_get()
    {
        $this->log->write_log('debug', $this->TAG . ': check: ');
        var_dump($this->check_access());
    }

}
