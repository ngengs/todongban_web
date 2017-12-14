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
    private $TAG = 'Location';

    public function __construct() { parent::__construct(true); }

    public function nearby_personal_post()
    {
        $id = $this->input->post('id');
        $latitude = (float)$this->input->post('latitude');
        $longitude = (float)$this->input->post('longitude');
        $id_help_type = $this->input->post('id_help_type');

        $this->load->model('m_location');
        $data_personal = $this->m_location->get_nearby_personal($id, $id_help_type, $latitude, $longitude);
        for ($i = 0; $i < count($data_personal); $i++) {
            $data_personal[$i]->__cast();
        }
        $data_garage =
            $this->m_location->get_nearby_garage($id, $id_help_type, $latitude, $longitude, date('Y-m-d H:i:s'));
        for ($i = 0; $i < count($data_garage); $i++) {
            $data_garage[$i]->__cast();
        }
        $this->response(['personal' => $data_personal, 'garage' => $data_garage]);
    }

    public function test_get($count = null)
    {
        $this->load->model('m_user');
        if (!is_null($count)) {
            $this->response($this->m_user->generate_id($count));
        } else {
            $this->response($this->m_user->generate_id());
        }
    }

    public function sampah_get()
    {
        $this->config->load('sensitive', true);
        $fcm_key = $this->config->item('maps_key', 'sensitive');
        echo $fcm_key;
    }

}
