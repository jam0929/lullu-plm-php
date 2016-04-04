<?php 
/**
 * File - admin.php
 *
 * PHP Version 5.4
 *
 * @category  Class
 * @package   JumpingNutsInc
 * @author    Hwan Oh <hwangoon@gmail.com>
 * @author    Jae Moon Kim <jam0929@gmail.com>
 * @copyright 2013-2014 Jumping Nuts Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://jumpingnuts.com
 */
 
if (!defined('BASEPATH')) { 
    exit('No direct script access allowed');
};

/**
 * Class - Admin 
 * 관리 관련 클래스
 *
 * @category  Class
 * @package   JumpingNutsInc
 * @author    Hwan Oh <hwangoon@gmail.com>
 * @author    Jae Moon Kim <jam0929@gmail.com>
 * @copyright 2013-2014 Jumping Nuts Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://jumpingnuts.com
 */
class Admin extends CI_Controller
{
    /**
     * Memeber functions
     *
     * @var array $data View를 위한 data 저장
     */
    var $data;
    
    /**
     * Method - Class Constructor
     * 
     * 특이사항 없음
     */
    function __construct()
    {
        parent::__construct();
        
        $this->lang->load('common', $this->config->item('language'));
        
        $this->data['title'] = $this->config->item('base_url');
    }

    /**
     * Method - index
     *
     * 기본 실행 Method
     * 페이지 타입을 전달받고 해당 view를 호출하여 출력
     *
     * @param string $type 페이지 타입 설정
     *
     * @return bool
     */
    function index($type=null)
    {
        //$this->data['is_login'] = $this->tank_auth->is_logged_in();
        
        $this->load->view('common/header', $this->data);
        $this->load->view('common/nav', $this->data);
        $this->load->view(
            ($type == null) ? 'common/404' : 'frontend/'.$type, $this->data
        );
        $this->load->view('common/footer', $this->data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */