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
 * Class Auth
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Auth extends TDB_Controller
{
    private $TAG = 'Auth';

    /**
     * Auth constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->log->write_log('debug', $this->TAG . ': index: ');
        $data = [];
        $data['next'] = $this->__process_next();
        redirect('admin/auth/signin' . (!empty($data['next']) ? '?next=' . $data['next'] : ''));
    }

    public function generate_password($password)
    {
        echo password_hash($password, PASSWORD_BCRYPT);
    }

    public function signin()
    {
        $this->log->write_log('debug', $this->TAG . ': signin: ');
        if (!$this->check_access()) {
            $data = $this->basic_data();
            $data['next'] = $this->__process_next();
            $this->load->view('admin/auth/signin', $data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function signin_process()
    {
        $this->log->write_log('debug', $this->TAG . ': signin_process: ');
        $data = [];
        $data['next'] = $this->__process_next(false);
        if (!$this->check_access()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            if (empty($username) || empty($password)) {
                redirect('admin/auth');
                die;
            }

            $this->load->model('m_user');
            $user = $this->m_user->get($username);
            if (!empty($user)) {
                $user = $user[0];
                if (password_verify($password, $user->PASSWORD)) {
                    if ($user->TYPE == User_data::$TYPE_ADMIN && $user->STATUS == User_data::$STATUS_ACTIVE) {
                        $this->session->set_userdata('session_user', $user->USERNAME);

                        if (!empty($data['next'])) {
                            redirect(urldecode(base64_decode($data['next'])));
                            die;
                        }
                    }
                }
            }
        }
        redirect('admin/auth/' . (!empty($data['next']) ? '?next=' . $data['next'] : ''));
    }

    public function signout()
    {
        if ($this->check_access()) {
            $this->session->sess_destroy();
        }
        redirect('admin/auth');
    }

    private function __process_next(bool $get = true): ?string
    {
        $data = null;
        $next_url = null;
        if ($get) {
            $next_url = $this->input->get('next');
        } else {
            $next_url = $this->input->post('next');
        }
        if (!empty($next_url)) {
            $new_next_url = urldecode(base64_decode($next_url));
            $parse_next_url = parse_url($new_next_url);
            $parse_app_url = parse_url(base_url());
            if ($parse_next_url != false && $parse_app_url != false) {
                if (strtolower($parse_app_url['host']) == strtolower($parse_next_url['host'])) {
                    $data = $next_url;
                }
            }
        }

        return $data;
    }

}
