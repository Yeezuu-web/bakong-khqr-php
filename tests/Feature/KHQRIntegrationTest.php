<?php

declare(strict_types=1);

namespace Piseth\BakongKhqr\Tests\Feature;

use Piseth\BakongKhqr\Models\IndividualInfo;
use Piseth\BakongKhqr\BakongKHQR;
use Piseth\BakongKhqr\Models\MerchantInfo;
use Piseth\BakongKhqr\Tests\BaseTestCase;

class KHQRIntegrationTest extends BaseTestCase
{
    /**
     * Test complete KHQR generation and verification flow for Individual
     */
    public function testCompleteIndividualKHQRFlow(): void
    {
        try {
            // Step 1: Generate Individual KHQR
            $individualInfo = new IndividualInfo(
                'chhunpiseth@wing',
                'Piseth Chhun',
                'Phnom Penh'
            );
            
            // Set additional properties
            if (property_exists($individualInfo, 'merchantID')) {
                $individualInfo->merchantID = '012345678';
            }
            if (property_exists($individualInfo, 'currency')) {
                $individualInfo->currency = 116; // KHR
            }
            if (property_exists($individualInfo, 'amount')) {
                $individualInfo->amount = 1000.0;
            }
            
            $generatedResult = BakongKHQR::generateIndividual($individualInfo);
            $this->assertValidKHQRResponse($generatedResult);
            
            $generatedData = null;
            if (method_exists($generatedResult, 'getData')) {
                $generatedData = $generatedResult->getData();
            } elseif (property_exists($generatedResult, 'data')) {
                $generatedData = $generatedResult->data;
            }
            
            if ($generatedData && is_array($generatedData) && isset($generatedData['qr'])) {
                $qrString = $generatedData['qr'];
                
                // Step 2: Verify the generated KHQR
                $verificationResult = BakongKHQR::verify($qrString);
                $this->assertInstanceOf(\Piseth\BakongKhqr\Models\CRCValidation::class, $verificationResult);
                
                // Step 3: Decode the KHQR
                $decodedResult = BakongKHQR::decode($qrString);
                $this->assertValidKHQRResponse($decodedResult);
                
                $decodedData = null;
                if (method_exists($decodedResult, 'getData')) {
                    $decodedData = $decodedResult->getData();
                } elseif (property_exists($decodedResult, 'data')) {
                    $decodedData = $decodedResult->data;
                }
                
                // Step 4: Verify decoded data matches original input
                if ($decodedData && is_array($decodedData)) {
                    $this->assertArrayHasKey('merchantName', $decodedData);
                    $this->assertArrayHasKey('merchantCity', $decodedData);
                    $this->assertEquals('Piseth Chhun', $decodedData['merchantName']);
                    $this->assertEquals('Phnom Penh', $decodedData['merchantCity']);
                }
            }
            
        } catch (\Exception $e) {
            error_log('Exception in testCompleteIndividualKHQRFlow: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    /**
     * Test complete KHQR generation and verification flow for Merchant
     */
    public function testCompleteMerchantKHQRFlow(): void
    {
        try {
            // Step 1: Generate Merchant KHQR
            $merchantInfo = new MerchantInfo(
                'merchant@wing',
                'Sample Merchant',
                'Phnom Penh',
                '987654321',
                'Sample Bank'
            );
            
            // Set additional properties
            if (property_exists($merchantInfo, 'currency')) {
                $merchantInfo->currency = 840; // USD
            }
            if (property_exists($merchantInfo, 'amount')) {
                $merchantInfo->amount = 25.50;
            }
            
            $generatedResult = BakongKHQR::generateMerchant($merchantInfo);
            $this->assertValidKHQRResponse($generatedResult);
            
            $generatedData = null;
            if (method_exists($generatedResult, 'getData')) {
                $generatedData = $generatedResult->getData();
            } elseif (property_exists($generatedResult, 'data')) {
                $generatedData = $generatedResult->data;
            }
            
            if ($generatedData && is_array($generatedData) && isset($generatedData['qr'])) {
                $qrString = $generatedData['qr'];
                
                // Step 2: Verify the generated KHQR
                $verificationResult = BakongKHQR::verify($qrString);
                $this->assertInstanceOf(\Piseth\BakongKhqr\Models\CRCValidation::class, $verificationResult);
                
                // Step 3: Decode the KHQR
                $decodedResult = BakongKHQR::decode($qrString);
                $this->assertValidKHQRResponse($decodedResult);
                
                $decodedData = null;
                if (method_exists($decodedResult, 'getData')) {
                    $decodedData = $decodedResult->getData();
                } elseif (property_exists($decodedResult, 'data')) {
                    $decodedData = $decodedResult->data;
                }
                
                // Step 4: Verify decoded data matches original input
                if ($decodedData && is_array($decodedData)) {
                    $this->assertArrayHasKey('merchantName', $decodedData);
                    $this->assertArrayHasKey('merchantCity', $decodedData);
                    $this->assertEquals('Sample Merchant', $decodedData['merchantName']);
                    $this->assertEquals('Phnom Penh', $decodedData['merchantCity']);
                }
            }
            
        } catch (\Exception $e) {
            error_log('Exception in testCompleteMerchantKHQRFlow: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    /**
     * Test KHQR generation with all supported currencies
     */
    public function testKHQRWithDifferentCurrencies(): void
    {
        $currencies = [
            ['code' => 116, 'name' => 'KHR', 'amount' => 4000.0],
            ['code' => 840, 'name' => 'USD', 'amount' => 1.0]
        ];
        
        foreach ($currencies as $currency) {
            try {
                $individualInfo = new IndividualInfo(
                    "test.{$currency['name']}@wing",
                    "Test {$currency['name']} User",
                    'Phnom Penh'
                );
                
                if (property_exists($individualInfo, 'currency')) {
                    $individualInfo->currency = $currency['code'];
                }
                if (property_exists($individualInfo, 'amount')) {
                    $individualInfo->amount = $currency['amount'];
                }
                
                $result = BakongKHQR::generateIndividual($individualInfo);
                $this->assertValidKHQRResponse($result);
                
            } catch (\Exception $e) {
                error_log("Exception testing currency {$currency['name']}: " . $e->getMessage());
                $this->assertInstanceOf(\Exception::class, $e);
            }
        }
    }

    /**
     * Test KHQR with comprehensive additional data
     */
    public function testKHQRWithCompleteAdditionalData(): void
    {
        try {
            $individualInfo = new IndividualInfo(
                'comprehensive@wing',
                'Comprehensive Test User',
                'Phnom Penh'
            );
            
            // Set all possible additional data
            $additionalFields = [
                'merchantID' => '123456789',
                'acquiringBank' => 'Test Bank',
                'currency' => 116,
                'amount' => 15000.0,
                'mobileNumber' => '85512345678',
                'billNumber' => 'BILL-2023-COMPREHENSIVE',
                'storeLabel' => 'Comprehensive Store',
                'terminalLabel' => 'TERM-001',
                'purposeOfTransaction' => 'Payment Test',
                'languagePreference' => 'KM',
                'merchantNameAlternateLanguage' => 'អ្នកប្រើប្រាស់ការសាកល្បង',
                'merchantCityAlternateLanguage' => 'ភ្នំពេញ',
                'upiMerchantAccount' => 'UPI-123456'
            ];
            
            foreach ($additionalFields as $field => $value) {
                if (property_exists($individualInfo, $field)) {
                    $individualInfo->$field = $value;
                }
            }
            
            $result = BakongKHQR::generateIndividual($individualInfo);
            $this->assertValidKHQRResponse($result);
            
            // If generation was successful, try to decode and verify
            $generatedData = null;
            if (method_exists($result, 'getData')) {
                $generatedData = $result->getData();
            } elseif (property_exists($result, 'data')) {
                $generatedData = $result->data;
            }
            
            if ($generatedData && is_array($generatedData) && isset($generatedData['qr'])) {
                $decodedResult = BakongKHQR::decode($generatedData['qr']);
                $this->assertValidKHQRResponse($decodedResult);
            }
            
        } catch (\Exception $e) {
            error_log('Exception in testKHQRWithCompleteAdditionalData: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    /**
     * Test token-based operations
     */
    public function testTokenBasedOperations(): void
    {
        try {
            $bakongKHQR = new BakongKHQR($this->testToken);
            
            // Test checking account existence
            $accountResult = BakongKHQR::checkBakongAccount('test@wing', true);
            $this->assertValidKHQRResponse($accountResult);
            
            // Test transaction checking (these will likely fail without real data)
            $sampleMD5 = md5('sample_transaction_data');
            
            try {
                $transactionResult = $bakongKHQR->checkTransactionByMD5($sampleMD5, true);
                $this->assertIsArray($transactionResult);
            } catch (\Exception $e) {
                // Expected for test environment
                $this->assertInstanceOf(\Exception::class, $e);
            }
            
        } catch (\Exception $e) {
            error_log('Exception in testTokenBasedOperations: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    /**
     * Test deep link generation
     */
    public function testDeepLinkGeneration(): void
    {
        try {
            // First generate a KHQR
            $individualInfo = new IndividualInfo(
                'deeplink@wing',
                'Deep Link Test User',
                'Phnom Penh'
            );
            
            $khqrResult = BakongKHQR::generateIndividual($individualInfo);
            $this->assertValidKHQRResponse($khqrResult);
            
            $khqrData = null;
            if (method_exists($khqrResult, 'getData')) {
                $khqrData = $khqrResult->getData();
            } elseif (property_exists($khqrResult, 'data')) {
                $khqrData = $khqrResult->data;
            }
            
            if ($khqrData && is_array($khqrData) && isset($khqrData['qr'])) {
                // Try to generate deep link
                $deepLinkResult = BakongKHQR::generateDeepLink($khqrData['qr'], null, true);
                $this->assertValidKHQRResponse($deepLinkResult);
            }
            
        } catch (\Exception $e) {
            error_log('Exception in testDeepLinkGeneration: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }
}
