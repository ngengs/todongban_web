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
 * Class M_user
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class M_user extends TDB_Model
{
    private $TAG = 'M_user';

    /**
     * M_user constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $id
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $full_name
     * @param string $phone
     * @param int $gender
     * @param string $birth_date
     * @param string $avatar
     * @param string $identity_number
     * @param string $identity_picture
     * @param string $address
     * @param string $device_id
     * @param int $status
     * @param int $type
     *
     * @return bool
     * @throws \Exception
     *
     */
    public function create(string $id, string $username, string $email, string $password, string $full_name,
        string $phone, int $gender, string $birth_date, string $avatar, string $identity_number,
        string $identity_picture, string $address,
        string $device_id, int $status = 2, int $type = 1)
    {
        $date = date('Y-m-d H:i:s');
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        if (!$password_hash) {
            throw new Exception('Failed hashing password');
        }
        $data = [
            'ID' => $id,
            'USERNAME' => $username,
            'EMAIL' => $email,
            'PASSWORD' => $password_hash,
            'FULL_NAME' => $full_name,
            'PHONE' => $phone,
            'GENDER' => $gender,
            'BIRTH_DATE' => date('Y-m-d', strtotime($birth_date)),
            'AVATAR' => $avatar,
            'IDENTITY_NUMBER' => $identity_number,
            'IDENTITY_PICTURE' => $identity_picture,
            'ADDRESS' => $address,
            'DEVICE_ID' => $device_id,
            'TYPE' => $type,
            'STATUS' => $status
        ];
        $this->set_creator($id, $date);
        $this->set_updater($id, $date);
        $result = $this->db->insert('USER', $data);

        return $result;
    }

    /**
     * @param string $username
     *
     * @return bool
     */
    public function check_username(string $username): bool
    {
        $this->db->select('ID');
        $this->db->from('USER');
        $this->db->where('USERNAME', $username);
        $result = $this->db->get();

        $cas_use = true;
        if (!empty($result->result())) {
            $cas_use = false;
        }

        return $cas_use;
    }

    /**
     * @param null|string $username
     * @param null|string $device_id
     * @param null $status
     * @param null|string $id
     * @param null $type
     *
     * @return \User_data[]
     */
    public function get(?string $username = null, ?string $device_id = null, $status = null, ?string $id = null,
        $type = null)
    {
        $this->db->select('ID, USERNAME, EMAIL, PASSWORD, FULL_NAME, PHONE, GENDER, AVATAR, IDENTITY_NUMBER, IDENTITY_PICTURE, ADDRESS, DEVICE_ID, ID_CREATE, DATE_CREATE, ID_UPDATE, DATE_UPDATE, STATUS, TYPE');
        if (!empty($username)) {
            $this->db->where('USERNAME', $username);
        }
        if (!empty($device_id)) {
            $this->db->where('DEVICE_ID', $device_id);
        }
        if (!empty($id)) {
            $this->db->where('ID', $id);
        }
        if (!is_null($status)) {
            if (is_array($status)) {
                $this->db->where_in('STATUS', $status);
            } else {
                $this->db->where('STATUS', $status);
            }
        }
        if (!is_null($type)) {
            if (is_array($type)) {
                $this->db->where_in('TYPE', $type);
            } else {
                $this->db->where('TYPE', $type);
            }
        }
        $this->db->from('USER');
        $result = $this->db->get();

        return $result->result('User_data');
    }

    public function count_not_active(): int
    {
        $this->db->from('USER');
        $this->db->where_in('TYPE', [1, 2]);
        $this->db->where('STATUS', 2);

        return $this->db->count_all_results();
    }

    public function count_rejected(): int
    {
        $this->db->from('USER');
        $this->db->where('TYPE', 1);
        $this->db->where('STATUS', 3);

        return $this->db->count_all_results();
    }

    public function update_status(string $id, int $status, ?string $id_updater = null)
    {
        $this->db->set('STATUS', $status);
        $this->db->where('ID', $id);
        if (empty($id_updater)) {
            $id_updater = $id;
        }
        $this->set_updater($id_updater);
        $result = $this->db->update('USER');

        return $result;
    }

    public function update_device_id(string $id, ?string $device_id = null)
    {
        $this->db->set('DEVICE_ID', $device_id);
        $this->set_updater($id);
        $this->db->where('ID', $id);
        $result = $this->db->update('USER');

        return $result;
    }

    /**
     * @param string $id
     * @param string $new_password
     *
     * @return bool
     * @throws \Exception
     */
    public function update_password(string $id, string $new_password)
    {
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        if (!$password_hash) {
            throw new Exception('Failed hashing password');
        }
        $this->db->set('PASSWORD', $password_hash);
        $this->set_updater($id);
        $this->db->where('ID', $id);
        $result = $this->db->update('USER');

        return $result;
    }

    public function delete_pure(string $id)
    {
        $this->db->where('ID', $id);
        $this->db->delete('USER');
    }

}
