<?php 
/**
 * File - auth.php
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

require_once 'phpass-0.1/PasswordHash.php';

define('STATUS_ACTIVATED', '1');
define('STATUS_NOT_ACTIVATED', '0');

/**
 * Class - Auth 
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
class Auth
{
    /**
     * Memeber functions
     *
     * @var array $_error 에러 배열
     */
    private $_error = array();

    /**
     * Method - Class Constructor
     * 
     * 특이사항 없음
     */
    function __construct()
    {
        $this->ci =& get_instance();

        $this->ci->load->config('auth', true);

        $this->ci->load->library('session');
        $this->ci->load->database();
        $this->ci->load->model('auth/users');

        // Try to _autologin
        $this->_autologin();
    }

    /**
     * Login user on the site. Return true if login is successful
     * (user exists and activated, password is correct), otherwise false.
     *
     * @param string $login             login
     * @param string $password          password
     * @param bool   $remember          remember
     * @param bool   $login_by_username remember
     * @param bool   $login_by_email    remember
     *
     * @return bool
     */
    function login($login, $password, $remember, $login_by_username, $login_by_email)
    {
        if ((strlen($login) > 0) AND (strlen($password) > 0)) {

            // Which function to use to login (based on config)
            if ($login_by_username AND $login_by_email) {
                $get_user_func = 'getUserByLogin';
            } else if ($login_by_username) {
                $get_user_func = 'getUserByUsername';
            } else {
                $get_user_func = 'getUserByEmail';
            }

            if (!is_null(
                $user = $this->ci->users->$get_user_func($login)
            )) {    // login ok

                // Does password match hash in database?
                $hasher = new PasswordHash(
                    $this->ci->config->item('phpass_hash_strength', 'auth'),
                    $this->ci->config->item('phpass_hash_portable', 'auth')
                );
                if ($hasher->CheckPassword($password, $user->password)) {
                    // password ok

                    if ($user->banned == 1) {
                        // fail - banned
                        $this->_error = array('banned' => $user->ban_reason);

                    } else {
                        
                        $this->ci->session->set_userdata(
                            array(
                                'user_id' => $user->id,
                                'username' => $user->username,
                                'email' => $user->email,
                                'status' => ($user->activated == 1) 
                                ? STATUS_ACTIVATED 
                                : STATUS_NOT_ACTIVATED,
                                'isAdmin' => $user->is_admin,
                                'profiles' 
                                    => $this->ci->users->getProfileById($user->id)
                            )
                        );

                        if ($user->activated == 0) {    // fail - not activated
                            $this->_error = array('not_activated' => '');

                        } else {    // success
                            if ($remember) {
                                $this->_createAutologin($user->id);
                            }

                            $this->_clearLoginAttempts($login);

                            $this->ci->users->updateLoginInfo(
                                $user->id,
                                $this->ci->config->item(
                                    'login_record_ip', 'auth'
                                ),
                                $this->ci->config->item(
                                    'login_record_time', 'auth'
                                )
                            );
                            return true;
                        }
                    }
                } else {    // fail - wrong password
                    $this->_increaseLoginAttempt($login);
                    $this->_error = array('password' => 'auth_incorrect_password');
                }
            } else {    // fail - wrong login
                $this->_increaseLoginAttempt($login);
                $this->_error = array('login' => 'auth_incorrect_login');
            }
        }
        return false;
    }

    /**
     * Logout user from the site
     *
     * @return void
     */
    function logout()
    {
        $this->_deleteAutologin();
        /*
        See http://codeigniter.com/forums/viewreply/662369/ 
        as the reason for the next line
        */
        $this->ci->session->set_userdata(
            array(
                'user_id' => null, 
                'username' => null, 
                'email' => null, 
                'status' => null, 
                'isAdmin' => null,
                'profiles' => null
            )
        );

        $this->ci->session->sess_destroy();
    }

    /**
     * Check if user logged in. Also test if user is activated or not.
     *
     * @param bool $activated activated
     *
     * @return bool
     */
    function isLoggedIn($activated = true)
    {
        return 
            $this->ci->session->userdata('status') 
            === ($activated ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED);
    }
    
    /**
     * Check if user logged in. Also test if user is activated or not.
     *
     * @param bool $activated activated
     *
     * @return bool
     */
    function isLogin($activated = true) 
    {
        return $this->isLoggedIn($activated);
    }

    /**
     * Check if user is admin
     *
     * Jam0929 - 141126 is admin added by jm
     *
     * @return bool
     */
    function isAdmin()
    {
        return $this->ci->session->userdata('isAdmin') == 1;
    }

    /**
     * Get user_id
     *
     * @return string
     */
    function getUserId()
    {
        return $this->ci->session->userdata('user_id');
    }

    /**
     * Get username
     *
     * @return string
     */
    function getUsername()
    {
        return $this->ci->session->userdata('username');
    }

    /**
     * Create new user on the site and return some data about it:
     * user_id, username, password, email, new_email_key (if any).
     *
     * @param string $username         username
     * @param string $email            email
     * @param string $password         password
     * @param bool   $email_activation email activation
     *
     * @return array
     */
    function createUser($username, $email, $password, $email_activation)
    {
        if ((strlen($username) > 0) 
            AND !$this->ci->users->isUsernameAvailable($username)
        ) {
            $this->_error = array('username' => 'auth_username_in_use');

        } elseif (!$this->ci->users->isEmailAvailable($email)) {
            $this->_error = array('email' => 'auth_email_in_use');

        } else {
            // Hash password using phpass
            $hasher = new PasswordHash(
                $this->ci->config->item('phpass_hash_strength', 'auth'),
                $this->ci->config->item('phpass_hash_portable', 'auth')
            );
            $hashed_password = $hasher->HashPassword($password);

            $data = array(
                'username' => $username,
                'password' => $hashed_password,
                'email' => $email,
                'last_ip' => $this->ci->input->ip_address(),
            );

            if ($email_activation) {
                $data['new_email_key'] = md5(rand().microtime());
            }
            if (!is_null(
                $res = $this->ci->users->createUser($data, !$email_activation)
            )) {
                $data['user_id'] = $res['user_id'];
                $data['password'] = $password;
                unset($data['last_ip']);
            
                return $data;
            }
        }
        return null;
    }

    /**
     * Check if username available for registering.
     * Can be called for instant form validation.
     *
     * @param string $username username
     *
     * @return bool
     */
    function isUsernameAvailable($username)
    {
        return ((strlen($username) > 0) 
        AND $this->ci->users->isUsernameAvailable($username));
    }

    /**
     * Check if email available for registering.
     * Can be called for instant form validation.
     *
     * @param string $email email
     *
     * @return bool
     */
    function isEmailAvailable($email)
    {
        return ((strlen($email) > 0) AND $this->ci->users->isEmailAvailable($email));
    }

    /**
     * Change email for activation and return some data about user:
     * user_id, username, email, new_email_key.
     * Can be called for not activated users only.
     *
     * @param string $email email
     *
     * @return array
     */
    function changeEmail($email)
    {
        $user_id = $this->ci->session->userdata('user_id');

        if (!is_null($user = $this->ci->users->getUserById($user_id, false))) {

            $data = array(
                'user_id'    => $user_id,
                'username'    => $user->username,
                'email'        => $email,
            );
            if (strtolower($user->email) == strtolower($email)) {
                // leave activation key as is
                $data['new_email_key'] = $user->new_email_key;
                
                return $data;

            } elseif ($this->ci->users->isEmailAvailable($email)) {
                $data['new_email_key'] = md5(rand().microtime());
                $this->ci->users->setNewEmail(
                    $user_id, $email, $data['new_email_key'], false
                );
                
                return $data;

            } else {
                $this->_error = array('email' => 'auth_email_in_use');
            }
        }
        return null;
    }

    /**
     * Activate user using given key
     *
     * @param string $user_id           user id
     * @param string $activation_key    activation key
     * @param bool   $activate_by_email activate by email
     *
     * @return bool
     */
    function activateUser($user_id, $activation_key, $activate_by_email = true)
    {
        $this->ci->users->purgeNa(
            $this->ci->config->item('email_activation_expire', 'auth')
        );

        if ((strlen($user_id) > 0) AND (strlen($activation_key) > 0)) {
            return $this->ci->users->activateUser(
                $user_id, $activation_key, $activate_by_email
            );
        }
        return false;
    }

    /**
     * Set new password key for user and return some data about user:
     * user_id, username, email, new_pass_key.
     * The password key can be used to verify user when resetting his/her password.
     *
     * @param string $login login
     *
     * @return array
     */
    function forgotPassword($login)
    {
        if (strlen($login) > 0) {
            if (!is_null($user = $this->ci->users->getUserByLogin($login))) {

                $data = array(
                    'user_id'        => $user->id,
                    'username'        => $user->username,
                    'email'            => $user->email,
                    'new_pass_key'    => md5(rand().microtime()),
                );

                $this->ci->users->setPasswordKey($user->id, $data['new_pass_key']);
                
                return $data;

            } else {
                $this->_error = array('login' => 'auth_incorrect_email_or_username');
            }
        }
        return null;
    }

    /**
     * Check if given password key is valid and user is authenticated.
     *
     * @param string $user_id      user id
     * @param string $new_pass_key new pass key
     *
     * @return bool
     */
    function canResetPassword($user_id, $new_pass_key)
    {
        if ((strlen($user_id) > 0) AND (strlen($new_pass_key) > 0)) {
            return $this->ci->users->canResetPassword(
                $user_id,
                $new_pass_key,
                $this->ci->config->item('forgotPassword_expire', 'auth')
            );
        }
        return false;
    }

    /**
     * Replace user password (forgotten) with a new one (set by user)
     * and return some data about it: user_id, username, new_password, email.
     *
     * @param string $user_id      user id
     * @param string $new_pass_key new pass key
     * @param string $new_password new password
     *
     * @return bool
     */
    function resetPassword($user_id, $new_pass_key, $new_password)
    {
        if ((strlen($user_id) > 0) 
            AND (strlen($new_pass_key) > 0) 
            AND (strlen($new_password) > 0)
        ) {

            if (!is_null($user = $this->ci->users->getUserById($user_id, true))) {

                // Hash password using phpass
                $hasher = new PasswordHash(
                    $this->ci->config->item('phpass_hash_strength', 'auth'),
                    $this->ci->config->item('phpass_hash_portable', 'auth')
                );
                $hashed_password = $hasher->HashPassword($new_password);

                if ($this->ci->users->resetPassword(
                    $user_id,
                    $hashed_password,
                    $new_pass_key,
                    $this->ci->config->item('forgotPassword_expire', 'auth')
                )) {    // success

                    // Clear all user's _autologins
                    $this->ci->load->model('auth/user_autologin');
                    $this->ci->userAutologin->clear($user->id);

                    return array(
                        'user_id'        => $user_id,
                        'username'        => $user->username,
                        'email'            => $user->email,
                        'new_password'    => $new_password,
                    );
                }
            }
        }
        return null;
    }

    /**
     * Change user password (only when user is logged in)
     *
     * @param string $old_pass old pass
     * @param string $new_pass new pass
     *
     * @return bool
     */
    function changePassword($old_pass, $new_pass)
    {
        $user_id = $this->ci->session->userdata('user_id');

        if (!is_null($user = $this->ci->users->getUserById($user_id, true))) {

            // Check if old password correct
            $hasher = new PasswordHash(
                $this->ci->config->item('phpass_hash_strength', 'auth'),
                $this->ci->config->item('phpass_hash_portable', 'auth')
            );
            if ($hasher->CheckPassword($old_pass, $user->password)) {   // success

                // Hash new password using phpass
                $hashed_password = $hasher->HashPassword($new_pass);

                // Replace old password with new one
                $this->ci->users->changePassword($user_id, $hashed_password);
                return true;

            } else {    // fail
                $this->_error = array('old_password' => 'auth_incorrect_password');
            }
        }
        return false;
    }

    /**
     * Change user email (only when user is logged in) and 
     * return some data about user:
     * user_id, username, new_email, new_email_key.
     * The new email cannot be used for login or 
     * notification before it is activated.
     *
     * @param string $new_email new email
     * @param string $password  password
     *
     * @return array
     */
    function setNewEmail($new_email, $password)
    {
        $user_id = $this->ci->session->userdata('user_id');

        if (!is_null($user = $this->ci->users->getUserById($user_id, true))) {

            // Check if password correct
            $hasher = new PasswordHash(
                $this->ci->config->item('phpass_hash_strength', 'auth'),
                $this->ci->config->item('phpass_hash_portable', 'auth')
            );
            if ($hasher->CheckPassword($password, $user->password)) {   // success

                $data = array(
                    'user_id'    => $user_id,
                    'username'    => $user->username,
                    'new_email'    => $new_email,
                );

                if ($user->email == $new_email) {
                    $this->_error = array('email' => 'auth_current_email');

                } elseif ($user->new_email == $new_email) {
                    // leave email key as is
                    $data['new_email_key'] = $user->new_email_key;
                    
                    return $data;
                    
                } elseif ($this->ci->users->isEmailAvailable($new_email)) {
                
                    $data['new_email_key'] = md5(rand().microtime());
                    $this->ci->users->setNewEmail(
                        $user_id, $new_email, $data['new_email_key'], true
                    );
                    
                    return $data;

                } else {
                    $this->_error = array('email' => 'auth_email_in_use');
                }
            } else {    // fail
                $this->_error = array('password' => 'auth_incorrect_password');
            }
        }
        return null;
    }

    /**
     * Activate new email, if email activation key is valid.
     *
     * @param string $user_id       user id
     * @param string $new_email_key new email key
     *
     * @return bool
     */
    function activateNewEmail($user_id, $new_email_key)
    {
        if ((strlen($user_id) > 0) AND (strlen($new_email_key) > 0)) {
            return $this->ci->users->activateNewEmail(
                $user_id,
                $new_email_key
            );
        }
        return false;
    }

    /**
     * Delete user from the site (only when user is logged in)
     *
     * @param string $password password
     *
     * @return bool
     */
    function deleteUser($password)
    {
        $user_id = $this->ci->session->userdata('user_id');

        if (!is_null($user = $this->ci->users->getUserById($user_id, true))) {

            // Check if password correct
            $hasher = new PasswordHash(
                $this->ci->config->item('phpass_hash_strength', 'auth'),
                $this->ci->config->item('phpass_hash_portable', 'auth')
            );
            if ($hasher->CheckPassword($password, $user->password)) {   // success

                $this->ci->users->deleteUser($user_id);
                $this->logout();
                return true;

            } else {    // fail
                $this->_error = array('password' => 'auth_incorrect_password');
            }
        }
        return false;
    }

    /**
     * Get error message.
     * Can be invoked after any failed operation such as login or register.
     *
     * @return string
     */
    function getErrorMessage()
    {
        return $this->_error;
    }

    /**
     * Save data for user's _autologin
     *
     * @param int $user_id user id
     *
     * @return bool
     */
    private function _createAutologin($user_id)
    {
        $this->ci->load->helper('cookie');
        $key = substr(
            md5(
                uniqid(
                    rand().get_cookie($this->ci->config->item('sess_cookie_name'))
                )
            ), 0, 16
        );

        $this->ci->load->model('auth/user_autologin');
        $this->ci->userAutologin->purge($user_id);

        if ($this->ci->userAutologin->set($user_id, md5($key))) {
            set_cookie(
                array(
                    'name' => $this->ci->config->item(
                        'autologin_cookie_name', 'auth'
                    ),
                    'value' => serialize(
                        array('user_id' => $user_id, 'key' => $key)
                    ),
                    'expire' => $this->ci->config->item(
                        'autologin_cookie_life', 'auth'
                    ),
                )
            );
            
            return true;
        }
        return false;
    }

    /**
     * Clear user's _autologin data
     *
     * @return void
     */
    private function _deleteAutologin()
    {
        $this->ci->load->helper('cookie');
        if ($cookie = get_cookie(
            $this->ci->config->item('autologin_cookie_name', 'auth'), true
        )) {

            $data = unserialize($cookie);

            $this->ci->load->model('auth/user_autologin');
            $this->ci->userAutologin->delete($data['user_id'], md5($data['key']));

            delete_cookie($this->ci->config->item('autologin_cookie_name', 'auth'));
        }
    }

    /**
     * Login user automatically if he/she provides correct _autologin verification
     *
     * @return void
     */
    private function _autologin()
    {
        if (!$this->isLoggedIn() AND !$this->isLoggedIn(false)) {
            // not logged in (as any user)

            $this->ci->load->helper('cookie');
            if ($cookie = get_cookie(
                $this->ci->config->item('autologin_cookie_name', 'auth'), true
            )) {

                $data = unserialize($cookie);

                if (isset($data['key']) AND isset($data['user_id'])) {

                    $this->ci->load->model('auth/user_autologin');
                    if (!is_null(
                        $user = $this->ci->userAutologin->get(
                            $data['user_id'], md5($data['key'])
                        )
                    )) {

                        // Login user
                        $this->ci->session->set_userdata(
                            array(
                                'user_id'    => $user->id,
                                'username'    => $user->username,
                                'status'    => STATUS_ACTIVATED,
                                'isAdmin'  => $user->is_admin
                            )
                        );

                        // Renew users cookie to prevent it from expiring
                        set_cookie(
                            array(
                                'name' => $this->ci->config->item(
                                    'autologin_cookie_name', 'auth'
                                ),
                                'value' => $cookie,
                                'expire' => $this->ci->config->item(
                                    'autologin_cookie_life', 'auth'
                                ),
                            )
                        );

                        $this->ci->users->updateLoginInfo(
                            $user->id,
                            $this->ci->config->item('login_record_ip', 'auth'),
                            $this->ci->config->item('login_record_time', 'auth')
                        );
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Check if login attempts exceeded max login attempts (specified in config)
     *
     * @param string $login login
     *
     * @return bool
     */
    function isMaxLoginAttemptsExceeded($login)
    {
        if ($this->ci->config->item('login_count_attempts', 'auth')) {
            $this->ci->load->model('auth/login_attempts');
            return $this->ci->login_attempts->getAttemptsNum(
                $this->ci->input->ip_address(), $login
            ) >= $this->ci->config->item('login_max_attempts', 'auth');
        }
        return false;
    }

    /**
     * Increase number of attempts for given IP-address and login
     * (if attempts to login is being counted)
     *
     * @param string $login login
     *
     * @return void
     */
    private function _increaseLoginAttempt($login)
    {
        if ($this->ci->config->item('login_count_attempts', 'auth')) {
            if (!$this->isMaxLoginAttemptsExceeded($login)) {
                $this->ci->load->model('auth/login_attempts');
                $this->ci->login_attempts->increaseAttempt(
                    $this->ci->input->ip_address(), $login
                );
            }
        }
    }

    /**
     * Clear all attempt records for given IP-address and login
     * (if attempts to login is being counted)
     *
     * @param string $login login
     *
     * @return void
     */
    private function _clearLoginAttempts($login)
    {
        if ($this->ci->config->item('login_count_attempts', 'auth')) {
            $this->ci->load->model('auth/login_attempts');
            $this->ci->login_attempts->clearAttempts(
                $this->ci->input->ip_address(),
                $login,
                $this->ci->config->item('login_attempt_expire', 'auth')
            );
        }
    }
}

/* End of file auth.php */
/* Location: ./application/libraries/auth.php */