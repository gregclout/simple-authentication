<?php

namespace SimpleAuthenticate;

class AuthenticationFailedHandler 
{
    public function handle($question, $formPath) {
		include $formPath;
        exit;
    }
}