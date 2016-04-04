<?php 
/**
 * File - login_attempts.php
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
 * Class - Login_Attempts 
 * 사용자 로그인 기록 모델 클래스
 *
 * @category  Class
 * @package   JumpingNutsInc
 * @author    Hwan Oh <hwangoon@gmail.com>
 * @author    Jae Moon Kim <jam0929@gmail.com>
 * @copyright 2013-2014 Jumping Nuts Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://jumpingnuts.com
 */
class Login_Attempts extends CI_Model
{
    /**
     * Memeber functions
     *
     * @var string $_table_name 테이블 이름
     */
    private $_table_name = 'Login_attempts';
    
    /**
     * Method - Class Constructor
     * 
     * 특이사항 없음
     */
    function __construct()
    {
        parent::__construct();

        $ci =& get_instance();
        $this->_table_name 
            = $ci->config->item('db_table_prefix', 'tank_auth').$this->_table_name;
    }

    /**
     * Get number of attempts to login occured from given IP-address or login
     *
     * @param string $ip_address ip address
     * @param string $login      login
     *
     * @return int
     */
    function getAttemptsNum($ip_address, $login)
    {
        $this->db->select('1', false);
        $this->db->where('ip_address', $ip_address);
        if (strlen($login) > 0) {
            $this->db->or_where('login', $login);
        }

        $qres = $this->db->get($this->_table_name);
        return $qres->num_rows();
    }

    /**
     * Increase number of attempts for given IP-address and login
     *
     * @param string $ip_address ip address
     * @param string $login      login
     *
     * @return void
     */
    function increaseAttempt($ip_address, $login)
    {
        $this->db->insert(
            $this->_table_name, 
            array(
                'ip_address' => $ip_address, 
                'login' => $login
            )
        );
    }

    /**
     * Clear all attempt records for given IP-address and login.
     * Also purge obsolete login attempts (to keep DB clear).
     *
     * @param string $ip_address    ip address
     * @param string $login         login
     * @param int    $expire_period expire period
     *
     * @return void
     */
    function clearAttempts($ip_address, $login, $expire_period = 86400)
    {
        $this->db->where(array('ip_address' => $ip_address, 'login' => $login));

        // Purge obsolete login attempts
        $this->db->or_where('UNIX_TIMESTAMP(time) <', time() - $expire_period);

        $this->db->delete($this->_table_name);
    }
}

/* End of file login_attempts.php */
/* Location: ./application/models/auth/login_attempts.php */