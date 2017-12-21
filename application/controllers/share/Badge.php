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
 * Class Badge
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Badge extends TDB_Controller
{
    private $TAG = 'Badge';

    public function __construct() { parent::__construct(false); }

    public function index($username)
    {
        $this->log->write_log('debug', $this->TAG . ': index: ' . $username);
        $this->load->model('m_user');
        $this->load->model('m_help');
        $users = $this->m_user->get($username);
        if (empty($users)) {
            $this->response_404();
        }
        $user = $users[0];
        $user->__cast();
        $badge = $this->m_help->badge($user->ID);
        $badge_data = new Badge_data($badge, $user->TYPE);
        $badge_name = $badge_data->badge_name();
        $response_count = $this->m_help->response_count($user->ID);
        $data = [];
        $data['user'] = $user;
        $data['badge'] = $badge;
        $data['garage'] = null;
        if ($user->TYPE == User_data::$TYPE_GARAGE) {
            $this->load->model('m_garage');
            $garage = $this->m_garage->get($user->ID);
            if (!empty($garage)) {
                $data['garage'] = $garage[0];
            }
        }
        $data['badge_name'] = $badge_name;
        $data['response_count'] = $response_count;
        $this->load->helper('assets_helper');
        $this->load->view('share/badge', $data);
    }

}
