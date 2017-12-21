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
class Help_response_user_data extends User_data
{

    /**
     * @var float $LATITUDE
     */
    public $LATITUDE;

    /**
     * @var float $LONGITUDE
     */
    public $LONGITUDE;
    /**
     * @var string $RESPONSE
     */
    public $GARAGE_NAME;
    /**
     * @var float $GARAGE_LATITUDE
     */
    public $GARAGE_LATITUDE;
    /**
     * @var float $GARAGE_LONGITUDE
     */
    public $GARAGE_LONGITUDE;


    public function __cast()
    {
        parent::__cast();

        if (is_null($this->LATITUDE) && !is_null($this->GARAGE_LATITUDE)) {
            $this->LATITUDE = $this->GARAGE_LATITUDE;
        }
        if (is_null($this->LONGITUDE) && !is_null($this->GARAGE_LONGITUDE)) {
            $this->LONGITUDE = $this->GARAGE_LONGITUDE;
        }
        $this->LATITUDE = (float)$this->LATITUDE;
        $this->LONGITUDE = (float)$this->LONGITUDE;
        $this->GARAGE_LATITUDE = (float)$this->GARAGE_LATITUDE;
        $this->GARAGE_LONGITUDE = (float)$this->GARAGE_LONGITUDE;
    }

    public function __toString()
    {
        return json_encode($this);
    }


}
