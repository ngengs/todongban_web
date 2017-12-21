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
class Help_response_data extends Base_data
{
    public static $STATUS_NOT_SELECTED = 0;
    public static $STATUS_SELECTED = 1;
    public static $RESPONSE_WAITING = 0;
    public static $RESPONSE_ACCEPT = 1;
    public static $RESPONSE_REJECT = 2;

    /**
     * @var string $ID_USER ID Response User
     */
    public $ID_USER;

    /**
     * @var string $ID_HELP_REQUEST ID from help request
     */
    public $ID_HELP_REQUEST;
    /**
     * @var int $RESPONSE Response user
     */
    public $RESPONSE;
    /**
     * @var string $RATING Rating point. 5 star system.
     */
    public $RATING;


    public function __cast()
    {
        parent::__cast();

        $this->RESPONSE = (int)$this->RESPONSE;
        if (is_null($this->RATING)) {
            $this->RATING = 0;
        }
        $this->RATING = (int)$this->RATING;
    }

    public function __toString()
    {
        return json_encode($this);
    }


}
