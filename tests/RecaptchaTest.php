<?php

/*
 * This file is part of reCAPTCHA.
 *
 * (c) Vincent Klaiber <hello@doubledip.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Vinkla\Tests\Recaptcha;

use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use Vinkla\Recaptcha\Recaptcha;
use Vinkla\Recaptcha\RecaptchaException;

/**
 * This is the recaptcha test case class.
 *
 * @author Vincent Klaiber <hello@doubledip.se>
 */
class RecaptchaTest extends TestCase
{
    public function testVerify()
    {
        $recaptcha = $this->getRecaptcha(['success' => true]);

        $response = $recaptcha->verify('my-recaptcha-response');

        $this->assertTrue($response->success);
    }

    public function testInvalidResponse()
    {
        $this->expectException(RecaptchaException::class);

        $recaptcha = $this->getRecaptcha(['success' => false]);

        $recaptcha->verify('my-recaptcha-response');
    }

    public function testInvalidResponseWithErrorCodes()
    {
        $this->expectException(RecaptchaException::class);
        $this->expectExceptionMessage('The secret parameter is missing.');

        $recaptcha = $this->getRecaptcha([
            'success' => false,
            'error-codes' => [
                'missing-input-secret',
            ],
        ]);

        $recaptcha->verify('my-recaptcha-response');
    }

    protected function getRecaptcha($data)
    {
        $client = new Client();

        $response = new Response(200, [], json_encode($data));

        $client->addResponse($response);

        return new Recaptcha('my-secret-key', $client);
    }
}
