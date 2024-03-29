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
 * Class Nearby_data
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Help_request_data extends Base_data
{
    public static $STATUS_CANCELED = 0;
    public static $STATUS_REQUESTED = 1;
    public static $STATUS_PROCESS = 2;
    public static $STATUS_FINISH = 3;
    /**
     * @var float $LATITUDE
     */
    public $LATITUDE;
    /**
     * @var float $LONGITUDE
     */
    public $LONGITUDE;

    /**
     * @var string $ID_USER ID Requested User
     */
    public $ID_USER;
    /**
     * @var string $DEVICE_ID Device Requested User
     */
    public $DEVICE_ID;

    /**
     * @var string $ID_HELP_TYPE ID from help type
     */
    public $ID_HELP_TYPE;
    /**
     * @var string $MESSAGE Status Message from requested user
     */
    public $MESSAGE;
    /**
     * @var string $LOCATION_NAME Location Name
     */
    public $LOCATION_NAME;
    /**
     * @var string $FULL_NAME Requested Name
     */
    public $FULL_NAME;
    /**
     * @var string $USERNAME Requested username
     */
    public $USERNAME;
    /**
     * @var string $AVATAR Requested Profile Picture / Avatar
     */
    public $AVATAR;


    public function __cast()
    {
        parent::__cast();

        $this->LATITUDE = (float)$this->LATITUDE;
        $this->LONGITUDE = (float)$this->LONGITUDE;
    }

    public function __cast_date()
    {
        $this->__cast();
        $this->DATE_CREATE = date('Y-m-d', strtotime($this->DATE_CREATE));
        $this->DATE_UPDATE = date('Y-m-d', strtotime($this->DATE_UPDATE));
    }

    public function __toString()
    {
        return json_encode($this);
    }


}
