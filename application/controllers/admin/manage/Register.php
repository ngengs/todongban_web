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
 * Class Register
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Register extends TDB_Controller
{
    private $TAG = 'Register';

    /**
     * Register constructor.
     * @throws \BadFunctionCallException
     */
    public function __construct()
    {
        parent::__construct();
        $this->check_access(true);
    }

    public function index()
    {
        redirect('admin/manage/register/validation');
    }

    public function validation()
    {
        $this->log->write_log('debug', $this->TAG . ': validation: ');
        $data = $this->basic_data();
        $data['title'] = 'Validasi';
        $data['menu'] = 20;
        $data['breadcrumb'] = [['title' => 'validasi']];
        $data['registered_garage'] = $this->m_user->get(null,
                                                        null,
                                                        User_data::$STATUS_NOT_ACTIVE,
                                                        null,
                                                        User_data::$TYPE_GARAGE);
        $data['registered_personal'] = $this->m_user->get(null,
                                                          null,
                                                          User_data::$STATUS_NOT_ACTIVE,
                                                          null,
                                                          User_data::$TYPE_PERSONAL);
        $this->load->view('admin/base/header', $data);
        $this->load->view('admin/manage/register/validation_list', $data);
        $this->load->view('admin/base/footer', $data);
    }

    /**
     * @param $id
     *
     * @throws \Exception
     */
    public function validate($id)
    {
        $this->log->write_log('debug', $this->TAG . ': validate: ');
        $user = $this->get_user();
        $result = $this->m_user->update_status($id, User_data::$STATUS_ACTIVE, $user->ID);
        if ($result) {
            $registered = $this->m_user->get(null, null, null, $id);
            $this->log->write_log('debug', $this->TAG . ': validate: user:' . json_encode($registered));
            if (!empty($registered)) {
                $registered = $registered[0];
                if (!empty($registered->DEVICE_ID)) {
                    $this->load->library('fcm');
                    $title = 'Status Verifikasi ' . $this->config->item('app_name');
                    $message =
                        'Pendaftaran anda telah di verifikasi, silahkan menggunakan aplikasi dan membantu sesama';
                    $this->fcm->set_target($registered->DEVICE_ID)
                              ->set_key($this->config->item('fcm_key', 'sensitive'))
                              ->set_title($title)
                              ->set_message($message)
                              ->set_code(Fcm::CODE_REGISTER_COMPLETE)
                              ->send();

                    $message = $message . ' pada ' . date('d M Y H:i');
                    $this->send_email($registered->EMAIL, $title, $message);
                }
            }
        }
        redirect('admin/manage/register');
    }


}
