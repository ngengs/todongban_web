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
 * Class M_type
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class M_type extends TDB_Model
{
    private $TAG = 'M_type';

    /**
     * M_type constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param int $vehicle
     * @param string $name
     * @param int $status
     *
     * @return bool
     * @throws \Exception
     */
    public function create(int $vehicle, string $name, int $status = 1)
    {
        if ($vehicle !== 1 && $vehicle !== 2) {
            throw new Exception('Vehicle type not supported');
        }
        $this->db->set('VEHICLE', $vehicle);
        $this->db->set('NAME', $name);
        $this->db->set('STATUS', $status);
        $this->db->set('ID', $this->generate_id());
        $this->db->from('HELP_TYPE');

        return $this->db->insert();
    }

    /**
     * @param int|null $vehicle
     * @param int|null $status
     *
     * @return array
     */
    public function get(?int $vehicle = null, ?int $status = null)
    {
        $this->db->select('ID, VEHICLE, NAME, STATUS');
        if (!is_null($vehicle) && ($vehicle !== 1 && $vehicle !== 2)) {
            $this->db->where('VEHICLE', $vehicle);
        }
        if (!is_null($status)) {
            $this->db->where('STATUS', $status);
        }
        $this->db->order_by('VEHICLE', 'ASC');
        $this->db->order_by('STATUS', 'DESC');
        $this->db->from('HELP_TYPE');
        $result = $this->db->get();

        return $result->result();
    }


}
