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
 * Class Connection
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Connection extends TDB_Controller
{
    private $TAG = 'Connection';

    public function __construct() { parent::__construct(true); }

    public function token_get($username)
    {
        $this->log->write_log('debug', $this->TAG . ': token_get: ');
        $this->load->model('m_user');
        $users = $this->m_user->get($username);
        if (empty($users)) {
            $this->response_error(404, "Error");
        }
        $user = $users[0];
        $user->__cast();

        if (!empty($user->DEVICE_ID)) {
            $this->response($this->create_token([$user->USERNAME, $user->DEVICE_ID]));
        } else {
            $this->response_error(404, "Error");
        }
    }

    public function test_thread_get()
    {
        $this->log->write_log('debug', $this->TAG . ': test_thread_get: ');
        if (!$this->check_access()) {
            $this->response_error(404, "Error");
        }
        $user = $this->get_user();
        $token = $this->create_token([$user->USERNAME, $user->DEVICE_ID]);

        $handler = new \GuzzleHttp\Handler\CurlMultiHandler();
        $client = new \GuzzleHttp\Client(['curl' => [CURLOPT_SSL_VERIFYPEER => false, CURLOPT_TIMEOUT => 1],
                                          'handler' =>
                                              \GuzzleHttp\HandlerStack::create($handler)]);
        $promise = $client->requestAsync('POST',
                                         base_url('api/test/connection/test_thread_second'),
                                         [
                                             'headers' => ['Authorization' => 'Bearer ' . $token],
                                             'form_params' => [
                                                 'halo' => 'Aloha',
                                             ]
                                         ]);
//        $response = $promise->wait();
//        $this->log->write_log('debug', $this->TAG . ': test_thread_get: '. ((string) $response->getBody()));
//        $this->log->write_log('debug', $this->TAG . ': test_thread_get: promise: '.$promise->getState());
//        $queue = \GuzzleHttp\Promise\queue();
//        $queue->run();
        $this->log->write_log('debug', $this->TAG . ': test_thread_get: promise: ' . $promise->getState());
        $handler->execute();
//        while (!GuzzleHttp\Promise\is_settled($promise)) {
//            $handler->tick();
//            // It won't hog the processor because it uses `curl_multi_select`; no need to sleep here.
//        }
        $this->log->write_log('debug', $this->TAG . ': test_thread_get: promise: ' . $promise->getState());
        $promise->then(
            function (\Psr\Http\Message\ResponseInterface $response) {
                $this->log->write_log('debug', $this->TAG . ': test_thread_get: ' . ((string)$response->getBody()));
            },
            function (\GuzzleHttp\Exception\RequestException $e) {
                $this->log->write_log('debug',
                                      $this->TAG .
                                      ': test_thread_get: ' . $e->getRequest()->getMethod() . ': ' .
                                      ($e->getMessage()));
            });

        $this->log->write_log('debug', $this->TAG . ': test_thread_get: Finish');
        echo 'First Thread';
//        $this->response('First Thread');

    }

    public function test_thread_second_post()
    {
        $this->log->write_log('debug', $this->TAG . ': test_thread_second_get: ');
        ini_set('max_execution_time', 300);
        if (!$this->check_access()) {
            $this->response_error(404, "Error");
        }
        sleep(100);
        $post = $this->input->post('halo');
        $this->log->write_log('debug', $this->TAG . ': test_thread_second_get: Finish Sleep');
        $this->log->write_log('debug', $this->TAG . ': test_thread_second_get: Data: ' . $post);
        $this->response('Second Thread');
    }

}
