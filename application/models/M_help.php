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
        $this->db->set('STATUS', 1);
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
     *
     * @return mixed
     */
    public function cancel_request(string $id_request, string $id_user)
    {
        $this->log->write_log('debug', $this->TAG . ': cancel_request: ');
        $this->db->where('ID', $id_request);
        $this->db->where('ID_USER', $id_user);
        $this->db->set('STATUS', 0);
        $this->set_updater($id_user);
        $this->db->from('HELP_REQUEST');
        $result = $this->db->update();

        return $result;
    }
}
