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

    /**
     * Badge constructor.
     */
    public function __construct()
    {
        parent::__construct(true);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function index_get()
    {
        $this->log->write_log('debug', $this->TAG . ': index: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $user = $this->get_user();
        $name = $user->FULL_NAME;
        if ($user->TYPE == User_data::$TYPE_GARAGE) {
            $this->load->model('m_garage');
            $garages = $this->m_garage->get($user->ID);
            $garage = $garages[0];
            $name = $garage->NAME;
        }
        $this->load->model('m_help');
        $badge = $this->m_help->badge($user->ID);
        $badge_data = new Badge_data($badge, $user->TYPE);
        $response_count = $this->m_help->response_count($user->ID);
        $this->load->helper('assets_helper');
        $this->response(
            [
                'name' => $name,
                'avatar' => avatar_user_url_return($user),
                'user_type' => $user->TYPE,
                'badge' => $badge_data->BADGE,
                'response_count' => $response_count,
                'share_url' => base_url('share/badge/' . $user->USERNAME)
            ]
        );
    }

}
