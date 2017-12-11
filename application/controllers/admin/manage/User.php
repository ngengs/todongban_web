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
 */
class User extends TDB_Controller
{
    private $TAG = 'User';

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->check_access(true);
    }

    public function index()
    {
        $data = $this->basic_data();
        $data['title'] = 'Pengguna';
        $data['menu'] = 30;
        $data['breadcrumb'] = [['title' => 'pengguna']];
        $data['users'] = $this->m_user->get(null, null, 1, 1, [1, 2]);
        $this->load->view('admin/base/header', $data);
        $this->load->view('admin/manage/user/list', $data);
        $this->load->view('admin/base/footer', $data);
    }

    public function user_banned()
    {
        $data = $this->basic_data();
        $data['title'] = 'Pengguna Terblokir';
        $data['menu'] = 31;
        $data['breadcrumb'] = [['title' => 'pengguna']];
        $data['users'] = $this->m_user->get(null, null, 4, 1, [1, 2]);
        $this->load->view('admin/base/header', $data);
        $this->load->view('admin/manage/user/list', $data);
        $this->load->view('admin/base/footer', $data);
    }

    public function detail($id, $validation = null)
    {
        $from_validation = (strtolower($validation) == 'validation');
        $this->log->write_log('debug', $this->TAG . ': validation_detail: ');
        $registered = $this->m_user->get(null, null, ($from_validation) ? [2, 3] : [1, 4], $id);
        if (!empty($registered)) {
            $registered = $registered[0];
            $data = $this->basic_data();
            $data['title'] = 'Pengguna';
            $data['sub_title'] = 'Detail';
            $data['menu'] = 30;
            if ($registered->STATUS == 4) {
                $data['menu'] = 31;
            }
            $data['breadcrumb'] = [
                ['url' => 'admin/manage/user', 'title' => 'pengguna'],
                ['title' => 'detail']
            ];
            $data['from_validation'] = $from_validation;
            if ($registered->ID != $registered->ID_UPDATE) {
                $last_update_by = $this->m_user->get(null, null, null, $registered->ID_UPDATE);
                if (!empty($last_update_by)) {
                    $data['last_update_by'] = $last_update_by[0]->FULL_NAME;
                }
            }
            $data['registered'] = $registered;
            $this->load->view('admin/base/header', $data);
            $this->load->view('admin/manage/user/detail', $data);
            $this->load->view('admin/base/footer', $data);
        } else {
            $this->session->set_flashdata('error',
                                          'User tidak ditemukan' .
                                          ($from_validation ? ' atau telah berubah status' : ''));
            if (!$from_validation) {
                redirect('admin/manage/register/validation');
            } else {
                redirect('admin/manage/user');
            }
        }
    }

    public function unbanned($id)
    {
        $this->log->write_log('debug', $this->TAG . ': validate: ');
        $user = $this->get_user();
        $this->m_user->update_status($id, User_data::$STATUS_ACTIVE, $user->ID);
        redirect('admin/manage/user/detail/' . $id);
    }

    public function banned($id)
    {
        $this->log->write_log('debug', $this->TAG . ': validate: ');
        $user = $this->get_user();
        $this->m_user->update_status($id, User_data::$STATUS_BANNED, $user->ID);
        redirect('admin/manage/user/detail/' . $id);
    }

    public function test($id){
        $this->load->model('m_config');

        $this->output->set_content_type('application/json');
        echo json_encode($this->m_config->get($id));
    }


}
