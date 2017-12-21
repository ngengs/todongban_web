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
 * Class M_location
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class M_help extends TDB_Model
{
    private $TAG = 'M_location';

    /**
     * M_location constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Function to add user last location.
     * This will add new location with status active and update old location to non active.
     *
     * @param string $id_user User ID
     * @param float $latitude user last position Latitude
     * @param float $longitude user last position Longitude
     *
     * @param string $id_help_type
     * @param null|string $message
     * @param null|string $location_name
     *
     * @return string|null Inserted ID
     * @throws \Exception
     */
    public function insert_request(string $id_user, float $latitude, float $longitude, String $id_help_type,
        ?string $message, ?string $location_name)
    {
        $this->log->write_log('debug', $this->TAG . ': insert_request: ');
        $date = date('Y-m-d H:i:s');
        $id = $this->generate_id();
//		Generate insert query
        $this->db->set('ID', $id);
        $this->db->set('ID_USER', $id_user);
        $this->db->set('ID_HELP_TYPE', $id_help_type);
        $this->db->set('LATITUDE', $latitude);
        $this->db->set('LONGITUDE', $longitude);
        if (!empty($message)) {
            $this->db->set('MESSAGE', $message);
        }
        if (!empty($location_name)) {
            $this->db->set('LOCATION_NAME', $location_name);
        }
        $this->db->set('STATUS', Help_request_data::$STATUS_REQUESTED);
        $this->set_creator($id_user, $date);
        $this->set_updater($id_user, $date);
        $this->db->from('HELP_REQUEST');
        $result = $this->db->insert();

        if (!empty($result)) {
            return $id;
        } else {
            return null;
        }
    }

    /**
     * @param string $id_request
     * @param string $id_user
     * @param int $status
     *
     * @return mixed
     */
    public function update_request(string $id_request, string $id_user, int $status)
    {
        $this->log->write_log('debug', $this->TAG . ': cancel_request: ');
        $this->db->where('ID', $id_request);
        $this->db->where('ID_USER', $id_user);
        $this->db->set('STATUS', $status);
        $this->set_updater($id_user);
        $this->db->from('HELP_REQUEST');
        $result = $this->db->update();

        return $result;
    }

    /**
     * @param string $id_request
     * @param string $id_user
     *
     * @return mixed
     */
    public function cancel_request(string $id_request, string $id_user)
    {
        return $this->update_request($id_request, $id_user, Help_request_data::$STATUS_CANCELED);
//        $this->log->write_log('debug', $this->TAG . ': cancel_request: ');
//        $this->db->where('ID', $id_request);
//        $this->db->where('ID_USER', $id_user);
//        $this->db->set('STATUS', Help_request_data::$STATUS_CANCELED);
//        $this->set_updater($id_user);
//        $this->db->from('HELP_REQUEST');
//        $result = $this->db->update();
//
//        return $result;
    }

    /**
     * @param string $id_request
     * @param string $id_user
     *
     * @return mixed
     */
    public function process_request(string $id_request, string $id_user)
    {
        return $this->update_request($id_request, $id_user, Help_request_data::$STATUS_PROCESS);
//        $this->log->write_log('debug', $this->TAG . ': cancel_request: ');
//        $this->db->where('ID', $id_request);
//        $this->db->where('ID_USER', $id_user);
//        $this->db->set('STATUS', Help_request_data::$STATUS_PROCESS);
//        $this->set_updater($id_user);
//        $this->db->from('HELP_REQUEST');
//        $result = $this->db->update();
//
//        return $result;
    }

    /**
     * @param string $id_request
     * @param string $id_user
     *
     * @return mixed
     */
    public function finish_request(string $id_request, string $id_user)
    {
        return $this->update_request($id_request, $id_user, Help_request_data::$STATUS_FINISH);
//        $this->log->write_log('debug', $this->TAG . ': cancel_request: ');
//        $this->db->where('ID', $id_request);
//        $this->db->where('ID_USER', $id_user);
//        $this->db->set('STATUS', Help_request_data::$STATUS_FINISH);
//        $this->set_updater($id_user);
//        $this->db->from('HELP_REQUEST');
//        $result = $this->db->update();
//
//        return $result;
    }

    /**
     * @param string $id_request
     * @param string|null $id_user
     *
     * @return \Help_request_data[]
     */
    public function get_request(string $id_request, ?string $id_user = null)
    {
        $this->db->select('HELP_REQUEST.ID, HELP_REQUEST.ID_USER, HELP_REQUEST.ID_HELP_TYPE, HELP_REQUEST.MESSAGE, HELP_REQUEST.LATITUDE, HELP_REQUEST.LONGITUDE, HELP_REQUEST.LOCATION_NAME, HELP_REQUEST.ID_CREATE, HELP_REQUEST.DATE_CREATE, HELP_REQUEST.ID_UPDATE, HELP_REQUEST.DATE_UPDATE, HELP_REQUEST.STATUS, USER.DEVICE_ID');
        $this->db->where('HELP_REQUEST.ID', $id_request);
        if (!empty($id_user)) {
            $this->db->where('HELP_REQUEST.ID_USER', $id_user);
        }
        $this->db->from('HELP_REQUEST');
        $this->db->join('USER', 'HELP_REQUEST.ID_USER = USER.ID');
        $result = $this->db->get();

        return $result->result('Help_request_data');
    }

    /**
     * @param string $id_response
     * @param string|null $id_user
     *
     * @return \Help_request_data[]
     */
    public function get_request_by_response(string $id_response, ?string $id_user = null)
    {
        $this->db->select('HELP_REQUEST.ID, HELP_REQUEST.ID_USER, HELP_REQUEST.ID_HELP_TYPE, HELP_REQUEST.MESSAGE, HELP_REQUEST.LATITUDE, HELP_REQUEST.LONGITUDE, HELP_REQUEST.LOCATION_NAME, HELP_REQUEST.ID_CREATE, HELP_REQUEST.DATE_CREATE, HELP_REQUEST.ID_UPDATE, HELP_REQUEST.DATE_UPDATE, HELP_REQUEST.STATUS, USER.FULL_NAME, USER.USERNAME, USER.AVATAR');
        $this->db->from('HELP_RESPONSE');
        $this->db->join('HELP_REQUEST', 'HELP_REQUEST.ID = HELP_RESPONSE.ID_HELP_REQUEST');
        $this->db->join('USER', 'HELP_REQUEST.ID_USER = USER.ID');
        $this->db->where('HELP_RESPONSE.ID', $id_response);
        if (!empty($id_user)) {
            $this->db->where('HELP_RESPONSE.ID_USER', $id_user);
        }
//        echo $this->db->get_compiled_select();
        $result = $this->db->get();

        return $result->result('Help_request_data');
    }

    public function insert_response(?array $data = [])
    {
        if (!empty($data)) {
            $result = $this->db->insert_batch('HELP_RESPONSE', $data);

            return $result;
        }

        return null;
    }

    /**
     * @param string $id_request
     * @param null|string $id_user
     * @param null|string $id_user_request
     * @param null|int $response
     * @param null|int $rating
     * @param null|int $status
     * @param null|string $id_response
     */
    public function update_response(string $id_request, ?string $id_user = null, ?string $id_user_request = null,
        ?int $response = null, ?int $rating = null, ?int $status = null, ?string $id_response = null)
    {
        $this->log->write_log('debug', $this->TAG . ': update_response: start');
        if (!is_null($response)) {
            $this->db->set('RESPONSE', $response);
        }
        if (!is_null($rating)) {
            $this->db->set('RATING', $rating);
        }
        if (!is_null($status)) {
            $this->db->set('STATUS', $status);
        }
        $this->db->where('ID_HELP_REQUEST', $id_request);
        if (!is_null($id_user)) {
            $this->db->where('ID_USER', $id_user);
        }
        if (!is_null($id_user_request)) {
            $this->set_updater($id_user_request);
        } else {
            if (!is_null($id_user)) {
                $this->set_updater($id_user);
            }
        }
        if (!is_null($id_response)) {
            $this->db->where('ID', $id_response);
        }
        $this->db->from('HELP_RESPONSE');
        $this->log->write_log('debug', $this->TAG . ': update_response: finish_prepare_query');
        $query = $this->db->get_compiled_update();
        $this->log->write_log('debug', $this->TAG . ': update_response: QUERY: ' . $query);
//        $this->db->update();
        $this->db->query($query);
    }

    /**
     * @param string $id_request
     * @param string $id_user
     *
     * @return \Help_response_data[]
     */
    public function get_response(string $id_request, string $id_user)
    {
        $this->db->select('ID, ID_USER, ID_HELP_REQUEST, RESPONSE, RATING, STATUS, ID_CREATE, DATE_CREATE, ID_UPDATE, DATE_UPDATE');
        $this->db->where('ID_HELP_REQUEST', $id_request);
        $this->db->where('ID_USER', $id_user);
        $this->db->from('HELP_RESPONSE');
        $result = $this->db->get();

        return $result->result('Help_response_data');
    }


    public function badge(string $id_user)
    {
        $this->db->select('HELP_RESPONSE.ID, HELP_RESPONSE.RATING');
        $this->db->from('HELP_RESPONSE');
        $this->db->join('HELP_REQUEST', 'HELP_RESPONSE.ID_HELP_REQUEST = HELP_REQUEST.ID');
        $this->db->where('HELP_REQUEST.STATUS', Help_request_data::$STATUS_FINISH);
        $this->db->where('HELP_RESPONSE.STATUS', Help_response_data::$STATUS_SELECTED);
        $this->db->where('HELP_RESPONSE.ID_USER', $id_user);
        $query = $this->db->get();
        $result = $query->result();
        $badge = 0;
        if (!empty($result)) {
            $one = 0;
            $two = 0;
            $three = 0;
            $four = 0;
            $five = 0;
            foreach ($result as $item) {
                switch ($item->RATING) {
                    case 1:
                        $one++;
                        break;
                    case 2:
                        $two++;
                        break;
                    case 3:
                        $three++;
                        break;
                    case 4:
                        $four++;
                        break;
                    case 5:
                        $five++;
                        break;
                }
            }
            $sum = ($one + $two + $three + $four + $five);
            $total = ((5 * $five) + (4 * $four) + (3 * $three) + (2 * $two) + (1 * $one));
            $weight = $total / $sum;
            if ($sum > 0) {
                $badge_level = $sum * 10;
                $badge_weight = (int)$weight;
                $badge = $badge_level + $badge_weight;
            }
        }

        return $badge;
    }

    public function response_count(string $id_user)
    {
        $this->db->select('HELP_RESPONSE.ID, HELP_RESPONSE.RATING');
        $this->db->from('HELP_RESPONSE');
        $this->db->join('HELP_REQUEST', 'HELP_RESPONSE.ID_HELP_REQUEST = HELP_REQUEST.ID');
        $this->db->where('HELP_REQUEST.STATUS', Help_request_data::$STATUS_FINISH);
        $this->db->where('HELP_RESPONSE.STATUS', Help_response_data::$STATUS_SELECTED);
        $this->db->where('HELP_RESPONSE.ID_USER', $id_user);
        $query = $this->db->get();
        $result = $query->result();
        $response = 0;
        if (!empty($result)) {
            $response = count($result);
        }

        return $response;
    }

    /**
     * @param string $id_request
     * @param string $id_user_request
     *
     * @return \Help_response_user_data[]
     */
    public function get_user_response(string $id_request, ?string $id_user_request = null)
    {
        $this->db->select('USER.ID, USER.USERNAME, USER.EMAIL, USER.FULL_NAME, USER.PHONE, USER.GENDER, USER.ADDRESS, USER.IDENTITY_NUMBER, USER.IDENTITY_PICTURE, USER.DEVICE_ID, USER.TYPE, USER.AVATAR, USER_LOCATION.LATITUDE, USER_LOCATION.LONGITUDE, GARAGE.NAME as GARAGE_NAME, GARAGE.LATITUDE as GARAGE_LATITUDE, GARAGE.LONGITUDE as GARAGE_LONGITUDE, HELP_RESPONSE.STATUS, HELP_RESPONSE.ID_CREATE, HELP_RESPONSE.DATE_CREATE, HELP_RESPONSE.ID_UPDATE, HELP_RESPONSE.DATE_UPDATE');
        $this->db->from('HELP_REQUEST');
        $this->db->join('HELP_RESPONSE', 'HELP_RESPONSE.ID_HELP_REQUEST = HELP_REQUEST.ID', 'INNER');
        $this->db->join('USER', 'HELP_RESPONSE.ID_USER = USER.ID', 'INNER');
        $this->db->join('USER_LOCATION', 'USER_LOCATION.ID_USER = USER.ID AND USER_LOCATION.STATUS= 1', 'LEFT');
        $this->db->join('GARAGE', 'GARAGE.ID_USER = USER.ID', 'LEFT');
        $this->db->where('HELP_REQUEST.ID', $id_request);
        $this->db->where('HELP_RESPONSE.STATUS', 1);
        if (!is_null($id_user_request)) {
            $this->db->where('HELP_REQUEST.ID_USER', $id_user_request);
        }
        $result = $this->db->get();

        return $result->result('Help_response_user_data');
    }

    /**
     * @param string $id_user
     *
     * @return \History_data[]
     */
    public function history_response(string $id_user)
    {
        $this->db->select('HELP_REQUEST.ID_HELP_TYPE ,HELP_RESPONSE.DATE_CREATE, HELP_RESPONSE.RESPONSE, HELP_RESPONSE.STATUS');
        $this->db->from('HELP_RESPONSE');
        $this->db->join('HELP_REQUEST', 'HELP_RESPONSE.ID_HELP_REQUEST = HELP_REQUEST.ID');
        $this->db->where('HELP_RESPONSE.ID_USER', $id_user);
        $this->db->order_by('HELP_RESPONSE.DATE_CREATE', 'DESC');
        $result = $this->db->get();

        return $result->result('History_data');
    }

    /**
     * @param string $id_user
     *
     * @return \History_data[]
     */
    public function history_request(string $id_user)
    {
        $this->db->select('HELP_REQUEST.ID_HELP_TYPE ,HELP_REQUEST.DATE_CREATE, HELP_REQUEST.STATUS');
        $this->db->from('HELP_REQUEST');
        $this->db->where('HELP_REQUEST.ID_USER', $id_user);
        $this->db->order_by('HELP_REQUEST.DATE_CREATE', 'DESC');
        $result = $this->db->get();

        return $result->result('History_data');
    }
}
