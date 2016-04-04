<?php  if (! defined('BASEPATH')) exit('No direct script access allowed');

class Language extends CI_Controller
{
    var $lang_code, $country_code, $default, $accept_languages;
    
    function __construct() {
		parent::__construct();
        
        $this->load->helper('url');
        $this->load->library('user_agent');
		$this->load->library('session');
        
        $this->default = array('lang_code' => 'en', 'country_code' => 'us');
        $this->accept_languages = preg_split('/(\,|\;)/', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
    }
    
    public function language() {
        /******
        lang(2char)-country(2char)
        URL은 국가데이터
        우선순위: Userset > Session > browser
        
        URL 체계
        startupbomb.com/number
        startupbomb.com/countrycode/number
        
        판단로직
        URL에 국가 정보가 없을 경우
            세션이 있는 경우
                세션의 언어코드/국가코드 사용
            세션이 없는 경우
                브라우저 코드 해석
                    세션의 언어코드/국가코드 설정 및 사용
        URL에 국가 정보가 있을 경우
            세션이 있는 경우
                세션의 언어코드만 사용
            세션이 없는 경우
                브라우저 코드 해석
                    세션의 언어코드만 사용
        ******/
        
        $path = $_SERVER['REQUEST_URI'];
        
        if($path == '/' || 
            !$this->is_support_lang(substr($path,1,2)) ||
            !$this->is_support_country(substr($path,4,2))) {
            //country setting
            if($this->session->userdata('lang_code') && $this->session->userdata('country_code')) {
                //세션이 있을 때
            } else {
                //세션이 없을 때
                foreach($this->accept_languages as $al) {
                    $al_split = explode('-', $al);
                    
                    //세션에 저장 되지 않았을 때만 저장
                    if(!$this->session->userdata('lang_code') && $this->is_support_lang($al_split[0])) {
                        $this->session->set_userdata('lang_code', $al_split[0]);
                    }
                    //세션에 저장 되지 않았을 때만 저장
                    if(!$this->session->userdata('country_code') && isset($al_split[1]) && $this->is_support_country($al_split[1])) {
                        $this->session->set_userdata('country_code', $al_split[1]);
                    }
                }
                //반복이 끝나도 없을 때
                if(!$this->session->userdata('lang_code')) {
                    $this->session->set_userdata('lang_code', $this->default['lang_code']);
                }
                if(!$this->session->userdata('country_code')) {
                    $this->session->set_userdata('country_code', $this->default['country_code']);
                }
            }
            redirect('/'.$this->session->userdata('lang_code').'-'.$this->session->userdata('country_code').$path);
        } else {
            //덮어씌우기
            $this->session->set_userdata(
                'lang_code', 
                $this->is_support_lang(substr($path,1,2)) ? substr($path,1,2) : $this->default['lang_code']
            );
            $this->session->set_userdata(
                'country_code', 
                $this->is_support_country(substr($path,4,2)) ? substr($path,4,2) : $this->default['country_code']
            );
            
            $this->config->set_item('language', $this->lang_code2lang($this->session->userdata('lang_code')));
        }
    }
    
    public function is_support_country($str) {
        if(strlen($str) != 2) return FALSE;
        
        $support_country = array(
            'us',
            'kr',
            'jp',
            'cn',
        );
        
        return in_array($str, $support_country);
    }
    
    public function is_support_lang($str) {
        if(strlen($str) != 2) return FALSE;
        
        $support_lang = array(
            'en',
            'ko',
            'jo',
            'zn',
        );
        
        return in_array($str, $support_lang);
    }
    
    public function lang_code2lang($lang_code) {
        $match = array(
            'en' => 'english',
            'ko' => 'korean',
            'jo' => 'japanese',
            'zn' => 'chinese',
        );
        return isset($match[$lang_code]) ? $match[$lang_code] : FALSE;
    }
    
    
}