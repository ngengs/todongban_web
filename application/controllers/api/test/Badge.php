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

    public function __construct() { parent::__construct(true); }

    /**
     * @throws \BadFunctionCallException
     */
    public function check_get()
    {
        $this->load->helper('score_helper');
        $five = 1;
        $four = 0;
        $three = 0;
        $two = 1;
        $one = 1;
//        $weight = five_star_rating_weight(0,0,0,0,1);
//        $weight = five_star_rating_avg_weight(4,6);
        $weight = ((5 * $five) + (4 * $four) + (3 * $three) + (2 * $two) + (1 * $one)) / ($one + $two + $three + $four
                                                                                          + $five);
        $this->response((int)$weight);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function test_post()
    {
        $this->check_access();
        $id_request = $this->input->post('id_request');
        $this->load->model('m_help');
        $data = $this->m_help->get_user_response($id_request, $this->get_user()->ID);
        $data[0]->__cast();
        $this->response($data[0]);
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function date_post()
    {

        $this->log->write_log('debug', $this->TAG . ': date: ');
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Cant access');
        }
//        $is_valid = false;
        $date_start = $this->input->post('date_start');
        $date_end = $this->input->post('date_end');
        $date_start_time = strtotime($date_start);
        $date_end_time = strtotime($date_end);
        $total_day = ceil(abs($date_end_time - $date_start_time) / 86400);
//        echo $total_day;die;
//        if (strtotime("+6 day", $date_start_time) == $date_end_time) {
//            $is_valid = true;
//        }
//        if(!$is_valid){
//            $this->response_error(404, 'Not valid date range');
//        }
//        $this->response([$is_valid, date('Y-m-d H:i:s',strtotime("+7 day", $date_start_time)), $date_start_time,
//                                         $date_end_time]);
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
                'motor_2' => 0,
                'motor_3' => 0,
                'car_1' => 0,
                'car_2' => 0,
                'car_3' => 0,
                'total' => 0
            ];
        }
        for ($i = 0; $i < count($data); $i++) {
            $date_check_time = strtotime('+' . $i . ' day', $date_start_time);
            $date_check = date('Y-m-d', $date_check_time);
            $data[$i]['date'] = date('Y-m-d H:i:s', $date_check_time);
//            echo $date_check.' ';
            foreach ($result as $item) {
                $item->__cast_date();
                if ($date_check == $item->DATE_CREATE) {
                    $data[$i]['total']++;
                    switch ($item->ID_HELP_TYPE) {
                        case Config_data::$TYPE_MOTORCYCLE_FLAT_TIRE:
//                            echo 'DISINI 1 ';
                            $data[$i]['motor_1']++;
                            break;
                        case Config_data::$TYPE_MOTORCYCLE_NO_FUEL:
//                            echo 'DISINI 2 ';
                            $data[$i]['motor_2']++;
                            break;
                        case Config_data::$TYPE_MOTORCYCLE_BROKEN:
//                            echo 'DISINI 3 ';
                            $data[$i]['motor_3']++;
                            break;
                        case Config_data::$TYPE_CAR_FLAT_TIRE:
//                            echo 'DISINI 4 ';
                            $data[$i]['car_1']++;
                            break;
                        case Config_data::$TYPE_CAR_NO_FUEL:
//                            echo 'DISINI 5 ';
                            $data[$i]['car_2']++;
                            break;
                        case Config_data::$TYPE_CAR_BROKEN:
//                            echo 'DISINI 6 ';
                            $data[$i]['car_3']++;
                            break;
                    }
                }
            }
        }
//        die;
        $this->response($data);
//        $this->response($result);
    }


}
