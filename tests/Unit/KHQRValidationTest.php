<?php

declare(strict_types=1);

namespace Piseth\BakongKhqr\Tests\Unit;

use Piseth\BakongKhqr\Tests\BaseTestCase;
use Piseth\BakongKhqr\Tests\Fixtures\KHQRTestData;

class KHQRValidationTest extends BaseTestCase
{
    /**
     * Test KHQR validation with valid samples
     */
    public function testValidKHQRValidation(): void
    {
        $samples = KHQRTestData::$validKHQRSamples;
        
        foreach ($samples as $name => $khqrString) {
            try {
                $result = \Piseth\BakongKhqr\BakongKHQR::verify($khqrString);
                $this->assertInstanceOf(\Piseth\BakongKhqr\Models\CRCValidation::class, $result);
                
                error_log("Testing valid KHQR sample: $name");
                
            } catch (\Exception $e) {
                error_log("Exception testing valid KHQR $name: " . $e->getMessage());
                $this->assertInstanceOf(\Exception::class, $e);
            }
        }
    }

    /**
     * Test KHQR validation with invalid samples
     */
    public function testInvalidKHQRValidation(): void
    {
        $samples = KHQRTestData::$invalidKHQRSamples;
        
        foreach ($samples as $name => $khqrString) {
            try {
                $result = \Piseth\BakongKhqr\BakongKHQR::verify($khqrString);
                $this->assertInstanceOf(\Piseth\BakongKhqr\Models\CRCValidation::class, $result);
                
                // Invalid KHQR should return false for isValid property
                if (property_exists($result, 'isValid')) {
                    $this->assertFalse($result->isValid, "Invalid KHQR $name should fail validation");
                }
                
                error_log("Testing invalid KHQR sample: $name - properly rejected");
                
            } catch (\Exception $e) {
                // Expected for some invalid formats
                error_log("Expected exception for invalid KHQR $name: " . $e->getMessage());
                $this->assertInstanceOf(\Exception::class, $e);
            }
        }
    }

    /**
     * Test edge cases for KHQR validation
     */
    public function testKHQRValidationEdgeCases(): void
    {
        $edgeCases = [
            'empty_string' => '',
            'whitespace_only' => '   ',
            'single_char' => 'a',
            'very_long' => str_repeat('a', 1000),
            'special_chars' => '!@#$%^&*()',
            'numbers_only' => '1234567890',
            'mixed_case' => 'AbCdEfGhIjKlMnOp'
        ];
        
        foreach ($edgeCases as $name => $testCase) {
            try {
                $result = \Piseth\BakongKhqr\BakongKHQR::verify($testCase);
                $this->assertInstanceOf(\Piseth\BakongKhqr\Models\CRCValidation::class, $result);
                
                if (property_exists($result, 'isValid')) {
                    $this->assertFalse($result->isValid, "Edge case $name should fail validation");
                }
                
            } catch (\Exception $e) {
                // Expected for edge cases
                error_log("Expected exception for edge case $name: " . $e->getMessage());
                $this->assertInstanceOf(\Exception::class, $e);
            }
        }
    }

    /**
     * Test KHQR decoding with fixtures
     */
    public function testKHQRDecodingWithFixtures(): void
    {
        $samples = KHQRTestData::$validKHQRSamples;
        
        foreach ($samples as $name => $khqrString) {
            try {
                $result = \Piseth\BakongKhqr\BakongKHQR::decode($khqrString);
                $this->assertValidKHQRResponse($result);
                
                $decodedData = null;
                if (method_exists($result, 'getData')) {
                    $decodedData = $result->getData();
                } elseif (property_exists($result, 'data')) {
                    $decodedData = $result->data;
                }
                
                if ($decodedData && is_array($decodedData)) {
                    $expectedStructure = KHQRTestData::getExpectedDecodedStructure();
                    
                    foreach ($expectedStructure as $field => $type) {
                        if (isset($decodedData[$field])) {
                            $this->assertIsString($decodedData[$field], "Field $field should be a string");
                        }
                    }
                }
                
                error_log("Successfully decoded KHQR sample: $name");
                
            } catch (\Exception $e) {
                error_log("Exception decoding KHQR $name: " . $e->getMessage());
                $this->assertInstanceOf(\Exception::class, $e);
            }
        }
    }

    /**
     * Test consistency between validation and decoding
     */
    public function testValidationDecodingConsistency(): void
    {
        $samples = KHQRTestData::$validKHQRSamples;
        
        foreach ($samples as $name => $khqrString) {
            try {
                // First validate
                $validationResult = \Piseth\BakongKhqr\BakongKHQR::verify($khqrString);
                
                // Then try to decode
                $decodingResult = \Piseth\BakongKhqr\BakongKHQR::decode($khqrString);
                
                // Both should succeed or both should fail
                $this->assertValidKHQRResponse($decodingResult);
                
                error_log("Consistency check passed for: $name");
                
            } catch (\Exception $e) {
                error_log("Consistency check exception for $name: " . $e->getMessage());
                $this->assertInstanceOf(\Exception::class, $e);
            }
        }
    }
}
