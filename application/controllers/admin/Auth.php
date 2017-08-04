<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Auth
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Auth extends TDB_Controller
{
    private $TAG = 'Auth';

    /**
     * Auth constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = array();
        $data['next'] = $this->__process_next();
        redirect('admin/auth/signin' . (!empty($data['next']) ? '?next=' . $data['next'] : ''));
    }

    public function signin()
    {
        $this->log->write_log('debug', $this->TAG . ': signin: ');
        if (!$this->check_access()) {
            $data = $this->basic_data();
            $data['next'] = $this->__process_next();
            $this->load->view('admin/auth/signin', $data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function signin_process()
    {
        $this->log->write_log('debug', $this->TAG . ': signin_process: ');
        $data = array();
        $data['next'] = $this->__process_next(false);
        if (!$this->check_access()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            if (empty($username) || empty($password)) {
                redirect('admin/auth');
                die;
            }

            $this->load->model('m_user');
            $user = $this->m_user->get($username);
            if (!empty($user)) {
                $user = $user[0];
                if (password_verify($password, $user->PASSWORD)) {
                    if ($user->TYPE == 2 && $user->STATUS == 1) {
                        $this->session->set_userdata('session_user', $user->USERNAME);
                        if (!empty($data['next'])) {
                            redirect(urldecode(base64_decode($data['next'])));
                            die;
                        }
                    }
                }
            }
        }
        redirect('admin/auth/' . (!empty($data['next']) ? '?next=' . $data['next'] : ''));
    }

    public function signout()
    {
        if($this->check_access()) {
            $this->session->sess_destroy();
        }
        redirect('admin/auth');
    }

    private function __process_next($get = true)
    {
        $data = null;
        $next_url = null;
        if ($get) {
            $next_url = $this->input->get('next');
        } else $next_url = $this->input->post('next');
        if (!empty($next_url)) {
            $new_next_url = urldecode(base64_decode($next_url));
            $parse_next_url = parse_url($new_next_url);
            $parse_app_url = parse_url(base_url());
            if ($parse_next_url != false && $parse_app_url != false) {
                if (strtolower($parse_app_url['host']) == strtolower($parse_next_url['host'])) {
                    $data = $next_url;
                }
            }
        }

        return $data;
    }

}
