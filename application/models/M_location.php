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
}
