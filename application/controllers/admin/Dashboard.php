<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Dashboard
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Dashboard extends TDB_Controller
{
    private $TAG = 'Dashboard';

    /**
     * Dashboard constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->check_access(true);
    }

    public function index()
    {
        $data = $this->basic_data();
        $data['title'] = 'Dashboard';
        $data['menu'] = 1;
        $this->load->view('admin/base/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('admin/base/footer', $data);
    }

}
