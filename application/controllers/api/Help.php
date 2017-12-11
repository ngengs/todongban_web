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
 * Class Help
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Help extends TDB_Controller
{
    private $TAG = 'Help';

    /**
     * Help constructor.
     */
    public function __construct()
    {
        parent::__construct(true);
    }

    /**
     * @throws \BadFunctionCallException
     * @throws \Exception
     */
    public function request_post()
    {
        $this->log->write_log('debug', $this->TAG . ': request: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }

        $this->log->write_log('debug', $this->TAG . ': disini_1: ');

        $latitude = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');
        $help_type = $this->input->post('help_type');
        $message = $this->input->post('message');
        $location_name = $this->input->post('location_name');
        $this->log->write_log('debug', $this->TAG . ': disini_2: ');
        if (empty($latitude) || empty($longitude) || empty($help_type)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $this->load->model('m_help');
        $result = $this->m_help->insert_request($user->ID, $latitude, $longitude, $help_type, $message, $location_name);
        $this->response($result);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function request_cancel_post()
    {
        $this->log->write_log('debug', $this->TAG . ': request_cancel: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }

        $id_request = $this->input->post('id_request');
        if (empty($id_request)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $this->load->model('m_help');
        $this->m_help->cancel_request($id_request, $user->ID);
        $this->response(null);
    }

}
