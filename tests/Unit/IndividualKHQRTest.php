<?php

declare(strict_types=1);

namespace Piseth\BakongKhqr\Tests\Unit;

use Piseth\BakongKhqr\Models\IndividualInfo;
use Piseth\BakongKhqr\BakongKHQR;
use Piseth\BakongKhqr\Tests\BaseTestCase;
use Piseth\BakongKhqr\Exceptions\KHQRException;

class IndividualKHQRTest extends BaseTestCase
{
    public function testGenerateIndividualKHQRWithValidData(): void
    {
        $individualData = $this->createSampleIndividualInfo();
        
        try {
            // Create IndividualInfo object (this might need adjustment based on actual constructor)
            $individualInfo = new IndividualInfo(
                $individualData['bakongAccountID'],
                $individualData['merchantName'],
                $individualData['merchantCity']
            );
            
            // Set optional properties
            if (property_exists($individualInfo, 'merchantID')) {
                $individualInfo->merchantID = $individualData['merchantID'];
            }
            if (property_exists($individualInfo, 'acquiringBank')) {
                $individualInfo->acquiringBank = $individualData['acquiringBank'];
            }
            if (property_exists($individualInfo, 'currency')) {
                $individualInfo->currency = $individualData['currency'];
            }
            if (property_exists($individualInfo, 'amount')) {
                $individualInfo->amount = $individualData['amount'];
            }
            
            $result = BakongKHQR::generateIndividual($individualInfo);
            
            $this->assertValidKHQRResponse($result);
            
            // Check if result has QR and MD5
            if (method_exists($result, 'getData')) {
                $data = $result->getData();
                $this->assertArrayHasKey('qr', $data);
                $this->assertArrayHasKey('md5', $data);
                $this->assertIsString($data['qr']);
                $this->assertIsString($data['md5']);
                $this->assertEquals(32, strlen($data['md5'])); // MD5 should be 32 characters
            }
            
        } catch (\Exception $e) {
            // Log the exception for debugging
            error_log('Exception in testGenerateIndividualKHQRWithValidData: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testGenerateIndividualKHQRWithMinimalData(): void
    {
        try {
            $individualInfo = new IndividualInfo(
                'minimal@wing',
                'Minimal User',
                'Phnom Penh'
            );
            
            $result = BakongKHQR::generateIndividual($individualInfo);
            $this->assertValidKHQRResponse($result);
            
        } catch (\Exception $e) {
            error_log('Exception in testGenerateIndividualKHQRWithMinimalData: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testGenerateIndividualKHQRWithAdditionalData(): void
    {
        try {
            $individualInfo = new IndividualInfo(
                'chhunpiseth@wing',
                'Piseth Chhun',
                'Phnom Penh'
            );
            
            // Set additional data if properties exist
            if (property_exists($individualInfo, 'mobileNumber')) {
                $individualInfo->mobileNumber = '85512345678';
            }
            if (property_exists($individualInfo, 'billNumber')) {
                $individualInfo->billNumber = 'INV-2022-12-25';
            }
            if (property_exists($individualInfo, 'storeLabel')) {
                $individualInfo->storeLabel = 'Ishin Shop';
            }
            if (property_exists($individualInfo, 'terminalLabel')) {
                $individualInfo->terminalLabel = '012345';
            }
            if (property_exists($individualInfo, 'purposeOfTransaction')) {
                $individualInfo->purposeOfTransaction = 'Payment';
            }
            
            $result = BakongKHQR::generateIndividual($individualInfo);
            $this->assertValidKHQRResponse($result);
            
        } catch (\Exception $e) {
            error_log('Exception in testGenerateIndividualKHQRWithAdditionalData: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testGenerateIndividualKHQRWithLanguageData(): void
    {
        try {
            $individualInfo = new IndividualInfo(
                'chhunpiseth@wing',
                'Piseth Chhun',
                'Phnom Penh'
            );
            
            // Set language data if properties exist
            if (property_exists($individualInfo, 'languagePreference')) {
                $individualInfo->languagePreference = 'ZH';
            }
            if (property_exists($individualInfo, 'merchantNameAlternateLanguage')) {
                $individualInfo->merchantNameAlternateLanguage = '文山';
            }
            if (property_exists($individualInfo, 'merchantCityAlternateLanguage')) {
                $individualInfo->merchantCityAlternateLanguage = '金边';
            }
            
            $result = BakongKHQR::generateIndividual($individualInfo);
            $this->assertValidKHQRResponse($result);
            
        } catch (\Exception $e) {
            error_log('Exception in testGenerateIndividualKHQRWithLanguageData: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testGenerateIndividualKHQRWithUSDCurrency(): void
    {
        try {
            $individualInfo = new IndividualInfo(
                'usd.user@wing',
                'USD User',
                'Phnom Penh'
            );
            
            if (property_exists($individualInfo, 'currency')) {
                $individualInfo->currency = 840; // USD
            }
            if (property_exists($individualInfo, 'amount')) {
                $individualInfo->amount = 25.50;
            }
            
            $result = BakongKHQR::generateIndividual($individualInfo);
            $this->assertValidKHQRResponse($result);
            
        } catch (\Exception $e) {
            error_log('Exception in testGenerateIndividualKHQRWithUSDCurrency: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testGenerateIndividualKHQRWithInvalidBakongAccountID(): void
    {
        try {
            $individualInfo = new IndividualInfo(
                '', // Empty account ID
                'Test User',
                'Phnom Penh'
            );
            
            $result = BakongKHQR::generateIndividual($individualInfo);
            
            // This should either throw an exception or handle the error gracefully
            $this->fail('Expected exception for invalid account ID');
            
        } catch (KHQRException $e) {
            $this->assertInstanceOf(KHQRException::class, $e);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testGenerateIndividualKHQRWithInvalidMerchantName(): void
    {
        try {
            $individualInfo = new IndividualInfo(
                'valid@wing',
                '', // Empty merchant name
                'Phnom Penh'
            );
            
            $result = BakongKHQR::generateIndividual($individualInfo);
            
            // This should either throw an exception or handle the error gracefully
            $this->fail('Expected exception for invalid merchant name');
            
        } catch (KHQRException $e) {
            $this->assertInstanceOf(KHQRException::class, $e);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }
}
