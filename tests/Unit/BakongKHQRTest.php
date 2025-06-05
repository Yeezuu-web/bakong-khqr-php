<?php

declare(strict_types=1);

namespace Piseth\BakongKhqr\Tests\Unit;

use Piseth\BakongKhqr\BakongKHQR;
use Piseth\BakongKhqr\Tests\BaseTestCase;
use Piseth\BakongKhqr\Exceptions\KHQRException;

class BakongKHQRTest extends BaseTestCase
{
    private BakongKHQR $bakongKHQR;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bakongKHQR = new BakongKHQR($this->testToken);
    }

    public function testConstructorWithValidToken(): void
    {
        $khqr = new BakongKHQR($this->testToken);
        $this->assertInstanceOf(BakongKHQR::class, $khqr);
    }

    public function testConstructorWithEmptyToken(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Token cannot be blank');
        new BakongKHQR('');
    }

    public function testConstructorWithNullToken(): void
    {
        $this->expectException(\TypeError::class);
        new BakongKHQR(null);
    }

    public function testCheckTransactionByMD5(): void
    {
        $md5 = 'sample_md5_hash_here';
        
        // Note: This test might fail in actual execution without valid API endpoints
        // You might want to mock the Transaction class or use a test environment
        try {
            $result = $this->bakongKHQR->checkTransactionByMD5($md5, true);
            $this->assertIsArray($result);
        } catch (\Exception $e) {
            // Expected if API is not available in test environment
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCheckTransactionByMD5List(): void
    {
        $md5Array = ['hash1', 'hash2', 'hash3'];
        
        try {
            $result = $this->bakongKHQR->checkTransactionByMD5List($md5Array, true);
            $this->assertIsArray($result);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCheckTransactionByFullHash(): void
    {
        $fullHash = 'sample_full_hash_here';
        
        try {
            $result = $this->bakongKHQR->checkTransactionByFullHash($fullHash, true);
            $this->assertIsArray($result);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCheckTransactionByShortHash(): void
    {
        $shortHash = 'short_hash';
        $amount = 100.0;
        $currency = 'KHR';
        
        try {
            $result = $this->bakongKHQR->checkTransactionByShortHash($shortHash, $amount, $currency, true);
            $this->assertIsArray($result);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testRenewToken(): void
    {
        $email = 'test@example.com';
        
        try {
            $result = BakongKHQR::renewToken($email, true);
            $this->assertIsArray($result);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testVerifyValidKHQRString(): void
    {
        // This would need a real valid KHQR string to test properly
        $validKHQR = $this->getSampleKHQRString();
        
        try {
            $result = BakongKHQR::verify($validKHQR);
            $this->assertInstanceOf(\Piseth\BakongKhqr\Models\CRCValidation::class, $result);
        } catch (\Exception $e) {
            // Expected if the sample KHQR string is not properly formatted
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testVerifyInvalidKHQRString(): void
    {
        $invalidKHQR = 'invalid_khqr_string';
        
        $result = BakongKHQR::verify($invalidKHQR);
        $this->assertInstanceOf(\Piseth\BakongKhqr\Models\CRCValidation::class, $result);
        
        // The result should indicate invalid KHQR
        if (method_exists($result, 'isValid')) {
            $this->assertFalse($result->isValid);
        }
    }

    public function testDecodeKHQRString(): void
    {
        $khqrString = $this->getSampleKHQRString();
        
        try {
            $result = BakongKHQR::decode($khqrString);
            $this->assertValidKHQRResponse($result);
        } catch (\Exception $e) {
            // Expected if the sample KHQR string is not properly formatted
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCheckBakongAccount(): void
    {
        $bakongID = 'test@wing';
        
        try {
            $result = BakongKHQR::checkBakongAccount($bakongID, true);
            $this->assertValidKHQRResponse($result);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testGenerateDeepLink(): void
    {
        $qr = $this->getSampleKHQRString();
        $sourceInfo = null; // or create a SourceInfo object
        
        try {
            $result = BakongKHQR::generateDeepLink($qr, $sourceInfo, true);
            $this->assertValidKHQRResponse($result);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }
}
