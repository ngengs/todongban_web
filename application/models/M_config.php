<?php
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
    public function create($id_user, $type_id = array())
    {
        $data = array();
        foreach ($type_id as $key => $value) {
            $data[] = array(
                'ID' => 'UUID()',
                'ID_USER' => $this->db->escape($id_user),
                'ID_HELP_TYPE' => $this->db->escape($value),
                'STATUS' => 1
            );
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
    public function update($id_user, $type_change = array())
    {
        $query = 'UPDATE `USER_HELP_CONF` SET `STATUS` = CASE';
        $id_user_array = array();
        $id_help_type_array = array();
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

}
