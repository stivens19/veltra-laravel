<?php

namespace Veltra\VeltraLaravel\Tests\Feature;

use Veltra\VeltraLaravel\Services\TwoFactorService;
use Veltra\VeltraLaravel\Tests\TestCase;

class TwoFactorServiceTest extends TestCase
{
    protected TwoFactorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TwoFactorService::class);
    }

    public function test_it_generates_valid_base32_secret(): void
    {
        $secret = $this->service->generateSecretKey(16);

        $this->assertSame(16, strlen($secret));
        $this->assertMatchesRegularExpression('/^[A-Z2-7]{16}$/', $secret);
    }

    public function test_it_verifies_valid_totp_code(): void
    {
        $secret = $this->service->generateSecretKey();
        $code = $this->service->calculateCode($secret);

        $this->assertTrue($this->service->verifyCode($secret, $code));
        $this->assertFalse($this->service->verifyCode($secret, '000000'));
    }

    public function test_it_matches_rfc_test_vectors(): void
    {
        // RFC 6238 standard test vector (ASCII '12345678901234567890' -> Base32 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ')
        // At Unix time 59, expected OTP is 287082
        $rfcSecret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
        $code = $this->service->calculateCode($rfcSecret, 59);

        $this->assertSame('287082', $code);
    }

    public function test_it_generates_and_verifies_recovery_codes(): void
    {
        $codes = $this->service->generateRecoveryCodes(8);

        $this->assertCount(8, $codes);
        $first = $codes[0];

        $this->assertTrue($this->service->verifyRecoveryCode($codes, $first));
        $this->assertCount(7, $codes);
        $this->assertNotContains($first, $codes);

        $this->assertFalse($this->service->verifyRecoveryCode($codes, 'invalid-code'));
    }

    public function test_it_generates_otpauth_and_qr_urls(): void
    {
        $otpauth = $this->service->getOtpauthUrl('VeltraApp', 'user@example.com', 'JBSWY3DPEHPK3PXP');
        $this->assertStringStartsWith('otpauth://totp/', $otpauth);
        $this->assertStringContainsString('secret=JBSWY3DPEHPK3PXP', $otpauth);

        $qrUrl = $this->service->getQrCodeUrl('VeltraApp', 'user@example.com', 'JBSWY3DPEHPK3PXP');
        $this->assertStringContainsString('api.qrserver.com', $qrUrl);
    }
}
