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
 * Class Auth
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Auth extends TDB_Controller
{
    private $TAG = 'Auth';

    public function __construct() { parent::__construct(true); }

    /**
     * @throws \BadFunctionCallException
     */
    public function testing_get()
    {
        $this->response(getallheaders());
    }

}
