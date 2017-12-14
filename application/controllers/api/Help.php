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

        $handler = new \GuzzleHttp\Handler\CurlMultiHandler();
        $client = new \GuzzleHttp\Client(['curl' => [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_TIMEOUT => 1],
                                          'handler' =>
                                              \GuzzleHttp\HandlerStack::create($handler)]);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $promise = $client->requestAsync('POST',
                                         base_url('api/help/helper_search'),
                                         [
                                             'headers' => ['Authorization' => 'Bearer ' . $this->get_token()],
                                             'form_params' => [
                                                 'id_request' => $result,
                                             ]
                                         ]);
        $handler->execute();
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

    /**
     * @throws \BadFunctionCallException
     * @throws \Exception
     */
    public function helper_search_post()
    {
        $this->log->write_log('debug', $this->TAG . ': helper_search: ');
        ini_set('max_execution_time', 100);
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $id_request = $this->input->post('id_request');
        if (empty($id_request)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $this->load->model('m_help');
        $helps = $this->m_help->get_request($id_request, $user->ID);
        if (!empty($helps)) {
            $help = $helps[0];
            $this->load->model('m_location');
            $this->log->write_log('debug', $this->TAG . ': helper_search: help_request data: ' . $help);
            $nearby_garages = $this->m_location->get_nearby_garage($user->ID,
                                                                   $help->ID_HELP_TYPE,
                                                                   $help->LATITUDE,
                                                                   $help->LONGITUDE,
                                                                   $help->DATE_CREATE);
//            $this->response($nearby_garages);

            $this->log->write_log('debug', $this->TAG . ': helper_search: garage: ' . json_encode($nearby_garages));
            // Lets save help response from garage
            $id_insert_garages = [];
            $generated_id = $this->m_location->generate_id(count($nearby_garages));
            if (is_array($generated_id)) {
                $id_insert_garages = $generated_id;
            } else {
                $id_insert_garages[] = $generated_id;
            }
            $insert_garages = [];
            $date_now = date('Y-m-d H:i:s');
            for ($i = 0; $i < count($nearby_garages); $i++) {
                $garage = $nearby_garages[$i];
                $garage->__cast();
                $insert_garages[] = [
                    'ID' => $id_insert_garages[$i],
                    'ID_USER' => $garage->ID_USER,
                    'ID_HELP_REQUEST' => $help->ID,
                    'RESPONSE' => Help_response_data::$RESPONSE_WAITING,
                    'ID_CREATE' => $user->ID,
                    'ID_UPDATE' => null,
                    'DATE_CREATE' => $date_now,
                    'DATE_UPDATE' => null,
                    'STATUS' => Help_response_data::$STATUS_NOT_SELECTED
                ];
            }
            $this->m_help->insert_response($insert_garages);
            $this->load->library('fcm');
//            $this->config->load('sensitive', true);
//            $fcm_key = $this->config->item('fcm_key', 'sensitive');
//            $this->response($insert_garages);

//             Lets send notification to user and user
            for ($i = 0; $i < count($nearby_garages); $i++) {
                $garage = $nearby_garages[$i];
                $garage->__cast();
                $this->fcm->set_target($user->DEVICE_ID)
//                          ->set_key($fcm_key)
                          ->set_code(Fcm::CODE_HELP_RESPONSE)
                          ->set_payloads([
                                             'id' => $id_insert_garages[$i],
                                             'name' => $garage->NAME,
                                             'badge' => 0,
                                             'distance' => $garage->DISTANCE,
                                             'user_type' => User_data::$TYPE_GARAGE
                                         ])
                          ->send();
                $this->fcm->reset();
                $this->fcm->set_target($garage->DEVICE_ID)
//                          ->set_key($fcm_key)
                          ->set_code(Fcm::CODE_HELP_SEARCH_GARAGE)
                          ->set_payloads([
                                             'id' => $id_insert_garages[$i],
                                             'name' => $user->FULL_NAME,
                                             'help_type' => $help->ID_HELP_TYPE,
                                             'distance' => $garage->DISTANCE
                                         ])
                          ->send();
                $this->fcm->reset();
            }

            $nearby_personal = $this->m_location->get_nearby_personal($user->ID,
                                                                      $help->ID_HELP_TYPE,
                                                                      $help->LATITUDE,
                                                                      $help->LONGITUDE);
//            Lets save help response from personal
            $id_insert_personal = [];
            $generated_id = $this->m_location->generate_id(count($nearby_personal));
            if (is_array($generated_id)) {
                $id_insert_personal = $generated_id;
            } else {
                $id_insert_personal[] = $generated_id;
            }

            $insert_personal = [];
            $date_now = date('Y-m-d H:i:s');
            for ($i = 0; $i < count($nearby_personal); $i++) {
                $personal = $nearby_personal[$i];
                $personal->__cast();
                $insert_personal[] = [
                    'ID' => $id_insert_personal[$i],
                    'ID_USER' => $personal->ID_USER,
                    'ID_HELP_REQUEST' => $help->ID,
                    'RESPONSE' => Help_response_data::$RESPONSE_WAITING,
                    'ID_CREATE' => $user->ID,
                    'ID_UPDATE' => null,
                    'DATE_CREATE' => $date_now,
                    'DATE_UPDATE' => null,
                    'STATUS' => Help_response_data::$STATUS_NOT_SELECTED
                ];
            }
            $this->m_help->insert_response($insert_personal);
//             Lets send notification to user and user
            for ($i = 0; $i < count($nearby_personal); $i++) {
                $personal = $nearby_personal[$i];
                $personal->__cast();
//                $this->fcm->set_target($user->DEVICE_ID)
////                          ->set_key($fcm_key)
//                          ->set_code(Fcm::CODE_HELP_RESPONSE)
//                          ->set_payloads([
//                                             'id' => $id_insert_personal[$i],
//                                             'name' => $personal->NAME,
//                                             'badge' => 0,
//                                             'distance' => $personal->DISTANCE,
//                                             'user_type' => User_data::$TYPE_PERSONAL
//                                         ])
//                          ->send();
//                $this->fcm->reset();
                $this->fcm->set_target($personal->DEVICE_ID)
//                          ->set_key($fcm_key)
                          ->set_code(Fcm::CODE_HELP_SEARCH_PERSONAL)
                          ->set_payloads([
                                             'id' => $id_insert_personal[$i],
                                             'name' => $user->FULL_NAME,
                                             'help_type' => $help->ID_HELP_TYPE,
                                             'distance' => $personal->DISTANCE
                                         ])
                          ->send();
                $this->fcm->reset();
            }
        }

    }

}
