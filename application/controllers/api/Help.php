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
        $this->load->model('m_help');
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
        $this->m_help->cancel_request($id_request, $user->ID);
        $this->response(null);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function response_search_detail_post()
    {
        $this->log->write_log('debug', $this->TAG . ': request_search_detail: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $id_response = $this->input->post('id_response');
        if (empty($id_response)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $results = $this->m_help->get_request_by_response($id_response, $user->ID);
        $data = null;
        if (!empty($results)) {
            $request = $results[0];
            $this->load->helper('assets_helper');
            $requested_user = new User_data();
            $requested_user->AVATAR = $request->AVATAR;
            $requested_user->USERNAME = $request->USERNAME;
            $avatar_url = avatar_user_url_return($requested_user);
            $data = [
                'id' => $request->ID,
                'avatar' => $avatar_url,
                'name' => $request->FULL_NAME,
                'help_type' => $request->ID_HELP_TYPE,
                'message' => $request->MESSAGE,
                'latitude' => $request->LATITUDE,
                'longitude' => $request->LONGITUDE,
                'address' => $request->LOCATION_NAME
            ];
        }
        $this->response($data);
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
                                             'badge' => $this->m_help->badge($garage->ID_USER),
                                             'distance' => $garage->DISTANCE,
                                             'user_type' => User_data::$TYPE_GARAGE,
                                             'accept' => false
                                         ])
                          ->send();
                $this->fcm->reset();
                $this->fcm->set_target($garage->DEVICE_ID)
//                          ->set_key($fcm_key)
                          ->set_code(Fcm::CODE_HELP_REQUEST)
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
            $this->log->write_log('debug',
                                  $this->TAG . ': search_help: nearby_personal:' . json_encode
                                  ($nearby_personal));
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
                          ->set_code(Fcm::CODE_HELP_REQUEST)
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

    /**
     * @throws \BadFunctionCallException
     * @throws \Exception
     */
    public function help_response_reject_post()
    {
        $this->log->write_log('debug', $this->TAG . ': help_response_reject: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $id_response = $this->input->post('id_response');
        if (empty($id_response)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $responses = $this->m_help->get_request_by_response($id_response, $user->ID);
        /**
         * @var \Help_response_data|null $response
         */
        $response = null;
        if (!empty($responses)) {
            $response = $responses[0];
            $response->__cast();
        }
        $id_request = $response->ID;//$this->input->post('id_request');
        if (empty($id_request)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $helps = $this->m_help->get_request($id_request);
        if (!empty($helps)) {
            $help = $helps[0];
            $this->m_help->update_response($help->ID, $user->ID, null, Help_response_data::$RESPONSE_REJECT);
            if ($user->TYPE == User_data::$TYPE_GARAGE) {
                $this->log->write_log('debug', $this->TAG . ': help_response_reject: garage');
                $this->load->library('fcm');
                $response = $this->m_help->get_response($id_request, $user->ID);
                $response = $response[0];
                $this->fcm->set_target($help->ID_USER)
                          ->set_code(Fcm::CODE_HELP_RESPONSE_REJECTED)
                          ->set_payload('id', $response->ID)
                          ->send();
                $this->fcm->reset();
            } else {
                $this->log->write_log('debug', $this->TAG . ': help_response_reject: personal');
            }
        }
        $this->response(null);
    }

    /**
     * @throws \BadFunctionCallException
     * @throws \Exception
     */
    public function help_response_accept_post()
    {
        $this->log->write_log('debug', $this->TAG . ': help_response_accept: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $id_response = $this->input->post('id_response');
        if (empty($id_response)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $responses = $this->m_help->get_request_by_response($id_response, $user->ID);
        /**
         * @var \Help_response_data|null $response
         */
        $response = null;
        if (!empty($responses)) {
            $response = $responses[0];
            $response->__cast();
        }
        $id_request = $response->ID;//$this->input->post('id_request');
        /** @noinspection PhpUnusedLocalVariableInspection */
        $distance = 0;//$this->input->post('distance');
        if (empty($id_request)) {//} || is_null($distance)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $helps = $this->m_help->get_request($id_request);//, $user->ID);
        if (!empty($helps)) {
            $help = $helps[0];
            $this->m_help->update_response($help->ID, $user->ID, null, Help_response_data::$RESPONSE_ACCEPT);
            $name = $user->FULL_NAME;
            $this->load->model('m_location');
            if ($user->TYPE == User_data::$TYPE_GARAGE) {
                $this->log->write_log('debug', $this->TAG . ': help_response_accept: garage');
                $this->load->model('m_garage');
                $garage = $this->m_garage->get($user->ID);
                $garage = $garage[0];
                $name = $garage->NAME;
                $distance = $this->m_location->distance_from_current_location_garage($user->ID,
                                                                                     $help->LATITUDE,
                                                                                     $help->LONGITUDE);
            } else {
                $this->log->write_log('debug', $this->TAG . ': help_response_accept: personal');
                $distance = $this->m_location->distance_from_current_location_personal($user->ID,
                                                                                       $help->LATITUDE,
                                                                                       $help->LONGITUDE);
            }
            $this->load->library('fcm');
//            $response = $this->m_help->get_response($id_request, $user->ID);
//            $response = $response[0];
            $this->fcm->set_target($help->DEVICE_ID)
                      ->set_code(Fcm::CODE_HELP_RESPONSE_ACCEPTED)
                      ->set_payloads([
                                         'id' => $id_response,
                                         'name' => $name,
                                         'badge' => $this->m_help->badge($user->ID),
                                         'distance' => (float)$distance,
                                         'user_type' => $user->TYPE,
                                         'accept' => true
                                     ])
//                      ->set_payload('id', $id_response)
//                      ->set_payload('id', $response->ID)
//                      ->set_payload('name', $name)
//                      ->set_payload('badge', 0)
//                      ->set_payload('distance', (float)$distance)
//                      ->set_payload('user_type', $user->TYPE)
//                      ->set_payload('accept', true)
                      ->send();
            $this->log->write_log('debug', $this->TAG . ': accept: ' . $this->fcm);
            $this->fcm->reset();
            $this->response(null);
        }
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function select_response_post()
    {
        $this->log->write_log('debug', $this->TAG . ': select_response: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $id_response = $this->input->post('id_response');
        if (empty($id_response)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $responses = $this->m_help->get_request_by_response($id_response);
        $this->log->write_log('debug', $this->TAG . ': select_response: data:' . json_encode($responses));
        /**
         * @var \Help_response_data|null $response
         */
        $response = null;
        if (!empty($responses)) {
            $response = $responses[0];
            $response->__cast();
        }
        $id_request = $response->ID;
        if (empty($id_request)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $this->log->write_log('debug', $this->TAG . ': disini: ');
        $this->m_help->update_response($id_request,
                                       null,
                                       $response->ID_USER,
                                       null,
                                       null,
                                       Help_response_data::$STATUS_SELECTED,
                                       $id_response);
        $this->log->write_log('debug', $this->TAG . ': disitu: ');
        $this->m_help->process_request($id_request, $user->ID);
        $this->response(null);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function detail_response_post()
    {
        $this->log->write_log('debug', $this->TAG . ': detail_response: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $id_request = $this->input->post('id_request');
        if (empty($id_request)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $this->load->helper('assets_helper');
        $data = null;
        $result = $this->m_help->get_user_response($id_request, $user->ID);
        if (!empty($result)) {
            $temp = $result[0];
            $temp->__cast();
            $data = [
                'id' => $temp->ID,
                'username' => $temp->USERNAME,
                'email' => $temp->EMAIL,
                'full_name' => $temp->FULL_NAME,
                'phone' => $temp->PHONE,
                'gender' => $temp->GENDER,
                'avatar' => avatar_user_url_return($temp),
                'address' => $temp->ADDRESS,
                'type' => $temp->TYPE,
                'status' => $temp->STATUS,
                'latitude' => $temp->LATITUDE,
                'longitude' => $temp->LONGITUDE,
                'garage_name' => $temp->GARAGE_NAME,
            ];
        }
        $this->response($data);
    }

    /**
     * @throws \BadFunctionCallException
     * @throws \Exception
     */
    public function finish_request_post()
    {
        $this->log->write_log('debug', $this->TAG . ': finish_request: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $id_request = $this->input->post('id_request');
        $rating = $this->input->post('rating');
        if (empty($id_request) || empty($rating)) {
            $this->response_error(STATUS_CODE_KEY_EXPIRED, 'Data not complete');
        }
        $user = $this->get_user();
        $user_responses = $this->m_help->get_user_response($id_request, $user->ID);
        $user_response = $user_responses[0];
        $this->m_help->update_response($id_request, $user_response->ID, $user->ID, null, $rating);
        $this->m_help->finish_request($id_request, $user->ID);
        if (!empty($user_response->DEVICE_ID)) {
            $this->load->library('fcm');
            $this->fcm
                ->set_target($user_response->DEVICE_ID)
                ->set_code(Fcm::CODE_HELP_REQUEST_FINISH)
                ->send();
            $this->fcm->reset();
        }
        $this->response(null);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function history_response_get()
    {
        $this->log->write_log('debug', $this->TAG . ': history_response: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $user = $this->get_user();
        $histories = $this->m_help->history_response($user->ID);
        $data = [];
        foreach ($histories as $history) {
            $history->__cast();
            $data[] = [
                'help_type' => $history->ID_HELP_TYPE,
                'date' => $history->DATE_CREATE,
                'response' => $history->RESPONSE,
                'status' => $history->STATUS
            ];
        }
        $this->response($data);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function history_request_get()
    {
        $this->log->write_log('debug', $this->TAG . ': history_response: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $user = $this->get_user();
        $histories = $this->m_help->history_request($user->ID);
        $data = [];
        foreach ($histories as $history) {
            $history->__cast();
            $data[] = [
                'help_type' => $history->ID_HELP_TYPE,
                'date' => $history->DATE_CREATE,
                'response' => $history->RESPONSE,
                'status' => $history->STATUS
            ];
        }
        $this->response($data);
    }

}
