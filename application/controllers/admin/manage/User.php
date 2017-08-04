<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class User
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class User extends TDB_Controller
{
    private $TAG = 'User';

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->check_access(true);
    }

    public function index()
    {
        $data = $this->basic_data();
        $data['title'] = 'Pengguna';
        $data['menu'] = 30;
        $data['breadcrumb'] = array(array('title' => 'pengguna'));
        $data['users'] = $this->m_user->get(null, null, 1, 1, array(1, 2));
        $this->load->view('admin/base/header', $data);
        $this->load->view('admin/manage/user/list', $data);
        $this->load->view('admin/base/footer', $data);
    }

    public function user_banned()
    {
        $data = $this->basic_data();
        $data['title'] = 'Pengguna Terblokir';
        $data['menu'] = 31;
        $data['breadcrumb'] = array(array('title' => 'pengguna'));
        $data['users'] = $this->m_user->get(null, null, 4, 1, array(1, 2));
        $this->load->view('admin/base/header', $data);
        $this->load->view('admin/manage/user/list', $data);
        $this->load->view('admin/base/footer', $data);
    }

    public function detail($id, $validation = null)
    {
        $from_validation = (strtolower($validation) == 'validation');
        $this->log->write_log('debug', $this->TAG . ': validation_detail: ');
        $registered = $this->m_user->get(null, null, ($from_validation) ? array(2, 3) : array(1, 4), $id);
        if (!empty($registered)) {
            $registered = $registered[0];
            $data = $this->basic_data();
            $data['title'] = 'Pengguna';
            $data['sub_title'] = 'Detail';
            $data['menu'] = 30;
            if ($registered->STATUS == 4) $data['menu'] = 31;
            $data['breadcrumb'] = array(
                array('url' => 'admin/manage/user', 'title' => 'pengguna'),
                array('title' => 'detail')
            );
            $data['from_validation'] = $from_validation;
            if ($registered->ID != $registered->ID_UPDATE) {
                $last_update_by = $this->m_user->get(null, null, null, $registered->ID_UPDATE);
                if (!empty($last_update_by)) $data['last_update_by'] = $last_update_by[0]->FULL_NAME;
            }
            $data['registered'] = $registered;
            $this->load->view('admin/base/header', $data);
            $this->load->view('admin/manage/user/detail', $data);
            $this->load->view('admin/base/footer', $data);
        } else {
            $this->session->set_flashdata('error',
                                          'User tidak ditemukan' .
                                          ($from_validation ? ' atau telah berubah status' : ''));
            if (!$from_validation) {
                redirect('admin/manage/register/validation');
            } else redirect('admin/manage/user');
        }
    }

    public function unbanned($id)
    {
        $this->log->write_log('debug', $this->TAG . ': validate: ');
        $user = $this->get_user();
        $result = $this->m_user->update_status($id, 1, $user->ID);
        redirect('admin/manage/user/detail/'.$id);
    }

    public function banned($id)
    {
        $this->log->write_log('debug', $this->TAG . ': validate: ');
        $user = $this->get_user();
        $result = $this->m_user->update_status($id, 4, $user->ID);
        redirect('admin/manage/user/detail/'.$id);
    }


}
