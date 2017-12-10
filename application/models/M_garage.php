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
 * Class M_garage
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class M_garage extends TDB_Model
{
    private $TAG = 'M_garage';

    /**
     * M_garage constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $user_id
     * @param string $name
     * @param string $open_hour
     * @param string $close_hour
     * @param string $address
     * @param float $latitude
     * @param float $longitude
     *
     * @return bool|mixed result of inserting to table
     * @throws \Exception
     */
    public function create(string $user_id, string $name, string $open_hour, string $close_hour, string $address,
        float $latitude, float $longitude)
    {
        $this->log->write_log('debug', $this->TAG . ': create: ');

        $id = $this->generate_id();
        if (!empty($id)) {
            $data = [
                'ID' => $id,
                'ID_USER' => $user_id,
                'NAME' => $name,
                'OPEN_HOUR' => date('H:i:s', strtotime($open_hour)),
                'CLOSE_HOUR' => date('H:i:s', strtotime($close_hour)),
                'ADDRESS' => $address,
                'LATITUDE' => $latitude,
                'LONGITUDE' => $longitude
            ];

            $date = date('Y-m-d H:i:s');
            $this->set_creator($id, $date);
            $this->set_updater($id, $date);
            $result = $this->db->insert('GARAGE', $data);

            return $result;
        } else {
            return false;
        }
    }
}
