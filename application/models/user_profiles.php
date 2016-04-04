<?php 
/**
 * File - user_profiles.php
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
 * Class - User_Profiles 
 * 사용자 프로필 모델 클래스
 *
 * @category  Class
 * @package   JumpingNutsInc
 * @author    Hwan Oh <hwangoon@gmail.com>
 * @author    Jae Moon Kim <jam0929@gmail.com>
 * @copyright 2013-2014 Jumping Nuts Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://jumpingnuts.com
 */
class User_Profiles extends MY_Model
{
    /**
     * Memeber functions
     *
     * @var string $_table 테이블 이름
     */
    private $_table = "User_profiles";
    
    /**
     * Method - Class Constructor
     * 
     * 특이사항 없음
     */
    public function __construct() 
    {
        parent::__construct();
    }
}

/* End of file user_profiles.php */
/* Location: ./application/models/user_profiles.php */