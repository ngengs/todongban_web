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
 * Class Base_data
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Base_data
{
    /**
     * @var string $ID
     */
    public $ID;
    /**
     * @var null|string $ID_CREATE Not all object have this, please make sure to check before use this
     */
    public $ID_CREATE;
    /**
     * @var null|string $DATE_CREATE Not all object have this, please make sure to check before use this
     */
    public $DATE_CREATE;
    /**
     * @var null|string $ID_UPDATE Not all object have this, please make sure to check before use this
     */
    public $ID_UPDATE;
    /**
     * @var null|string $DATE_UPDATE Not all object have this, please make sure to check before use this
     */
    public $DATE_UPDATE;
    /**
     * @var int $STATUS
     */
    public $STATUS;

    public function __cast()
    {
        if (!is_null($this->STATUS)) {
            $this->STATUS = (int)$this->STATUS;
        }
    }

    public function __toString()
    {
        return json_encode($this);
    }


}
