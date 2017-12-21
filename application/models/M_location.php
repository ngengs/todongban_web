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
class M_location extends TDB_Model
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
     * @param float $longitude user last position Longtitude
     *
     * @return bool result of query
     * @throws \Exception
     */
    public function insert_location(string $id_user, float $latitude, float $longitude)
    {
        $date = date('Y-m-d H:i:s');
//		Generate query
//		Generate update query
        $this->db->where('ID_USER', $id_user);
        $this->db->where('STATUS', 1);
        $this->db->set('STATUS', 0);
        $this->db->from('USER_LOCATION');
        $query_update = $this->db->get_compiled_update();
//		Generate insert query
        $this->db->set('ID', $this->generate_id());
        $this->db->set('ID_USER', $id_user);
        $this->db->set('LATITUDE', $latitude);
        $this->db->set('LONGITUDE', $longitude);
        $this->db->set('DATE', $date);
        $this->db->set('STATUS', 1);
        $this->db->from('USER_LOCATION');
        $query_insert = $this->db->get_compiled_insert();

//		Run transaction
        $this->db->trans_start();
        $this->db->query($query_update);
        $this->db->query($query_insert);
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * @param string $id_user
     * @param string $help_type
     * @param float $latitude
     * @param float $longitude
     * @param int $max_rage_km
     * @param int $limit
     *
     * @return \Nearby_data[]
     */
    public function get_nearby_personal(string $id_user, string $help_type, float $latitude, float $longitude,
        int $max_rage_km = 10, int $limit = 5)
    {
        $this->db->select('USER_LOCATION.LATITUDE,
	USER_LOCATION.LONGITUDE,
	USER_LOCATION.`STATUS`,
	USER_LOCATION.ID_USER as ID,
	`USER`.ID as ID_USER,
	`USER`.USERNAME,
	`USER`.EMAIL,
	`USER`.FULL_NAME as NAME,
	`USER`.PHONE,
	`USER`.GENDER,
	`USER`.AVATAR,
	`USER`.ADDRESS,
	`USER`.DEVICE_ID,
	`USER`.ID_CREATE,
	`USER`.DATE_CREATE,
	`USER`.ID_UPDATE,
	`USER`.DATE_UPDATE,
	`USER`.TYPE,
	USER_HELP_CONF.ID_HELP_TYPE,
	USER_HELP_CONF.`STATUS` as STATUS_USER_CONF,'
                          . $this->distance_query($latitude,
                                                  $longitude,
                                                  'USER_LOCATION.LATITUDE',
                                                  'USER_LOCATION.LONGITUDE'),
                          false);
        $this->db->from('USER_LOCATION');

        $this->db->join('USER',
                        'USER_LOCATION.ID_USER = `USER`.ID AND `USER`.ID <> ' . $this->db->escape($id_user)
                        . ' AND `USER`.STATUS=' . User_data::$STATUS_ACTIVE
                        . ' AND `USER`.TYPE=' . User_data::$TYPE_PERSONAL
                        . ' AND `USER`.DEVICE_ID IS NOT NULL',
                        'INNER',
                        false);
        $this->db->join('USER_HELP_CONF',
                        'USER_HELP_CONF.ID_USER = `USER`.ID AND USER_HELP_CONF.ID_HELP_TYPE = '
                        . $this->db->escape($help_type) . ' AND USER_HELP_CONF.`STATUS` = 1',
                        'INNER',
                        false);

        $this->db->where('USER_LOCATION.STATUS', 1);

        // Exclude the user in searching help process
        $this->db->where('(SELECT COUNT(HELP_REQUEST.ID) AS IN_PROGRESS FROM HELP_REQUEST WHERE HELP_REQUEST.ID_USER = `USER`.ID AND (HELP_REQUEST.`STATUS` = '
                         . Help_request_data::$STATUS_REQUESTED . ' OR HELP_REQUEST.`STATUS` = '
                         . Help_request_data::$STATUS_PROCESS . ')) =',
                         0);

        $this->db->having('DISTANCE <=', $max_rage_km);
        $this->db->order_by('DISTANCE', 'ASC');
        $this->db->limit($limit);
//        $this->log->write_log('debug', $this->TAG . ': get_nearby_personal: QUERY: ' .  $this->db->get_compiled_select());
//        echo $this->db->get_compiled_select();die;
        $result = $this->db->get();

        return $result->result('Nearby_data');
    }

    /**
     * @param string $id_user
     * @param string $help_type
     * @param float $latitude
     * @param float $longitude
     * @param string $time_request
     * @param int $max_rage_km
     * @param int $limit
     *
     * @return \Nearby_data[]
     */
    public function get_nearby_garage(string $id_user, string $help_type, float $latitude, float $longitude,
        string $time_request, int $max_rage_km = 5, int $limit = 5)
    {
        $this->db->select('GARAGE.ID,
	GARAGE.`NAME`,
	GARAGE.OPEN_HOUR,
	GARAGE.CLOSE_HOUR,
	GARAGE.ADDRESS,
	GARAGE.LATITUDE,
	GARAGE.LONGITUDE,
	GARAGE.FORCE_CLOSE,
	GARAGE.ID_CREATE,
	GARAGE.DATE_CREATE,
	GARAGE.ID_UPDATE,
	GARAGE.DATE_UPDATE,
	`USER`.ID AS ID_USER,
	`USER`.USERNAME,
	`USER`.EMAIL,
	`USER`.PHONE,
	`USER`.GENDER,
	`USER`.AVATAR,
	`USER`.DEVICE_ID,
	`USER`.TYPE,
	`USER`.STATUS,
	USER_HELP_CONF.ID_HELP_TYPE,
	USER_HELP_CONF.`STATUS` as STATUS_USER_CONF,'
                          . $this->distance_query($latitude,
                                                  $longitude,
                                                  'GARAGE.LATITUDE',
                                                  'GARAGE.LONGITUDE'),
                          false);
        $this->db->from('GARAGE');

        $this->db->join('`USER`',
                        '`GARAGE`.`ID_USER` = `USER`.ID AND `USER`.ID <> ' . $this->db->escape($id_user)
                        . ' AND `USER`.`STATUS`=' . User_data::$STATUS_ACTIVE
                        . ' AND `USER`.`TYPE`=' . User_data::$TYPE_GARAGE
                        . ' AND `USER`.`DEVICE_ID` IS NOT NULL',
                        'INNER',
                        false);
        $this->db->join('`USER_HELP_CONF`',
                        '`USER_HELP_CONF`.`ID_USER` = `USER`.ID AND USER_HELP_CONF.ID_HELP_TYPE = '
                        . $this->db->escape($help_type) . ' AND USER_HELP_CONF.`STATUS` = 1',
                        'INNER',
                        false);

        $time_request = date('H:i', strtotime($time_request));
//        $this->db->where('(GARAGE.OPEN_HOUR <= '. $this->db->escape($time_request) .' OR GARAGE.CLOSE_HOUR >= '.
//                         $this->db->escape($time_request) .')');
        $this->db->where('GARAGE.OPEN_HOUR <=', $time_request);
        $this->db->where('GARAGE.CLOSE_HOUR >=', $time_request);
        $this->db->where('GARAGE.FORCE_CLOSE', 0);
        $this->db->having('DISTANCE <=', $max_rage_km);
        $this->db->order_by('DISTANCE', 'ASC');
        $this->db->limit($limit);
//        echo $this->db->get_compiled_select();die;
        $result = $this->db->get();

        return $result->result('Nearby_data');
    }

    public function distance_from_current_location_personal(string $id_user, float $latitude, float $longitude)
    {
        $this->db->select($this->distance_query($latitude, $longitude, 'LATITUDE', 'LONGITUDE'));
        $this->db->from('USER_LOCATION');
        $this->db->where('ID_USER', $id_user);
        $this->db->where('STATUS', 1);
        $result = $this->db->get();

        $result = $result->result();
        if (!empty($result)) {
            $result = $result[0];

            return $result->DISTANCE;
        }

        return 0;
    }

    public function distance_from_current_location_garage(string $id_user, float $latitude, float $longitude)
    {
        $this->db->select($this->distance_query($latitude, $longitude, 'LATITUDE', 'LONGITUDE'));
        $this->db->from('GARAGE');
        $this->db->where('ID_USER', $id_user);
        $result = $this->db->get();

        $result = $result->result();
        if (!empty($result)) {
            $result = $result[0];

            return $result->DISTANCE;
        }

        return 0;
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param $date_start
     * @param $date_end
     * @param int $max_range_km
     *
     * @return \Help_request_data[]
     */
    public function get_nearby_request(float $latitude, float $longitude, $date_start, $date_end,
        int $max_range_km = 10)
    {
//        echo date('Y-m-d', strtotime($date_start));die;
        $this->db->select('ID,	ID_USER, ID_HELP_TYPE, MESSAGE, LATITUDE, LONGITUDE, LOCATION_NAME, ID_CREATE, DATE_CREATE, ID_UPDATE, DATE_UPDATE, STATUS, '
                          . $this->distance_query($latitude, $longitude, 'LATITUDE', 'LONGITUDE'));
        $this->db->from('HELP_REQUEST');
        $this->db->where('(DATE_CREATE BETWEEN '
                         . $this->db->escape(date('Y-m-d', strtotime($date_start))) . ' AND '
                         . $this->db->escape(date('Y-m-d', strtotime('+1 day', strtotime($date_end)))) . ')',
                         null,
                         false);
        $this->db->having('DISTANCE <=', $max_range_km);
        $this->db->order_by('DATE_CREATE ', 'ASC');
//        echo $this->db->get_compiled_select();
        $result = $this->db->get();

        return $result->result('Help_request_data');
    }

    /**
     * Query for get DISTANCE from given latitude and longitude
     * Get from: https://developers.google.com/maps/solutions/store-locator/clothing-store-locator#domxml
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $column_latitude
     * @param string $column_longitude
     *
     * @return string
     */
    private function distance_query(float $latitude, float $longitude, string $column_latitude,
        string $column_longitude): string
    {
        return '(3959 * acos(cos(radians(' . $latitude . ')) * cos( radians(' . $this->db->escape_str($column_latitude)
               . ')) * cos(radians( ' . $this->db->escape_str($column_longitude) . ') - radians(' . $longitude
               . ') ) + sin(radians(' . $latitude . ')) * sin(radians(' . $this->db->escape_str($column_latitude)
               . ')))) AS DISTANCE';
    }

    /**
     * Query for get DISTANCE from given latitude and longitude
     * Get from: https://developers.google.com/maps/solutions/store-locator/clothing-store-locator#domxml
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $latitude_initial
     * @param float $longitude_initial
     *
     * @return string
     */
    private function distance_query_coordinate(float $latitude, float $longitude, float $latitude_initial,
        float $longitude_initial): string
    {
        return '(3959 * acos(cos(radians(' . $latitude . ')) * cos( radians(' . $latitude_initial
               . ')) * cos(radians( ' . $longitude_initial . ') - radians(' . $longitude
               . ') ) + sin(radians(' . $latitude . ')) * sin(radians(' . $latitude_initial
               . ')))) AS DISTANCE';
    }
}
