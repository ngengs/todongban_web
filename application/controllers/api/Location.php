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
     * @throws \BadFunctionCallException
     * @throws \Exception
     */
    public function index_post()
    {
        $this->log->write_log('debug', 'Location: index_post:');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $latitude = (double)$this->input->post('latitude');
        $longitude = (double)$this->input->post('longitude');
        if (empty($longitude) || empty($latitude)) {
            $this->response_error(STATUS_CODE_SERVER_ERROR, 'Data not complete');
        }
        $user = $this->get_user();
        $result = $this->m_location->insert_location($user->ID, $latitude, $longitude);
        if ($result) {
            $this->response('Success Update Location');
        } else {
            $this->response_error(STATUS_CODE_SERVER_ERROR, 'Fail update location');
        }
    }
}
