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
class Nearby_data extends User_data
{
    /**
     * @var string $ID_USER ID User
     */
    public $ID_USER;

    /**
     * @var float $LATITUDE
     */
    public $LATITUDE;
    /**
     * @var float $LONGITUDE
     */
    public $LONGITUDE;

    /**
     * @var string $ID_HELP_TYPE ID from help type
     */
    public $ID_HELP_TYPE;
    /**
     * @var int $STATUS_USER_CONF Status User Configuration HELP
     */
    public $STATUS_USER_CONF;
    /**
     * @var float $DISTANCE Distance in KM
     */
    public $DISTANCE;

    // GARAGE DATA
    /**
     * @var string $NAME Name of User or Garage
     */
    public $NAME;
    public $OPEN_HOUR;
    public $CLOSE_HOUR;
    /**
     * @var int $FORCE_CLOSE
     */
    public $FORCE_CLOSE;

    /**
     * @return bool
     */
    public function is_force_close(): bool
    {
        return ($this->FORCE_CLOSE == 1);
    }

    public function __cast()
    {
        parent::__cast();

        $this->DISTANCE = (float)$this->DISTANCE;
        $this->LATITUDE = (float)$this->LATITUDE;
        $this->LONGITUDE = (float)$this->LONGITUDE;
        $this->FORCE_CLOSE = (int)$this->FORCE_CLOSE;
        $this->STATUS_USER_CONF = (int)$this->STATUS_USER_CONF;
        // Give full name from name
        $this->FULL_NAME = $this->NAME;
    }

    public function __toString()
    {
        return json_encode($this);
    }


}
