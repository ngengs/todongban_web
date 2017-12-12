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
 * Class User
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class User_data extends Base_data
{
    private $TAG = 'User_data';


    /**
     * @var int $TYPE_ADMIN User Type Admin Value
     */
    public static $TYPE_ADMIN = -1;
    /**
     * @var int $TYPE_PERSONAL User Type Personal Value
     */
    public static $TYPE_PERSONAL = 1;
    /**
     * @var int $TYPE_GARAGE User Type Garage Value
     */
    public static $TYPE_GARAGE = 2;

    /**
     * @var int $STATUS_DELETE User Status Delete Value
     */
    public static $STATUS_DELETE = 0;

    /**
     * @var int $STATUS_ACTIVE User Status Active Value
     */
    public static $STATUS_ACTIVE = 1;

    /**
     * @var int $STATUS_NOT_ACTIVE User Status Not Active / Under Verify Value
     */
    public static $STATUS_NOT_ACTIVE = 2;

    /**
     * @var int $STATUS_REJECTED User Status Rejected / Failed Verify Value
     */
    public static $STATUS_REJECTED = 3;

    /**
     * @var int $STATUS_BANNED User Status Banned Value
     */
    public static $STATUS_BANNED = 4;

    /**
     * @var int $GENDER_MALE
     */
    public static $GENDER_MALE = 1;
    /**
     * @var int $GENDER_FEMALE
     */
    public static $GENDER_FEMALE = 2;


    /**
     * @var string $USERNAME
     */
    public $USERNAME;
    /**
     * @var string $EMAIL
     */
    public $EMAIL;
    /**
     * @var string $PASSWORD Encrypted (HASH) Of saved password
     */
    public $PASSWORD;
    /**
     * @var string $FULL_NAME
     */
    public $FULL_NAME;
    /**
     * @var string $PHONE
     */
    public $PHONE;
    /**
     * @var int $GENDER Use User_data::$GENDER_MALE or User_data::$GENDER_FEMALE
     */
    public $GENDER;
    /**
     * @var string $AVATAR
     */
    public $AVATAR;
    /**
     * @var string $IDENTITY_NUMBER
     */
    public $IDENTITY_NUMBER;
    /**
     * @var string $IDENTITY_PICTURE
     */
    public $IDENTITY_PICTURE;
    /**
     * @var string $ADDRESS
     */
    public $ADDRESS;
    /**
     * @var string
     */
    public $DEVICE_ID;
    /**
     * @var int $TYPE Use User_data::$TYPE_ADMIN or User_data::$TYPE_PERSONAL or User_data::$TYPE_GARAGE
     */
    public $TYPE;

    public function __cast()
    {
        parent::__cast();
        $this->GENDER = (int) $this->GENDER;
        $this->TYPE = (int) $this->TYPE;
    }

    public function __toString()
    {
        return json_encode($this);
    }


}
