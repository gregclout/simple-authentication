<?php

namespace SimpleAuthenticate\Test;

use PHPUnit\Framework\TestCase;
use SimpleAuthenticate\Authenticator;
use SimpleAuthenticate\AuthenticationFailedHandler;

final class FullConfigTests extends TestCase
{
    private $configuration;

    protected function setUp(): void 
    {
        $this->configuration = [
            'questions' => [
                [
                    'question' => "Question 1",
                    'answer' => '1'
                ],
                [
                    'question' => 'Question 2',
                    'answer' => '2'
                ],
            ],
            'form_path' => './example/form.php',
            'whitelisted_ips' => [
                '000.000.000.000',
                '000.000.000'
            ],
            'salt' => 'DeoNREf33gpI7xKSa62X',
        ];
    }

    public function testAuthenticatedFalseWhenNotOriginatingFromWhitelistedIpAddress() {
        // Arrange
        $_SERVER['REMOTE_ADDR'] = '192.168.0.1';
        $mockAuthenticationFailedHandler = $this->getMockBuilder(AuthenticationFailedHandler::class)->setMethods(['handle'])->getMock();
        $mockAuthenticationFailedHandler->method('handle')->willReturn(true);

        // Act
        $authenticator = new Authenticator($this->configuration, $mockAuthenticationFailedHandler);

        // Assert
        $this->assertFalse($authenticator->Authenticate());
    }

    public function testAuthenticatedTrueWhenOriginatingFromWhitelistedIpAddress()
    {
        // Arrange
        $_SERVER['REMOTE_ADDR'] = $this->configuration['whitelisted_ips'][0];

        // Act
        $authenticator = new Authenticator($this->configuration);

        // Assert
        $this->assertTrue($authenticator->Authenticate());
    }

    public function testAuthenticatedTrueWhenOriginatingFromPartialWhitelistedIpAddress()
    {
        // Arrange
        $_SERVER['REMOTE_ADDR'] = '000.000.000.123';

        // Act
        $authenticator = new Authenticator($this->configuration);

        // Assert
        $this->assertTrue($authenticator->Authenticate());
    }
}
