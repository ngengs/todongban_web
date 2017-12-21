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
 * Class Config
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Garage extends TDB_Controller
{
    private $TAG = 'Garage';

    /**
     * Config constructor.
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
        $this->load->model('m_garage');
        $garages = $this->m_garage->get($user->ID);
        if (empty($garages)) {
            $this->response_404();
        }
        $garage = $garages[0];
        $garage->__cast();
        $this->response([
                            'id' => $garage->ID,
                            'name' => $garage->NAME,
                            'open' => date('y-m-d H:i:s', strtotime($garage->OPEN_HOUR)),
                            'close' => date('y-m-d H:i:s', strtotime($garage->CLOSE_HOUR)),
                            'address' => $garage->ADDRESS,
                            'latitude' => $garage->LATITUDE,
                            'longitude' => $garage->LONGITUDE,
                            'force_close' => $garage->FORCE_CLOSE,
                        ]);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function index_post()
    {
        $this->log->write_log('debug', $this->TAG . ': index: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $open_hour = $this->input->post('garage_open');
        $close_hour = $this->input->post('garage_close');
        $force_close = $this->input->post('garage_force_close');
//        $this->response(json_encode(['un_open'=>$open_hour,'open'=>date('y-m-d H:i:s', strtotime($open_hour)), 'close'=>date('H:i:s', strtotime($close_hour))]));
        if (is_null($open_hour) || is_null($close_hour) || is_null($force_close)) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Data not complete');
        }
        $user = $this->get_user();
        $this->load->model('m_garage');
        $this->m_garage->edit($user->ID, $open_hour, $close_hour, (int)$force_close);
        $this->response(null);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function statistic_post()
    {
        $this->log->write_log('debug', $this->TAG . ': statistic: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');
        $date_start_time = strtotime($date_start);
        $date_end_time = strtotime($date_end);
        $total_day = ceil(abs($date_end_time - $date_start_time) / 86400);
        $user = $this->get_user();
        if ($user->TYPE == User_data::$TYPE_PERSONAL) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
        $this->load->model('m_garage');
        $garages = $this->m_garage->get($user->ID);
        $garage = $garages[0];
        $garage->__cast();
        $this->load->model('m_location');
        $result = $this->m_location->get_nearby_request($garage->LATITUDE, $garage->LONGITUDE, $date_start, $date_end);
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]->__cast_date();
        }
        $data = [];
        for ($i = 0; $i <= $total_day; $i++) {
            $data[$i] = [
                'motor_1' => 0,
                'motor_1_id' => Config_data::$TYPE_MOTORCYCLE_FLAT_TIRE,
                'motor_2' => 0,
                'motor_2_id' => Config_data::$TYPE_MOTORCYCLE_NO_FUEL,
                'motor_3' => 0,
                'motor_3_id' => Config_data::$TYPE_MOTORCYCLE_BROKEN,
                'car_1' => 0,
                'car_1_id' => Config_data::$TYPE_CAR_FLAT_TIRE,
                'car_2' => 0,
                'car_2_id' => Config_data::$TYPE_CAR_NO_FUEL,
                'car_3' => 0,
                'car_3_id' => Config_data::$TYPE_CAR_BROKEN,
                'total' => 0
            ];
        }
        for ($i = 0; $i < count($data); $i++) {
            $date_check_time = strtotime('+' . $i . ' day', $date_start_time);
            $date_check = date('Y-m-d', $date_check_time);
            $data[$i]['date'] = date('Y-m-d H:i:s', $date_check_time);
            foreach ($result as $item) {
                $item->__cast_date();
                if ($date_check == $item->DATE_CREATE) {
                    $data[$i]['total']++;
                    switch ($item->ID_HELP_TYPE) {
                        case Config_data::$TYPE_MOTORCYCLE_FLAT_TIRE:
                            $data[$i]['motor_1']++;
                            break;
                        case Config_data::$TYPE_MOTORCYCLE_NO_FUEL:
                            $data[$i]['motor_2']++;
                            break;
                        case Config_data::$TYPE_MOTORCYCLE_BROKEN:
                            $data[$i]['motor_3']++;
                            break;
                        case Config_data::$TYPE_CAR_FLAT_TIRE:
                            $data[$i]['car_1']++;
                            break;
                        case Config_data::$TYPE_CAR_NO_FUEL:
                            $data[$i]['car_2']++;
                            break;
                        case Config_data::$TYPE_CAR_BROKEN:
                            $data[$i]['car_3']++;
                            break;
                    }
                }
            }
        }
        $this->response($data);
    }

}
