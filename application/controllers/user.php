<?php
/**
 * File - user.php
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
 * Class - User 
 * 인증 관련 클래스
 *
 * @category  Class
 * @package   JumpingNutsInc
 * @author    Hwan Oh <hwangoon@gmail.com>
 * @author    Jae Moon Kim <jam0929@gmail.com>
 * @copyright 2013-2014 Jumping Nuts Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://jumpingnuts.com
 */
class User extends CI_Controller
{
    /**
     * Method - Class Constructor
     * 
     * 특이사항 없음
     */
    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('security');
        $this->load->library('auth');
        //$this->lang->load('common');
        //$this->lang->load('auth');
        $this->lang->load('common', $this->config->item('language'));
        $this->lang->load('auth', $this->config->item('language'));
    }

    /**
     * Method - index
     *
     * 기본 실행 Method
     *
     * @return void
     */
    function index()
    {
        if ($message = $this->session->flashdata('message')) {
            $this->load->view('common/header');
            $this->load->view('common/nav');
            $this->load->view('auth/general_message', array('message' => $message));
            $this->load->view('common/footer');
        } else {
            redirect('/user/login/');
        }
    }

    /**
     * Login user on the site
     *
     * @return void
     */
    function login()
    {
        if ($this->auth->isLoggedIn()) {    // logged in
            //echo base_url());
            redirect('/');

        } elseif ($this->auth->isLoggedIn(false)) {    // logged in, not activated
            redirect('/user/sendAgain/');

        } else {
            $data['login_by_username'] 
                = ($this->config->item('login_by_username', 'auth') 
                AND $this->config->item('use_username', 'auth'));
            $data['login_by_email'] = $this->config->item('login_by_email', 'auth');

            $this->form_validation->set_rules(
                'login', 'Login', 'trim|required|xss_clean'
            );
            $this->form_validation->set_rules(
                'password', 'Password', 'trim|required|xss_clean'
            );
            $this->form_validation->set_rules(
                'remember', 'Remember me', 'integer'
            );

            // Get login for counting attempts to login
            if ($this->config->item('login_count_attempts', 'auth') 
                AND($login = $this->input->post('login'))
            ) {
                $login = $this->security->xss_clean($login);
            } else {
                $login = '';
            }

            $data['use_recaptcha'] = $this->config->item('use_recaptcha', 'auth');
            if ($this->auth->isMaxLoginAttemptsExceeded($login)) {
                if ($data['use_recaptcha']) {
                    $this->form_validation->set_rules(
                        'recaptcha_response_field', 
                        'Confirmation Code', 
                        'trim|xss_clean|required|callback__checkRecaptcha'
                    );
                } else {
                    $this->form_validation->set_rules(
                        'captcha', 
                        'Confirmation Code', 
                        'trim|xss_clean|required|callback__checkCaptcha'
                    );
                }
            }
            $data['errors'] = array();

            if ($this->form_validation->run()) {    // validation ok
                if ($this->auth->login(
                    $this->form_validation->set_value('login'),
                    $this->form_validation->set_value('password'),
                    $this->form_validation->set_value('remember'),
                    $data['login_by_username'],
                    $data['login_by_email']
                )) { // success
                    redirect('/');

                } else {
                    $errors = $this->auth->getErrorMessage();
                    if (isset($errors['banned'])) { // banned user
                        $this->_showMessage(
                            $this->lang->line('auth_message_banned').' '
                            .$errors['banned']
                        );

                    } elseif (isset($errors['not_activated'])) {    
                        // not activated user
                        redirect('/user/sendAgain/');

                    } else {    // fail
                        foreach ($errors as $k => $v) {
                            $data['errors'][$k] = $this->lang->line($v);
                        }
                    }
                }
            }
            $data['show_captcha'] = false;
            if ($this->auth->isMaxLoginAttemptsExceeded($login)) {
                $data['show_captcha'] = true;
                if ($data['use_recaptcha']) {
                    $data['recaptcha_html'] = $this->_createRecaptcha();
                } else {
                    $data['captcha_html'] = $this->_createCaptcha();
                }
            }
            $this->load->view('common/header', $data);
            $this->load->view('common/nav', $data);
            $this->load->view('auth/login_form', $data);
            $this->load->view('common/footer', $data);
        }
    }

    /**
     * Logout user
     *
     * @return void
     */
    function logout()
    {
        $this->auth->logout();

        $this->_showMessage($this->lang->line('auth_message_logged_out'));
    }

    /**
     * Register user on the site
     *
     * @return void
     */
    function register()
    {
        if ($this->auth->isLoggedIn()) {    // logged in
            redirect('/');

        } elseif ($this->auth->isLoggedIn(false)) {    // logged in, not activated
            redirect('/user/sendAgain/');

        } elseif (!$this->config->item('allow_registration', 'auth')) {    
            // registration is off
            $this->_showMessage(
                $this->lang->line('auth_message_registration_disabled')
            );

        } else {
            $use_username = $this->config->item('use_username', 'auth');
            if ($use_username) {
                $this->form_validation->set_rules(
                    'username', 
                    'Username', 
                    'trim|required|xss_clean|min_length['
                    .$this->config->item('username_min_length', 'auth')
                    .']|max_length['
                    .$this->config->item('username_max_length', 'auth')
                    .']|alpha_dash'
                );
            }
            $this->form_validation->set_rules(
                'email', 
                'Email', 
                'trim|required|xss_clean|valid_email'
            );
            $this->form_validation->set_rules(
                'password', 
                'Password', 
                'trim|required|xss_clean|min_length['
                .$this->config->item('password_min_length', 'auth')
                .']|max_length['
                .$this->config->item('password_max_length', 'auth')
                .']|alpha_dash'
            );
            $this->form_validation->set_rules(
                'confirm_password', 
                'Confirm Password', 
                'trim|required|xss_clean|matches[password]'
            );

            $captcha_registration    
                = $this->config->item('captcha_registration', 'auth');
            $use_recaptcha           
                = $this->config->item('use_recaptcha', 'auth');
            if ($captcha_registration) {
                if ($use_recaptcha) {
                    $this->form_validation->set_rules(
                        'recaptcha_response_field', 
                        'Confirmation Code', 
                        'trim|xss_clean|required|callback__checkRecaptcha'
                    );
                } else {
                    $this->form_validation->set_rules(
                        'captcha', 
                        'Confirmation Code', 
                        'trim|xss_clean|required|callback__checkCaptcha'
                    );
                }
            }
            $data['errors'] = array();

            $email_activation = $this->config->item('email_activation', 'auth');

            if ($this->form_validation->run()) {    // validation ok
                if (!is_null(
                    $data = $this->auth->createUser(
                        $use_username 
                        ? $this->form_validation->set_value('username') 
                        : '',
                        $this->form_validation->set_value('email'),
                        $this->form_validation->set_value('password'),
                        $email_activation
                    )
                )) {  // success

                    $data['site_name'] = $this->config->item('website_name', 'auth');

                    if ($email_activation) {    // send "activate" email
                        $data['activation_period'] = $this->config->item(
                            'email_activation_expire', 'auth'
                        ) / 3600;

                        $this->_sendEmail('activate', $data['email'], $data);

                        unset($data['password']); 
                        // Clear password (just for any case)

                        $this->_showMessage(
                            $this->lang->line(
                                'auth_message_registration_completed_1'
                            )
                        );

                    } else {
                        if ($this->config->item('email_account_details', 'auth')) {
                            // send "welcome" email

                            $this->_sendEmail('welcome', $data['email'], $data);
                        }
                        unset($data['password']); 
                        // Clear password (just for any case)

                        $this->_showMessage(
                            $this->lang->line(
                                'auth_message_registration_completed_2'
                            )
                            .' '
                            .anchor('/user/login/', 'Login')
                        );
                    }
                } else {
                    $errors = $this->auth->getErrorMessage();
                    foreach ($errors as $k => $v) {
                        $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }
            if ($captcha_registration) {
                if ($use_recaptcha) {
                    $data['recaptcha_html'] = $this->_createRecaptcha();
                } else {
                    $data['captcha_html'] = $this->_createCaptcha();
                }
            }
            $data['use_username'] = $use_username;
            $data['captcha_registration'] = $captcha_registration;
            $data['use_recaptcha'] = $use_recaptcha;
            
            $this->load->view('common/header', $data);
            $this->load->view('common/nav', $data);
            $this->load->view('auth/register_form', $data);
            $this->load->view('common/footer', $data);
        }
    }

    /**
     * Register user on the site
     *
     * @return void
     */
    function modify()
    {
        if ($this->auth->isLoggedIn() !== true) { // not logged in
            redirect('/');

        } elseif ($this->auth->isLoggedIn(false)) {   // logged in, not activated
            redirect('/user/sendAgain/');

        } else {
            $use_username = $this->config->item('use_username', 'auth');
            if ($use_username) {
                $this->form_validation->set_rules(
                    'username', 
                    'Username', 
                    'trim|required|xss_clean|min_length['
                    .$this->config->item('username_min_length', 'auth')
                    .']|max_length['
                    .$this->config->item('username_max_length', 'auth')
                    .']|alpha_dash'
                );
            }
            $this->form_validation->set_rules(
                'email', 
                'Email', 
                'trim|required|xss_clean|valid_email'
            );
            $this->form_validation->set_rules(
                'password', 
                'Password', 
                'trim|required|xss_clean|min_length['
                .$this->config->item('password_min_length', 'auth')
                .']|max_length['
                .$this->config->item('password_max_length', 'auth')
                .']|alpha_dash'
            );
            $this->form_validation->set_rules(
                'confirm_password', 
                'Confirm Password', 
                'trim|required|xss_clean|matches[password]'
            );

            $captcha_registration    
                = $this->config->item('captcha_registration', 'auth');
            $use_recaptcha            
                = $this->config->item('use_recaptcha', 'auth');
            if ($captcha_registration) {
                if ($use_recaptcha) {
                    $this->form_validation->set_rules(
                        'recaptcha_response_field', 
                        'Confirmation Code', 
                        'trim|xss_clean|required|callback__checkRecaptcha'
                    );
                } else {
                    $this->form_validation->set_rules(
                        'captcha', 
                        'Confirmation Code', 
                        'trim|xss_clean|required|callback__checkCaptcha'
                    );
                }
            }
            $data['errors'] = array();

            $email_activation = $this->config->item('email_activation', 'auth');

            if ($this->form_validation->run()) {    // validation ok
                if (!is_null(
                    $data = $this->auth->createUser(
                        $use_username 
                        ? $this->form_validation->set_value('username') 
                        : '',
                        $this->form_validation->set_value('email'),
                        $this->form_validation->set_value('password'),
                        $email_activation
                    )
                )) {    // success

                    $data['site_name'] = $this->config->item('website_name', 'auth');

                    if ($email_activation) {    // send "activate" email
                        $data['activation_period'] = $this->config->item(
                            'email_activation_expire', 'auth'
                        ) / 3600;

                        $this->_sendEmail('activate', $data['email'], $data);

                        unset($data['password']); 
                        // Clear password (just for any case)

                        $this->_showMessage(
                            $this->lang->line(
                                'auth_message_registration_completed_1'
                            )
                        );

                    } else {
                        if ($this->config->item('email_account_details', 'auth')) {
                            // send "welcome" email

                            $this->_sendEmail('welcome', $data['email'], $data);
                        }
                        unset($data['password']); 
                        // Clear password (just for any case)

                        $this->_showMessage(
                            $this->lang->line(
                                'auth_message_registration_completed_2'
                            )
                            .' '
                            .anchor('/user/login/', 'Login')
                        );
                    }
                } else {
                    $errors = $this->auth->getErrorMessage();
                    foreach ($errors as $k => $v) {
                        $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }
            
            if ($captcha_registration) {
                if ($use_recaptcha) {
                    $data['recaptcha_html'] = $this->_createRecaptcha();
                } else {
                    $data['captcha_html'] = $this->_createCaptcha();
                }
            }
            
            $data['use_username'] = $use_username;
            $data['captcha_registration'] = $captcha_registration;
            $data['use_recaptcha'] = $use_recaptcha;
            
            $this->load->view('common/header', $data);
            $this->load->view('common/nav', $data);
            $this->load->view('auth/modify_form', $data);
            $this->load->view('common/footer', $data);
        }
    }

    /**
     * Send activation email again, to the same or new email address
     *
     * @return void
     */
    function sendAgain()
    {
        if (!$this->auth->isLoggedIn(false)) {    // not logged in or activated
            redirect('/user/login/');

        } else {
            $this->form_validation->set_rules(
                'email', 
                'Email', 
                'trim|required|xss_clean|valid_email'
            );

            $data['errors'] = array();

            if ($this->form_validation->run()) {    // validation ok
                if (!is_null(
                    $data = $this->auth->changeEmail(
                        $this->form_validation->set_value('email')
                    )
                )) { // success

                    $data['site_name']    
                        = $this->config->item('website_name', 'auth');
                    $data['activation_period'] 
                        = $this->config->item('email_activation_expire', 'auth') 
                        / 3600;

                    $this->_sendEmail('activate', $data['email'], $data);

                    $this->_showMessage(
                        sprintf(
                            $this->lang->line('auth_message_activation_email_sent'), 
                            $data['email']
                        )
                    );

                } else {
                    $errors = $this->auth->getErrorMessage();
                    foreach ($errors as $k => $v) {
                        $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }
            $this->load->view('common/header', $data);
            $this->load->view('common/nav', $data);
            $this->load->view('auth/send_again_form', $data);
            $this->load->view('common/footer', $data);
        }
    }

    /**
     * Activate user account.
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function activate()
    {
        $user_id        = $this->uri->segment(3);
        $new_email_key    = $this->uri->segment(4);

        // Activate user
        if ($this->auth->activateUser($user_id, $new_email_key)) { // success
            $this->auth->logout();
            $this->_showMessage(
                $this->lang->line('auth_message_activation_completed')
                .' '
                .anchor('/user/login/', 'Login')
            );

        } else {            // fail
            $this->_showMessage(
                $this->lang->line('auth_message_activation_failed')
            );
        }
    }

    /**
     * Generate reset code (to change password) and send it to user
     *
     * @return void
     */
    function forgotPassword()
    {
        if ($this->auth->isLoggedIn()) {    // logged in
            redirect('/');

        } elseif ($this->auth->isLoggedIn(false)) {    // logged in, not activated
            redirect('/user/sendAgain/');

        } else {
            $this->form_validation->set_rules(
                'login', 
                'Email or login', 
                'trim|required|xss_clean'
            );

            $data['errors'] = array();

            if ($this->form_validation->run()) {            // validation ok
                if (!is_null(
                    $data = $this->auth->forgotPassword(
                        $this->form_validation->set_value('login')
                    )
                )) {

                    $data['site_name'] 
                        = $this->config->item('website_name', 'auth');

                    // Send email with password activation link
                    $this->_sendEmail('forgotPassword', $data['email'], $data);

                    $this->_showMessage(
                        $this->lang->line('auth_message_new_password_sent')
                    );

                } else {
                    $errors = $this->auth->getErrorMessage();
                    foreach ($errors as $k => $v) {
                        $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }
            $this->load->view('common/header', $data);
            $this->load->view('common/nav', $data);
            $this->load->view('auth/forgot_password_form', $data);
            $this->load->view('common/footer', $data);
        }
    }

    /**
     * Replace user password (forgotten) with a new one (set by user).
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function resetPassword()
    {
        $user_id        = $this->uri->segment(3);
        $new_pass_key    = $this->uri->segment(4);

        $this->form_validation->set_rules(
            'new_password', 
            'New Password', 
            'trim|required|xss_clean|min_length['
            .$this->config->item('password_min_length', 'auth')
            .']|max_length['
            .$this->config->item('password_max_length', 'auth')
            .']|alpha_dash'
        );
        $this->form_validation->set_rules(
            'confirm_new_password', 
            'Confirm new Password', 
            'trim|required|xss_clean|matches[new_password]'
        );

        $data['errors'] = array();

        if ($this->form_validation->run()) {            // validation ok
            if (!is_null(
                $data = $this->auth->resetPassword(
                    $user_id, $new_pass_key,
                    $this->form_validation->set_value('new_password')
                )
            )) {    // success

                $data['site_name'] = $this->config->item('website_name', 'auth');

                // Send email with new password
                $this->_sendEmail('resetPassword', $data['email'], $data);

                $this->_showMessage(
                    $this->lang->line('auth_message_new_password_activated')
                    .' '
                    .anchor('/user/login/', 'Login')
                );

            } else {    // fail
                $this->_showMessage(
                    $this->lang->line('auth_message_new_password_failed')
                );
            }
        } else {
            // Try to activate user by password key (if not activated yet)
            if ($this->config->item('email_activation', 'auth')) {
                $this->auth->activateUser($user_id, $new_pass_key, false);
            }

            if (!$this->auth->can_resetPassword($user_id, $new_pass_key)) {
                $this->_showMessage(
                    $this->lang->line('auth_message_new_password_failed')
                );
            }
        }
        $this->load->view('common/header', $data);
        $this->load->view('common/nav', $data);
        $this->load->view('auth/reset_password_form', $data);
        $this->load->view('common/footer', $data);
    }

    /**
     * Change user password
     *
     * @return void
     */
    function changePassword()
    {
        if (!$this->auth->isLoggedIn()) { // not logged in or not activated
            redirect('/user/login/');

        } else {
            $this->form_validation->set_rules(
                'old_password', 
                'Old Password', 
                'trim|required|xss_clean'
            );
            $this->form_validation->set_rules(
                'new_password', 
                'New Password', 
                'trim|required|xss_clean|min_length['
                .$this->config->item('password_min_length', 'auth')
                .']|max_length['
                .$this->config->item('password_max_length', 'auth')
                .']|alpha_dash'
            );
            $this->form_validation->set_rules(
                'confirm_new_password', 
                'Confirm new Password', 
                'trim|required|xss_clean|matches[new_password]'
            );

            $data['errors'] = array();

            if ($this->form_validation->run()) {    // validation ok
                if ($this->auth->changePassword(
                    $this->form_validation->set_value('old_password'),
                    $this->form_validation->set_value('new_password')
                )) {    
                    // success
                    $this->_showMessage(
                        $this->lang->line('auth_message_password_changed')
                    );

                } else {    // fail
                    $errors = $this->auth->getErrorMessage();
                    foreach ($errors as $k => $v) {
                        $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }
            $this->load->view('common/header', $data);
            $this->load->view('common/nav', $data);
            $this->load->view('auth/change_password_form', $data);
            $this->load->view('common/footer', $data);

        }
    }

    /**
     * Change user email
     *
     * @return void
     */
    function changeEmail()
    {
        if (!$this->auth->isLoggedIn()) { // not logged in or not activated
            redirect('/user/login/');

        } else {
            $this->form_validation->set_rules(
                'password', 
                'Password', 
                'trim|required|xss_clean'
            );
            $this->form_validation->set_rules(
                'email', 
                'Email', 
                'trim|required|xss_clean|valid_email'
            );

            $data['errors'] = array();

            if ($this->form_validation->run()) {    // validation ok
                if (!is_null(
                    $data = $this->auth->setNewEmail(
                        $this->form_validation->set_value('email'),
                        $this->form_validation->set_value('password')
                    )
                )) {    // success

                    $data['site_name'] 
                        = $this->config->item('website_name', 'auth');

                    // Send email with new email address and its activation link
                    $this->_sendEmail('changeEmail', $data['new_email'], $data);

                    $this->_showMessage(
                        sprintf(
                            $this->lang->line('auth_message_new_email_sent'), 
                            $data['new_email']
                        )
                    );

                } else {
                    $errors = $this->auth->getErrorMessage();
                    foreach ($errors as $k => $v) {
                        $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }
            $this->load->view('common/header', $data);
            $this->load->view('common/nav', $data);
            $this->load->view('auth/change_email_form', $data);
            $this->load->view('common/footer', $data);

        }
    }

    /**
     * Replace user email with a new one.
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function resetEmail()
    {
        $user_id        = $this->uri->segment(3);
        $new_email_key    = $this->uri->segment(4);

        // Reset email
        if ($this->auth->activateNewEmail($user_id, $new_email_key)) {    
            // success
            $this->auth->logout();
            $this->_showMessage(
                $this->lang->line('auth_message_new_email_activated')
                .' '
                .anchor('/user/login/', 'Login')
            );

        } else {    // fail
            $this->_showMessage(
                $this->lang->line('auth_message_new_email_failed')
            );
        }
    }

    /**
     * Delete user from the site (only when user is logged in)
     *
     * @return void
     */
    function unregister()
    {
        if (!$this->auth->isLoggedIn()) { // not logged in or not activated
            redirect('/user/login/');

        } else {
            $this->form_validation->set_rules(
                'password', 
                'Password', 
                'trim|required|xss_clean'
            );

            $data['errors'] = array();

            if ($this->form_validation->run()) {    // validation ok
                if ($this->auth->deleteUser(
                    $this->form_validation->set_value('password')
                )) {
                    // success
                    $this->_showMessage(
                        $this->lang->line('auth_message_unregistered')
                    );

                } else {    // fail
                    $errors = $this->auth->getErrorMessage();
                    foreach ($errors as $k => $v) {
                        $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }
            $this->load->view('common/header', $data);
            $this->load->view('common/nav', $data);
            $this->load->view('auth/unregister_form', $data);
            $this->load->view('common/footer', $data);
        }
    }

    /**
     * Show info message
     *
     * @param string $message message
     *
     * @return void
     */
    function _showMessage($message)
    {
        $this->session->set_flashdata('message', $message);
        redirect('/user/');
    }

    /**
     * Send email message of given type (activate, forgotPassword, etc.)
     *
     * @param string $type  type
     * @param string $email email
     * @param array  $data  data
     *
     * @return void
     */
    function _sendEmail($type, $email, &$data)
    {
        /*
        $this->load->library('email');
        $this->email->from(
            $this->config->item('webmaster_email', 'auth'), 
            $this->config->item('website_name', 'auth')
        );
        $this->email->reply_to($this->config->item(
            'webmaster_email', 'auth'), 
            $this->config->item('website_name', 'auth')
        );
        $this->email->to($email);
        $this->email->subject(
            sprintf(
                $this->lang->line('auth_subject_'.$type), 
                $this->config->item('website_name', 'auth')
            )
        );
        $this->email->message(
            $this->load->view('email/'.$type.'-html', $data, true)
        );
        $this->email->set_alt_message(
            $this->load->view('email/'.$type.'-txt', $data, true)
        );
        $this->email->send();
        */
        
        $this->load->library('awslib');
        $client = $this->awslib->aws->get('Ses');
        
        $msg = array();
        $msg['Source'] = '"'.$this->config->item('website_name', 'auth')
            .'" <'.$this->config->item('webmaster_email', 'auth').">";
        //ToAddresses must be an array
        $msg['Destination']['ToAddresses'][] = $email;

        $msg['Message']['Subject']['Data'] = sprintf(
            $this->lang->line('auth_subject_'.$type), 
            $this->config->item('website_name', 'auth')
        );
        $msg['Message']['Subject']['Charset'] = "UTF-8";

        $msg['Message']['Body']['Text']['Data'] 
            = $this->load->view('email/'.$type.'-txt', $data, true);
        $msg['Message']['Body']['Text']['Charset'] = "UTF-8";
        $msg['Message']['Body']['Html']['Data'] 
            = $this->load->view('email/'.$type.'-html', $data, true);
        $msg['Message']['Body']['Html']['Charset'] = "UTF-8";
        
        $client->sendEmail($msg);
        
    }

    /**
     * Create CAPTCHA image to verify user as a human
     *
     * @return string
     */
    function _createCaptcha()
    {
        $this->load->helper('captcha');

        $cap = create_captcha(
            array(
                'img_path' => './'.$this->config->item('captcha_path', 'auth'),
                'img_url' => base_url($this->config->item('captcha_path', 'auth')),
                'font_path' 
                    => './'.$this->config->item('captcha_fonts_path', 'auth'),
                'font_size' => $this->config->item('captcha_font_size', 'auth'),
                'img_width' => $this->config->item('captcha_width', 'auth'),
                'img_height' => $this->config->item('captcha_height', 'auth'),
                'show_grid' => $this->config->item('captcha_grid', 'auth'),
                'expiration' => $this->config->item('captcha_expire', 'auth'),
            )
        );

        // Save captcha params in session
        $this->session->set_flashdata(
            array(
                'captcha_word' => $cap['word'],
                'captcha_time' => $cap['time'],
            )
        );

        return $cap['image'];
    }

    /**
     * Callback function. Check if CAPTCHA test is passed.
     *
     * @param string $code code
     *
     * @return bool
     */
    function _checkCaptcha($code)
    {
        $time = $this->session->flashdata('captcha_time');
        $word = $this->session->flashdata('captcha_word');

        list($usec, $sec) = explode(" ", microtime());
        $now = ((float)$usec + (float)$sec);

        if ($now - $time > $this->config->item('captcha_expire', 'auth')) {
            $this->form_validation->set_message(
                '_checkCaptcha', 
                $this->lang->line('auth_captcha_expired')
            );
            return false;

        } elseif ( ($this->config->item('captcha_case_sensitive', 'auth') 
            AND $code != $word) 
            OR strtolower($code) != strtolower($word)
        ) {
            $this->form_validation->set_message(
                '_checkCaptcha', 
                $this->lang->line('auth_incorrect_captcha')
            );
            return false;
        }
        return true;
    }

    /**
     * Create reCAPTCHA JS and non-JS HTML to verify user as a human
     *
     * @return string
     */
    function _createRecaptcha()
    {
        $this->load->helper('recaptcha');

        // Add custom theme so we can get only image
        $options = "
        <script>
            var RecaptchaOptions = {
                theme: 'custom', 
                custom_theme_widget: 'recaptcha_widget'
            };
        </script>\n";

        // Get reCAPTCHA JS and non-JS HTML
        $html = recaptcha_get_html(
            $this->config->item('recaptcha_public_key', 'auth')
        );

        return $options.$html;
    }

    /**
     * Callback function. Check if reCAPTCHA test is passed.
     *
     * @return bool
     */
    function _checkRecaptcha()
    {
        $this->load->helper('recaptcha');

        $resp = recaptcha_check_answer(
            $this->config->item('recaptcha_private_key', 'auth'),
            $_SERVER['REMOTE_ADDR'],
            $_POST['recaptcha_challenge_field'],
            $_POST['recaptcha_response_field']
        );

        if (!$resp->is_valid) {
            $this->form_validation->set_message(
                '_checkRecaptcha', 
                $this->lang->line('auth_incorrect_captcha')
            );
            return false;
        }
        return true;
    }

}

/* End of file user.php */
/* Location: ./application/controllers/user.php */