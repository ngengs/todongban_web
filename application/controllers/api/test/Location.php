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

    /**
     * @param null $count
     *
     * @throws \BadFunctionCallException
     * @throws \Exception
     */
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

    /**
     * @throws \BadFunctionCallException
     */
    public function distance_response_post()
    {

        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $id_response = $this->input->post('id_response');
        if (empty($id_response)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $this->load->model('m_help');
        $responses = $this->m_help->get_request_by_response($id_response);//, $user->ID);
        /**
         * @var \Help_response_data|null $response
         */
        $response = null;
        if (!empty($responses)) {
            $response = $responses[0];
            $response->__cast();
        }
        $id_request = $response->ID;//$this->input->post('id_request');
        $distance = 0;//$this->input->post('distance');
        if (empty($id_request)) {//} || is_null($distance)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete: ' . $response);
        }
        $helps = $this->m_help->get_request($id_request);//, $user->ID);
        /**
         * @var \Help_request_data|null $help
         */
        $help = null;
        if (!empty($helps)) {
            $help = $helps[0];
        }
//        var_dump($helps);die;
        $this->load->model('m_location');
        if ($user->TYPE == User_data::$TYPE_PERSONAL) {
            $distance = $this->m_location->distance_from_current_location_personal($user->ID,
                                                                                   $help->LATITUDE,
                                                                                   $help->LONGITUDE);
        } else {
            $distance = $this->m_location->distance_from_current_location_garage($user->ID,
                                                                                 $help->LATITUDE,
                                                                                 $help->LONGITUDE);
        }
        $this->response((float)$distance);
    }

}
