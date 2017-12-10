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
 * Class Config
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Config extends TDB_Controller
{
    private $TAG = 'Config';

    /**
     * Config constructor.
     */
    public function __construct()
    {
        parent::__construct(true);
    }

    public function index_get()
    {

    }

    /**
     * API for update user config
     *
     * access: private. need authorization header
     * method: post
     *
     * Input needed:
     * array id_help_type ID of help type config
     * array status Status of the id (1 active, 2 non active)
     * @throws \BadFunctionCallException
     */
    public function update_post()
    {
        if (!$this->check_access()) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Not authorized');
        }
        $id_help_type = $this->input->post('id_help_type[]');
        $status = $this->input->post('status[]');
        if (count($id_help_type) != count($status)) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, 'Different value length');
        }
        $config = [];
        for ($i = 0; $i < count($id_help_type); $i++) {
            $config[$i] = [
                'ID_HELP_TYPE' => $id_help_type[$i],
                'STATUS' => (int)$status[$i]
            ];
        }
        $user = $this->get_user();
        $this->load->model('m_config');
        $this->m_config->update($user->ID, $config);
        $this->response(null);
    }

    /**
     * @throws \Exception
     */
    public function create_post()
    {
        $vehicle = (int)$this->input->post('vehicle');
        $name = $this->input->post('name');
        $this->load->model('m_type');
        $this->m_type->create($vehicle, $name);
    }

}
