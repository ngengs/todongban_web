<?php
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

    /**
     * Fcm constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->payload = array();
        $this->CI->config->load('sensitive');
    }


    /**
     * @param array|string $id Firebase Device ID
     *
     * @return $this
     */
    public function set_target($id = array())
    {
        $this->to = $id;

        return $this;
    }

    public function set_title($title)
    {
        $this->payload['title'] = $title;

        return $this;
    }

    public function set_message($message)
    {
        $this->payload['message'] = $message;

        return $this;
    }

    public function set_code($code)
    {
        $can_set = false;
        switch ($code) {
            case self::CODE_HELP_REQUEST:
                $can_set = true;
                break;
            case self::CODE_REGISTER_COMPLETE:
                $can_set = true;
                break;
            case self::CODE_REGISTER_REJECTED:
                $can_set = true;
                break;
        }
        if ($can_set) {
            $this->payload['code'] = $code;
        } else throw new Exception("Code cant be used");

        return $this;
    }

    /**
     * @param mixed $key
     */
    public function set_key($key)
    {
        $this->key = $key;

        return $this;
    }


    public function send()
    {
        $this->CI->log->write_log('debug', $this->TAG . ': key: ' . $this->key);
        if (empty($this->payload) || empty($this->payload['title']) || empty($this->payload['message'])
            || empty($this->payload['code'])
            || empty($this->to)
            || empty($this->key)) {
            throw new Exception("Must build with all set first");
        } else {
            $client = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
            $data = array();
            $data['data'] = $this->payload;
            if (is_array($this->to)) {
                $data['registration_ids'] = $this->to;
            } else $data['to'] = $this->to;
            $client->request('POST',
                             $this->URL,
                             array(
                                 'json' => $data,
                                 'headers' => array(
                                     'Authorization' => 'key=' . $this->key
                                 )
                             ));
        }
    }

}
