<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

/**
 * Class MY_Controller
 *
 * @property  \M_user m_user User Model
 * @property  \M_config m_config User Model
 * @property  \M_location m_location Location Model
 * @property  \M_type m_type Help Type Model
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class TDB_Controller extends CI_Controller
{
    private $TAG = 'TDB_Controller';
    private $api_controller;
    private $user;

    /**
     * MY_Controller constructor.
     *
     * @param bool $api Check is child need parent as API Controller or not
     */
    public function __construct($api = false)
    {
        parent::__construct();
        $this->api_controller = $api;
        $this->user = null;
        if (!$this->api_controller) $this->load->library('session');
    }

    /**
     * Function to get user active data.
     *
     * @return null|object User data
     */
    protected function get_user()
    {
        $this->log->write_log('debug', $this->TAG . ': get_user: ');

        return $this->user;
    }

    /**
     * Function to check if user can access.
     * This method must call in every router who need only logged in access.
     *
     * @return bool Can access or not
     */
    protected function check_access()
    {
        $this->log->write_log('debug', $this->TAG . ': check_access: ');
        $can_access = false;
        if (empty($this->user)) {
            $user = null;
            if ($this->api_controller) {
                $token = $this->explode_authorization();
                $data = $this->extract_data_from_token($token);
                if (is_array($data) && count($data) == 2) {
                    $user = $this->check_token_data($data[0], $data[1]);
                    $this->log->write_log('debug', $this->TAG . ': test: ' . json_encode($user));
                }
            } else {
                $username = $this->session->userdata('session_user');
                if (!empty($username)) {
                    $this->load->model('m_user');
                    $user = $this->m_user->get($username);
                    if (!empty($user)) $user = $user[0];
                }
            }

            if (!empty($user)) {
                unset($user->PASSWORD);
                $this->user = $user;
            }
        }

        if (!empty($this->user)) {
            if ($this->api_controller) {
                if ($this->user->STATUS == 1 && $this->user->TYPE == 1) $can_access = true;
            } else {
                if ($this->user->STATUS == 1 && $this->user->TYPE == 2) $can_access = true;
            }
        }

        return $can_access;
    }

    /**
     * Function to extract data from JWT token
     *
     * @param $token JWT token to extract
     *
     * @return null|object Data of JWT
     * @throws \BadFunctionCallException if call from non API controller
     */
    private function extract_data_from_token($token)
    {
        $this->log->write_log('debug', $this->TAG . ': extract_user_from_token: ');
        if (!$this->api_controller) throw new BadFunctionCallException('Only for API type controller');
        $data = null;
        $encoded = $this->read_token($token);
        if (!empty($encoded)) {
            $data = $encoded->sub;
        }

        return $data;
    }

    /**
     * Function to check if token have active user (useername & device id is match)
     *
     * @param string $username Username to check
     * @param string $device_id Device ID to check
     *
     * @return array User data
     * @throws \BadFunctionCallException if call from non API controller
     */
    private function check_token_data($username, $device_id)
    {
        $this->log->write_log('debug', $this->TAG . ': check_token_data: ');
        if (!$this->api_controller) throw new BadFunctionCallException('Only for API type controller');
        $this->load->model('m_user');
        $user = $this->m_user->get($username, $device_id);
        if (!empty($user)) $user = $user[0];

        return $user;
    }

    /**
     * Function to remap the router
     *
     * @param string $object_called function name
     * @param array $params parameter of function
     *
     * @return mixed function if exist or die if not exist
     */
    public function _remap($object_called, $params = array())
    {
        if ($this->api_controller) {
            $object_called = $object_called . '_' . $this->input->method(false);
        }
        if (method_exists($this, $object_called)) {
            return call_user_func_array(array($this, $object_called), $params);
        }
        if ($this->api_controller) {
            $this->response_404();
        } else {
            show_404();
        }

        return null;
    }

    /**
     * Function to send JSON response to client.
     *
     * @param mixed $data Data to send
     * @param string $status Status of response, OK for success and ERROR for error
     * @param int|null $code Status code of response, null if success and int if error
     * @param null|string $message Status message of response, null if success and string if error
     *
     * @throws \BadFunctionCallException if call from non API controller
     */
    protected function response($data, $status = VALUE_STATUS_OK, $code = STATUS_CODE_SUCCESS,
        $message = VALUE_STATUS_MESSAGE_DEFAULT)
    {
        $this->log->write_log('debug', $this->TAG . ': response: ');
        if (!$this->api_controller) throw new BadFunctionCallException('Only for API type controller');
        $response = $this->generate_response($status, $code, $message, $data);
        $this->send(200, $response);
    }

    /**
     * Function to explode API request header.
     *
     * @return string JWT from Bearer token
     * @throws \BadFunctionCallException if call from non API controller
     */
    private function explode_authorization()
    {
        $this->log->write_log('debug', $this->TAG . ': explode_authorization: ');
        if (!$this->api_controller) throw new BadFunctionCallException('Only for API type controller');

        $header_value = $this->input->get_request_header('authorization');
        if (empty($header_value)) {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, VALUE_STATUS_MESSAGE_FORBIDDEN);
        }
        $bearer_data = explode(' ', $header_value);
        if (strtolower($bearer_data[0]) !== 'bearer') {
            $this->response_error(STATUS_CODE_NOT_AUTHORIZED, VALUE_STATUS_MESSAGE_FORBIDDEN);
        }

        return (string)$bearer_data[1];
    }

    /**
     * Function to send 404
     */
    protected function response_404()
    {
        $this->log->write_log('debug', $this->TAG . ': response_404: ');
        if ($this->api_controller) {
            $this->response_error(STATUS_CODE_NOT_FOUND, 'Page not found / wrong method', 404);
        } else show_404();
    }

    /**
     * Function to send error response in API or 404 in web
     *
     * @param int $status_code Status code
     * @param string $status_message Status message
     * @param int $header_code Status code in header
     */
    protected function response_error($status_code, $status_message, $header_code = 200)
    {
        $this->log->write_log('debug', $this->TAG . ': response_error: ');
        if ($this->api_controller) {
            $response =
                $this->generate_response(VALUE_STATUS_ERROR,
                                         $status_code,
                                         $status_message,
                                         VALUE_DATA_ERROR);
            $this->send($header_code, $response);
        } else show_404();
    }

    /**
     * Function to create JWT token
     *
     * @param null|array $data Data to send with JWT. this app nedd array(username, device_id)
     *
     * @return null|string JWT token
     *
     * @throws \BadFunctionCallException if call from non API controller
     * @throws \Exception If private key not exist
     * @throws \LogicException if failed read private key
     */
    protected function create_token($data = null)
    {
        $this->log->write_log('debug', $this->TAG . ': create_token: ');
        if (!$this->api_controller) throw new BadFunctionCallException('Only for API type controller');
        $key_file = @file_get_contents('private.pem');
        if ($key_file === false) throw new Exception('Key File Not Found');
        $key = openssl_pkey_get_private($key_file, 'todongban');

        $date_now = date('c');
        $date_exp = date('c', strtotime('+1 year', strtotime($date_now)));
        $token = array(
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => strtotime($date_now),
            'exp' => strtotime($date_exp),
            'sub' => $data
        );

        $encoded = null;

        if ($key && !empty($token)) {
            try {
                $encoded = JWT::encode($token, $key, 'RS256');
            } catch (DomainException $exception) {
                $this->response_error(STATUS_CODE_SERVER_ERROR, $exception);
            }
        } else {
            throw new LogicException('OpenSSL key or token data is empty');
        }

        return $encoded;
    }

    /**
     * Function to read JWT token
     *
     * @param string $jwt JWT token
     *
     * @return null|object JWT object
     * @throws \BadFunctionCallException if call from non API controller
     * @throws \Exception if public key not exist
     * @throws \LogicException if failed read public key
     */
    protected function read_token($jwt)
    {
        $this->log->write_log('debug', $this->TAG . ': read_token: ');
        if (!$this->api_controller) throw new BadFunctionCallException('Only for API type controller');
        $key_file = @file_get_contents('public.pem');
        if ($key_file === false) throw new Exception('Key File Not Found');
        $key = openssl_pkey_get_public($key_file);
        $decoded = null;

        if ($key) {
            try {
                JWT::$leeway = 60;
                $decoded = JWT::decode($jwt, $key, array('RS256'));
            } catch (ExpiredException $exception) {
                $this->response_error(STATUS_CODE_KEY_EXPIRED, $exception);
            } catch (InvalidArgumentException $exception) {
                $this->response_error(STATUS_CODE_SERVER_ERROR, $exception);
            } catch (UnexpectedValueException $exception) {
                $this->response_error(STATUS_CODE_SERVER_ERROR, $exception);
            }
        } else {
            throw new LogicException('OpenSSL key is empty');
        }

        return $decoded;
    }


    /**
     * Function to generate JSON response
     *
     * @param string $status Status of response, OK for success and ERROR for error
     * @param int|null $code Status code of response, null if success and int if error
     * @param string|null $message Status message of response, null if success and string ig error
     * @param mixed $data Data to send
     *
     * @return array Generated response from parameter
     * @throws \BadFunctionCallException
     */
    private function generate_response($status, $code, $message, $data)
    {
        $this->log->write_log('debug', $this->TAG . ': generate_response: ');
        if (!$this->api_controller) throw new BadFunctionCallException('Only for API type controller');
        if ($message instanceof Exception) $message = $message->getMessage();

        return array(
            KEY_STATUS => $status,
            KEY_STATUS_CODE => $code,
            KEY_STATUS_MESSAGE => $message,
            KEY_DATA => $data
        );
    }

    /**
     * Function to send JSON to client
     *
     * @param int $status_code HTTP status code usually 200 for OK, 500 ERROR, 404 NOT FOUND
     * @param array $response Array response which will convert to json
     * @param array $custom_header Custom HTTP header to send
     *
     * @throws \BadFunctionCallException
     */
    private function send($status_code, $response, $custom_header = array())
    {
        $this->log->write_log('debug', $this->TAG . ': send: ' . $status_code . ', response_status: ');
        if (!$this->api_controller) throw new BadFunctionCallException('Only for API type controller');
        $this->output->set_status_header($status_code);
        $this->output->set_content_type('application/json');
        if (!empty($custom_header)) {
            foreach ($custom_header as $name => $header) {
                $this->output->set_header((sprintf('%s: %s', $name, $header)), true);
            }
        }
        $this->output->set_output(json_encode($response));
        $this->output->_display();
        die;
    }
}
