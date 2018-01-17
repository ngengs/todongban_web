<?php
/**
 * Copyright (c) 2017 Rizky Kharisma (@ngengs)
 *
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
     * @throws \BadFunctionCallException
     * @throws \Exception
     * @throws \LogicException
     */
    public function signin_post()
    {
        $this->log->write_log('debug', $this->TAG . ': sign_in: ');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $device_id = $this->input->post('device_id');
        if (empty($username) || empty($password) || empty($device_id)) {
            $this->response_error(STATUS_CODE_NOT_FOUND, 'Data not complete');
        }
        $this->load->model('m_user');
        $users = $this->m_user->get($username);
        if (empty($users)) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Username not found');
        }
        $user = $users[0];
        if (!password_verify($password, $user->PASSWORD)) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Username and password not match');
        }
        if (!$this->m_user->update_device_id($user->ID, $device_id)) {
            $this->log->write_log('error', $this->TAG . ':  sing_in: Failed update device id');
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Something wrong with server');
        }
        $user->DEVICE_ID = $device_id;
        if ($user->TYPE == User_data::$TYPE_ADMIN) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Hanya untuk pengguna');
        }

        switch ($user->STATUS) {
            case 1:
            case 2:
            case 3:
            case 4:
                $this->response($this->create_token([$user->USERNAME, $user->DEVICE_ID]));
                break;
//            case 2:
//                $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Under verification');
//                break;
//            case 3:
//                $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Verification failed');
//                break;
//            case 4:
//                $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'User banned');
//                break;
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
     * int type User Type (1: personal, 2: garage)
     * file identity_picture Image of identity user
     * file avatar Image of profile picture user
     * @throws \Exception
     */
    public function signup_post()
    {
        $this->log->write_log('debug', $this->TAG . ': sign_up: ');
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $full_name = $this->input->post('full_name');
        $phone = $this->input->post('phone');
        $gender = $this->input->post('gender');
        $birth_date = $this->input->post('birth_date');
        $identity_number = $this->input->post('identity_number');
        $address = $this->input->post('address');
        $device_id = $this->input->post('device_id');
        $type = $this->input->post('type');
        $this->log->write_log('debug', $this->TAG . ': $username: ' . $username);
        $this->log->write_log('debug', $this->TAG . ': $email: ' . $email);
        $this->log->write_log('debug', $this->TAG . ': $password: ' . $password);
        $this->log->write_log('debug', $this->TAG . ': $full_name: ' . $full_name);
        $this->log->write_log('debug', $this->TAG . ': $phone: ' . $phone);
        $this->log->write_log('debug', $this->TAG . ': $gender: ' . $gender);
        $this->log->write_log('debug', $this->TAG . ': $birth_date: ' . $birth_date);
        $this->log->write_log('debug', $this->TAG . ': $identity_number: ' . $identity_number);
        $this->log->write_log('debug', $this->TAG . ': $address: ' . $address);
        $this->log->write_log('debug', $this->TAG . ': $device_id: ' . $device_id);
        $this->log->write_log('debug', $this->TAG . ': $type: ' . $type);
//        throw new Exception("Coba");
        if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($phone)
            || empty($gender)
            || empty($identity_number)
            || empty($birth_date)
            || empty($address)
            || empty($device_id)
            || empty($type)
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
        if (!is_dir($upload_path)) {
            mkdir($upload_path);
        }
        $upload_path = $upload_path . '/' . $username;
        $this->log->write_log('debug', $this->TAG . ': Upload Path: ' . $upload_path);
        if (!is_dir($upload_path)) {
            mkdir($upload_path);
        }

        $upload_config = [
            'upload_path' => $upload_path,
            'allowed_types' => '*',
            'overwrite' => true,
            'file_ext_tolower' => true
        ];
        $upload_config['file_name'] = $avatar_name;
        $this->upload->initialize($upload_config);
        if (!$this->upload->do_upload('avatar')) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Failed upload profile picture');
        }
        $avatar = $this->upload->data('file_name');

        $identity_name = 'IDP_' . random_string('alnum', 16);
        $upload_config['file_name'] = $identity_name;
        $this->upload->initialize($upload_config);
        if (!$this->upload->do_upload('identity_picture')) {
            // Delete uploaded avatar and the directory
            unlink($upload_path . '/' . $avatar);
            rmdir($upload_path);
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Failed upload identity picture');
        }
        $identity = $this->upload->data('file_name');

        $result = $this->m_user->create($id,
                                        $username,
                                        $email,
                                        $password,
                                        $full_name,
                                        $phone,
                                        $gender,
                                        $birth_date,
                                        $avatar,
                                        $identity_number,
                                        $identity,
                                        $address,
                                        $device_id,
                                        User_data::$STATUS_NOT_ACTIVE,
                                        $type);
        if ($result) {
            if ($type == 2) {
                $garage_name = $this->input->post('garage_name');
                $garage_open = $this->input->post('garage_open');
                $garage_close = $this->input->post('garage_close');
                $garage_address = $this->input->post('garage_address');
                $garage_latitude = $this->input->post('garage_latitude');
                $garage_longitude = $this->input->post('garage_longitude');
                if (empty($garage_name) && empty($garage_open) && empty($garage_close) && empty($garage_address)
                    && empty($garage_latitude)
                    && empty($garage_longitude)) {
                    $this->m_user->delete_pure($id);
                    $this->response_error(STATUS_CODE_SERVER_ERROR, 'Data Garage not complete');
                }
                $this->load->model('m_garage');
                $result_garage = $this->m_garage->create($id,
                                                         $garage_name,
                                                         $garage_open,
                                                         $garage_close,
                                                         $garage_address,
                                                         $garage_latitude,
                                                         $garage_longitude);
                if (!$result_garage) {
                    $this->m_user->delete_pure($id);
                    $this->log->write_log('error',
                                          $this->TAG . ':  sign_up: Inserting data garage error, rollback user');
                    $this->response_error(STATUS_CODE_SERVER_ERROR, 'Something wrong when create user');
                }
            }
            $this->load->model('m_type');
            $this->load->model('m_config');
            $help_type = $this->m_type->get();
            $help_type_id = [];
            foreach ($help_type as $key => $value) {
                $help_type_id[] = $value->ID;
            }
            $this->m_config->create($id, $help_type_id);

            $this->response(['message' => 'Success create user',
                             'token' => $this->create_token([$username, $device_id])]);
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
     * @throws \BadFunctionCallException
     */
    public function signout_get()
    {
        $this->log->write_log('debug', $this->TAG . ': sign_out: ');
        if (!$this->check_access()) {
            $this->response_404();
        }
        $this->load->model('m_user');
        $user = $this->get_user();
        $result = $this->m_user->update_device_id($user->ID, null);

        if ($result) {
            $this->response_error(0, 'Success logout');
        } else {
            $this->response_error(1, 'Failed logout');
        }
    }

    /**
     *
     * @throws \BadFunctionCallException
     */
    public function check_status_get()
    {
        $this->log->write_log('debug', $this->TAG . ': check: ');
        $status = null;
        $can_access = $this->check_access();
        $user = $this->get_user();
        if ($can_access) {
            if (!empty($user)) {
                $this->load->helper('assets_helper');
                $status = [
                    'username' => $user->USERNAME,
                    'email' => $user->EMAIL,
                    'full_name' => $user->FULL_NAME,
                    'gender' => $user->GENDER,
                    'status' => $user->STATUS,
                    'type' => $user->TYPE,
                    'avatar' => avatar_user_url_return($user)
                ];
            }
        } else {
            if (!empty($user)) {
                $status = [
                    'username' => $user->USERNAME,
                    'status' => $user->STATUS,
                    'type' => $user->TYPE
                ];
            }
        }
        $this->response($status);
    }

    /**
     * @throws \BadFunctionCallException
     * @throws \Exception
     * @throws \LogicException
     */
    public function update_device_id_post()
    {
        $this->log->write_log('debug', $this->TAG . ': update_device_id: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $device_id = $this->input->post('device_id');
        if (empty($device_id)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $this->load->model('m_user');
        $result = $this->m_user->update_device_id($user->ID, $device_id);
        if ($result) {
            $this->response($this->create_token([$user->USERNAME, $device_id]));
        } else {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Failed update device id');
        }
    }

    /**
     * @throws \BadFunctionCallException
     * @throws \Exception
     */
    public function update_password_post()
    {
        $this->log->write_log('debug', $this->TAG . ': update_password: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');
        if (empty($old_password) || empty($new_password)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $this->load->model('m_user');
        $old_users = $this->m_user->get($user->USERNAME);
        if (empty($old_users)) {
            $this->response_error(500, "Cant get user");
        }
        $old_user = $old_users[0];
        if (!password_verify($old_password, $old_user->PASSWORD)) {
            $this->response_error(501, "Wrong old password");
        }
        $result = $this->m_user->update_password($user->ID, $new_password);
        if (!empty($result)) {
            $this->response(null);
        } else {
            $this->response_error(502, "Failed update password");
        }

    }

}
