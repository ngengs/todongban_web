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
 * Class Fcm
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Fcm
{
    private $TAG = 'Fcm';

    private $CI;
    private $URL = 'https://fcm.googleapis.com/fcm/send';

    private $key;
    private $payload;
    private $to;

    const CODE_REGISTER_COMPLETE = 100;
    const CODE_REGISTER_REJECTED = 101;
    const CODE_HELP_REQUEST = 200;
    const CODE_HELP_RESPONSE = 201;
    const CODE_HELP_SEARCH_GARAGE = 202;
    const CODE_HELP_SEARCH_PERSONAL = 203;
    const CODE_HELP_RESPONSE_ACCEPTED = 204;
    const CODE_HELP_RESPONSE_REJECTED = 205;
    const CODE_HELP_REQUEST_FINISH = 210;

    /**
     * Fcm constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('sensitive', true);
        $this->reset();
    }


    /**
     * @param array|string $id Firebase Device ID
     *
     * @return $this
     */
    public function set_targets(array $id): self
    {
        $this->to = $id;

        return $this;
    }

    public function set_target(string $id): self
    {
        $this->to = $id;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return \Fcm
     */
    public function set_title(string $title): self
    {
        $this->payload['title'] = $title;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return \Fcm
     */
    public function set_message(string $message): self
    {
        $this->payload['message'] = $message;

        return $this;
    }

    /**
     * @param int $code
     *
     * @return \Fcm
     * @throws \Exception
     *
     */
    public function set_code(int $code): self
    {
        $can_set = false;
        switch ($code) {
            case self::CODE_HELP_REQUEST:
            case self::CODE_HELP_RESPONSE:
            case self::CODE_REGISTER_COMPLETE:
            case self::CODE_REGISTER_REJECTED:
            case self::CODE_HELP_SEARCH_GARAGE:
            case self::CODE_HELP_SEARCH_PERSONAL:
            case self::CODE_HELP_RESPONSE_ACCEPTED:
            case self::CODE_HELP_RESPONSE_REJECTED:
            case self::CODE_HELP_REQUEST_FINISH:
                $can_set = true;
                break;
        }
        if ($can_set) {
            $this->payload['code'] = $code;
        } else {
            throw new Exception("Code cant be used");
        }

        return $this;
    }

    /**
     * @param array $payloads
     *
     * @return \Fcm
     */
    public function set_payloads(array $payloads = []): self
    {
        foreach ($payloads as $key => $value) {
            $this->payload[$key] = $value;
        }

        return $this;
    }

    public function set_payload(string $key, $value): self
    {
        $this->payload[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Fcm
     */
    public function set_key(string $key): self
    {
        $this->key = $key;

        return $this;
    }


    /**
     * @throws \Exception
     */
    public function send()
    {
        $this->CI->log->write_log('debug', $this->TAG . ': ' . $this);
        if (empty($this->payload) || empty($this->payload['code']) || empty($this->to) || empty($this->key)) {
            throw new Exception('Must build with all set first');
        } else {
            $client = new \GuzzleHttp\Client(['curl' => [CURLOPT_SSL_VERIFYPEER => false]]);
            $data = [];
            $data['data'] = $this->payload;
            if (is_array($this->to)) {
                $data['registration_ids'] = $this->to;
            } else {
                $data['to'] = $this->to;
            }
            $client->request('POST',
                             $this->URL,
                             [
                                 'json' => $data,
                                 'headers' => [
                                     'Authorization' => 'key=' . $this->key
                                 ]
                             ]);
        }
    }

    public function reset()
    {
        $this->payload = [];
        $this->to = null;
        $this->key = $this->CI->config->item('fcm_key', 'sensitive');
    }

    public function __toString()
    {
        $payload = json_encode($this->payload);

        return 'to: ' . $this->to . ', key: ' . $this->key . ', payload: ' . $payload;
    }


}
