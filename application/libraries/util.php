<?php 
/**
 * File - util.php
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
 * Class - Util 
 * AWS 관련 클래스
 *
 * @category  Class
 * @package   JumpingNutsInc
 * @author    Hwan Oh <hwangoon@gmail.com>
 * @author    Jae Moon Kim <jam0929@gmail.com>
 * @copyright 2013-2014 Jumping Nuts Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://jumpingnuts.com
 */
class Util
{
    /**
     * Method - Class Constructor
     * 
     * 특이사항 없음
     */
    function __construct()
    {
        $this->ci =&get_instance();
        $this->ci->load->helper('url');
    }
    
    /**
     * Method - Awslib
     *
     * @param string $url url
     *
     * @return string
     */
    function absUrl($url = null) 
    {
        if ($url == null) {
            $url = current_url();
        }
        $parse = null;
        
        if (strpos($url, 'http://') !== false 
            OR strpos($url, 'https://') !== false
        ) {
            $parse = parse_url($url);
        } else {
            $parse = parse_url(base_url($url));
        }
        
        return isset($parse) && isset($parse['path']) ? $parse['path'] : '';
    }
}