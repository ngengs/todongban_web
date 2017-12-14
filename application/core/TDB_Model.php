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

/**
 * Class TDB_Model
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class TDB_Model extends CI_Model
{

    /**
     * TDB_Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Function to generate ID.
     * This use mysql UUID.
     *
     * @param int $many
     *
     * @return string|array|null generated ID
     * @throws \Exception if cant create ID
     */
    public function generate_id(int $many = 1)
    {
        $select = 'UUID() as ID';
        if ($many > 1) {
            $selects = [];
            for ($i = 0; $i < $many; $i++) {
                $selects[] = 'UUID() AS ID_' . $i;
            }
            $select = implode(',', $selects);
        }
        $this->db->select($select, false);
        $result = $this->db->get();
        $result = $result->result();
        if (empty($result)) {
            throw new Exception('Something wrong with database');
        }

        if ($many > 1) {
            $ids = [];
            for ($i = 0; $i < $many; $i++) {
                $ids[$i] = $result[0]->{'ID_' . $i};
            }

            return $ids;
        } else {
            return $result[0]->ID;
        }
    }

    /**
     * Function to add set data of creator.
     * In every table has Creator data so we can call this function to generate that query.
     *
     * @param string $id_creator id of user creator
     * @param string|null $date Date of create
     */
    protected function set_creator(string $id_creator, ?string $date = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }
        $this->db->set('ID_CREATE', $id_creator);
        $this->db->set('DATE_CREATE', $date);
    }

    /**
     * Function to add set data of updater.
     * In every table has Updater data so we can call this function to generate that query.
     *
     * @param string $id_updater id of user update
     * @param string|null $date Date of update
     */
    protected function set_updater(string $id_updater, ?string $date = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }
        $this->db->set('ID_UPDATE', $id_updater);
        $this->db->set('DATE_UPDATE', $date);
    }
}
