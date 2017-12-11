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
 * Class M_config
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class M_config extends TDB_Model
{
    private $TAG = 'M_config';

    /**
     * M_config constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Function to create new configuration
     *
     * @param string $id_user User ID
     * @param array $type_id Help Type ID
     *
     * @return int result of query
     */
    public function create(string $id_user, array $type_id = [])
    {
        $data = [];
        foreach ($type_id as $key => $value) {
            $data[] = [
                'ID' => 'UUID()',
                'ID_USER' => $this->db->escape($id_user),
                'ID_HELP_TYPE' => $this->db->escape($value),
                'STATUS' => 1
            ];
        }

        return $this->db->insert_batch('USER_HELP_CONF', $data, false);
    }

    /**
     * Function to update user configuration status
     *
     * @param string $id_user User ID
     * @param array $type_change Help Type ID and status array('ID_HELP_TYPE'=>string,'STATUS'=>int)
     *
     * @return mixed result of query
     */
    public function update(string $id_user, array $type_change = [])
    {
        $query = 'UPDATE `USER_HELP_CONF` SET `STATUS` = CASE';
        $id_user_array = [];
        $id_help_type_array = [];
        foreach ($type_change as $key => $value) {
            $id_user_array[] = $this->db->escape($id_user);
            $id_help_type_array[] = $this->db->escape($value['ID_HELP_TYPE']);
            $query .= ' WHEN `ID_USER`  = ' . $this->db->escape($id_user) . ' AND `ID_HELP_TYPE` = '
                      . $this->db->escape($value['ID_HELP_TYPE']) . ' THEN ' . $this->db->escape($value['STATUS']);
        }
        $query .= ' ELSE `STATUS` END';
        $query .= ' WHERE `ID_USER` IN(' . implode(',', $id_user_array) . ') '
                  . 'AND `ID_HELP_TYPE` IN (' . implode(',', $id_help_type_array) . ')';

        return $this->db->simple_query($query);
    }

    /**
     * @param string $id_user
     *
     * @return \Config_data[] Array of config data
     */
    public function get(string $id_user)
    {
        $this->db->select('USER_HELP_CONF.ID, USER_HELP_CONF.ID_USER, USER_HELP_CONF.ID_HELP_TYPE, USER_HELP_CONF.STATUS, HELP_TYPE.NAME, HELP_TYPE.VEHICLE');
        $this->db->where('ID_USER', $id_user);
        $this->db->join('HELP_TYPE', 'HELP_TYPE.ID=USER_HELP_CONF.ID_HELP_TYPE', 'LEFT');
        $this->db->order_by('HELP_TYPE.VEHICLE', 'ASC');
        $this->db->from('USER_HELP_CONF');
        $result = $this->db->get();

        return $result->result('Config_data');
    }

}
