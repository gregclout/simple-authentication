<?php

class SimpleAuthenticate 
{
    private $whitelistedIPs = [];
    private $salt = 'lasjdlkjyalsduylashsdlkauysdlakshd';
    private $questions = [];
    private $formPath = '';

    const INTERNAL_IP_PREFIX = '192.168';
    const USERNAME_PARAM = 'username';
    const SECRET_PARAM = 'secret';
    const QUESTION_INDEX_FORM_PARAM = 'question_index';
    const QUESTION_ANSWER_FORM_PARAM = 'question_answer';
    
    public function __construct($configuration) {
        if (!is_array($configuration)) {
            throw new Exception("Configuration must be set.");
        }

        // Required Fields
        if (empty($configuration['form_path'])) {
            throw new Exception("Path to form must be supplied.");
        }

        if (empty($configuration['questions'])) {
            throw new Exception('At least one question must be posed.');
        }

        foreach ($configuration['questions'] as $question) {
            if (empty($question['question']) || empty($question['answer'])) {
                throw new Exception('All questions must supply an answer');
            }
        }

        $this->questions = $configuration['questions'];
        $this->formPath = $configuration['form_path'];

        // Optional Fields
        if (!empty($configuration['salt'])) {
            $this->salt = $configuration['salt'];
        }

        if (!empty($configuration['whitelisted_ips']) && is_array($configuration['whitelisted_ips'])) {
            $this->whitelistedIPs = $configuration['whitelisted_ips'];
        }

        session_start();
    }
    
    public function Authenticate() {
        // Check whitelist
        if ($this->OriginatedFromWhitelistedIP()) {
            return;
        }

        if ($this->IsAlreadyAuthenticated()) {
            return;
        }

        if ($this->CheckAnswer()) {
            $this->Login();
            return;
        }
		
		include $this->formPath;
        exit;
    }

    public function GetQuestion() {
        $questionIndex = rand(0, count($this->questions)-1);
        return [
            'index' => $questionIndex,
            'question' => $this->questions[$questionIndex]['question']
        ];
    }

    private function OriginatedFromWhitelistedIP() {
        return in_array($_SERVER['REMOTE_ADDR'], $this->whitelistedIPs) || substr($_SERVER['REMOTE_ADDR'], 0, strlen(self::INTERNAL_IP_PREFIX)) === self::INTERNAL_IP_PREFIX;
    }

    private function IsAlreadyAuthenticated() {
        return isset($_SESSION[self::USERNAME_PARAM]) && isset($_SESSION[self::SECRET_PARAM]) && $_SESSION[self::SECRET_PARAM] == $this->GetSecret($_SESSION[self::SECRET_PARAM]);
    }

    private function CheckAnswer() {
        return !empty($_POST) && isset($_POST[self::QUESTION_INDEX_FORM_PARAM]) && isset($_POST[self::QUESTION_ANSWER_FORM_PARAM]) && trim(strtolower($this->questions[$_POST[self::QUESTION_INDEX_FORM_PARAM]]['answer'])) === trim(strtolower($_POST[self::QUESTION_ANSWER_FORM_PARAM]));
    }

    private function GetSecret($username) {
        return md5($_SESSION[self::USERNAME_PARAM] . $this->salt);
    }

    private function Login() {
        $_SESSION[self::USERNAME_PARAM] = $this->GetGUID();
        $_SESSION[self::SECRET_PARAM] =  $this->GetSecret($_SESSION[self::USERNAME_PARAM]);
    }

    private function GetGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }
        else {
            mt_srand((double)microtime()*10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }
}