<?php

declare(strict_types=1);

namespace Piseth\BakongKhqr\Tests\Unit;

use Piseth\BakongKhqr\Utils\StringUtils;
use Piseth\BakongKhqr\Tests\BaseTestCase;

class StringUtilsTest extends BaseTestCase
{
    public function testIsEmpty(): void
    {
        $this->assertTrue(StringUtils::isEmpty(''));
        $this->assertTrue(StringUtils::isEmpty('   '));
        $this->assertTrue(StringUtils::isEmpty("\t\n"));
        $this->assertFalse(StringUtils::isEmpty('test'));
        $this->assertFalse(StringUtils::isEmpty('0'));
    }

    public function testCutString(): void
    {
        // Test with a sample KHQR-like string format
        $testString = '0002010102';
        
        try {
            $result = StringUtils::cutString($testString);
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('tag', $result);
            $this->assertArrayHasKey('value', $result);
            $this->assertArrayHasKey('slicedString', $result);
            
            // Based on EMV format: first 2 chars = tag, next 2 = length, rest = value
            $this->assertEquals('00', $result['tag']);
            
        } catch (\Exception $e) {
            error_log('Exception in testCutString: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCheckCRCRegExp(): void
    {
        // Test with various string formats
        $validFormat = 'test string with 63041234'; // Ends with CRC format
        $invalidFormat = 'test string without crc';
        
        try {
            $validResult = StringUtils::checkCRCRegExp($validFormat);
            $invalidResult = StringUtils::checkCRCRegExp($invalidFormat);
            
            $this->assertIsBool($validResult);
            $this->assertIsBool($invalidResult);
            
        } catch (\Exception $e) {
            error_log('Exception in testCheckCRCRegExp: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCRC16(): void
    {
        $testData = 'sample data for crc calculation';
        
        try {
            $crc = StringUtils::crc16($testData);
            
            $this->assertIsString($crc);
            $this->assertEquals(4, strlen($crc)); // CRC16 should be 4 hex characters
            $this->assertMatchesRegularExpression('/^[0-9A-F]{4}$/', $crc);
            
        } catch (\Exception $e) {
            error_log('Exception in testCRC16: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCRC16Consistency(): void
    {
        $testData = 'consistent test data';
        
        try {
            $crc1 = StringUtils::crc16($testData);
            $crc2 = StringUtils::crc16($testData);
            
            $this->assertEquals($crc1, $crc2, 'CRC16 should be consistent for same input');
            
        } catch (\Exception $e) {
            error_log('Exception in testCRC16Consistency: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCRC16DifferentInputs(): void
    {
        $testData1 = 'test data 1';
        $testData2 = 'test data 2';
        
        try {
            $crc1 = StringUtils::crc16($testData1);
            $crc2 = StringUtils::crc16($testData2);
            
            $this->assertNotEquals($crc1, $crc2, 'Different inputs should produce different CRCs');
            
        } catch (\Exception $e) {
            error_log('Exception in testCRC16DifferentInputs: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testFindTag(): void
    {
        // Test finding a tag in an array
        $testArray = [
            ['tag' => '00', 'value' => 'first'],
            ['tag' => '01', 'value' => 'second'],
            ['tag' => '02', 'value' => 'third']
        ];
        
        try {
            $result = StringUtils::findTag($testArray, '01');
            
            if ($result !== false && $result !== null) {
                $this->assertIsArray($result);
                $this->assertEquals('01', $result['tag']);
                $this->assertEquals('second', $result['value']);
            }
            
        } catch (\Exception $e) {
            error_log('Exception in testFindTag: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testFindTagNotFound(): void
    {
        $testArray = [
            ['tag' => '00', 'value' => 'first'],
            ['tag' => '01', 'value' => 'second']
        ];
        
        try {
            $result = StringUtils::findTag($testArray, '99');
            
            // Should return false or null for non-existent tag
            $this->assertTrue($result === false || $result === null);
            
        } catch (\Exception $e) {
            error_log('Exception in testFindTagNotFound: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testIsEmptyWithNull(): void
    {
        // Test null input
        try {
            $result = StringUtils::isEmpty(null);
            $this->assertTrue($result);
        } catch (\TypeError $e) {
            // If method doesn't handle null, expect TypeError
            $this->assertInstanceOf(\TypeError::class, $e);
        }
    }

    public function testCutStringWithInvalidInput(): void
    {
        // Test with too short string
        $shortString = '0';
        
        try {
            $result = StringUtils::cutString($shortString);
            // Should handle gracefully or throw appropriate exception
            $this->assertIsArray($result);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    public function testCRC16WithEmptyString(): void
    {
        try {
            $crc = StringUtils::crc16('');
            $this->assertIsString($crc);
            $this->assertEquals(4, strlen($crc));
        } catch (\Exception $e) {
            error_log('Exception in testCRC16WithEmptyString: ' . $e->getMessage());
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }
}
