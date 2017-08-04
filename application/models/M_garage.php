<?php
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
     * @param \DateTime $open_hour
     * @param \DateTime $close_hour
     * @param string $address
     * @param float $latitude
     * @param float $longtitude
     *
     * @return bool|mixed result of inserting to table
     */
    public function create($user_id, $name, $open_hour, $close_hour, $address, $latitude, $longitude)
    {
        $id = $this->generate_id();
        if (!empty($id)) {
            $data = array(
                'ID' => $id,
                'ID_USER' => $user_id,
                'NAME' =>  $name,
                'OPEN_HOUR' => date('H:i:s', strtotime($open_hour)),
                'CLOSE_HOUR' => date('H:i:s', strtotime($close_hour)),
                'ADDRESS' => $address,
                'LATITUDE' => $latitude,
                'LONGITUDE' => $longitude
            );

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
