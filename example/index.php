<?php
    require 'src/SimpleAuthenticate.php';
    
    $authenticator = new Authenticator([
        
        'questions' => [
            [
                'question' => "What colour is the sky?",
                'answer' => 'blue'
            ],
            [
                'question' => 'who is the lead actor in "The Mask (1985)',
                'answer' => 'jim carrey'
            ],
        ],
        'form_path' => 'form.php',
        'whitelisted_ips' => [
            '000.000.000.000',
            '192.168.20',
        ],
        'salt' => 'DeoNREf33gpI7xKSa62X',
    ]);
    $authenticator->Authenticate();
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>HelloWorld</title>
    </head>
    <body>
        <p>Hello world.</p>
    </body>
</html>