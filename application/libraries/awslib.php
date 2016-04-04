<?php 
/**
 * File - awslib.php
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

require_once dirname(__FILE__).'/aws.phar';

use Aws\Common\Aws;

/**
 * Class - Awslib 
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
class Awslib
{
    /**
     * Memeber functions
     *
     * @var object $aws         AWS object
     */
    public $aws;

    /**
     * Method - Class Constructor
     * 
     * 특이사항 없음
     */
    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->config('aws', true);
        $this->aws = Aws::factory(
            array(
                'key' => $this->ci->config->item('key', 'aws'),
                'secret' => $this->ci->config->item('secret', 'aws'),
                'region' => $this->ci->config->item('region', 'aws')
            )
        );
        /*
        $this->aws = Aws::factory(array(
            'includes' => array('_aws'),
            'services' => array(
                'default_settings' => array(
                    'params' => array(
                        'key'    => 'AKIAJOILFK5O6IE4F4MQ',
                        'secret' => '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',
                        // OR: 'profile' => 'my_profile',
                        'region' => 'us-west-1'
                    )
                )
            )
        ));
        */
    }

}

/* End of file awslib.php */
/* Location: ./application/libraries/awslib.php */