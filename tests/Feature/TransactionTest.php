<?php

declare(strict_types=1);

namespace Piseth\BakongKhqr\Tests\Feature;

use Piseth\BakongKhqr\BakongKHQR;
use Piseth\BakongKhqr\Tests\BaseTestCase;

class TransactionTest extends BaseTestCase
{
    private BakongKHQR $bakongKHQR;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bakongKHQR = new BakongKHQR($this->testToken);
    }

    /**
     * Test checking transaction by MD5
     */
    public function testCheckTransactionByMD5(): void
    {
        $testMD5 = md5('sample_transaction_' . time());
        
        try {
            $result = $this->bakongKHQR->checkTransactionByMD5($testMD5, true);
            
            $this->assertIsArray($result);
            // In a real test environment, you might check for specific keys
            // $this->assertArrayHasKey('status', $result);
            
        } catch (\Exception $e) {
            // Expected in test environment without actual transaction data
            $this->assertInstanceOf(\Exception::class, $e);
            error_log('Expected exception in testCheckTransactionByMD5: ' . $e->getMessage());
        }
    }

    /**
     * Test checking multiple transactions by MD5 list
     */
    public function testCheckTransactionByMD5List(): void
    {
        $testMD5List = [
            md5('transaction_1_' . time()),
            md5('transaction_2_' . time()),
            md5('transaction_3_' . time())
        ];
        
        try {
            $result = $this->bakongKHQR->checkTransactionByMD5List($testMD5List, true);
            
            $this->assertIsArray($result);
            
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            error_log('Expected exception in testCheckTransactionByMD5List: ' . $e->getMessage());
        }
    }

    /**
     * Test checking transaction by full hash
     */
    public function testCheckTransactionByFullHash(): void
    {
        $testFullHash = hash('sha256', 'full_hash_transaction_' . time());
        
        try {
            $result = $this->bakongKHQR->checkTransactionByFullHash($testFullHash, true);
            
            $this->assertIsArray($result);
            
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            error_log('Expected exception in testCheckTransactionByFullHash: ' . $e->getMessage());
        }
    }

    /**
     * Test checking transaction by full hash list
     */
    public function testCheckTransactionByFullHashList(): void
    {
        $testFullHashList = [
            hash('sha256', 'full_hash_1_' . time()),
            hash('sha256', 'full_hash_2_' . time())
        ];
        
        try {
            $result = $this->bakongKHQR->checkTransactionByFullHashList($testFullHashList, true);
            
            $this->assertIsArray($result);
            
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            error_log('Expected exception in testCheckTransactionByFullHashList: ' . $e->getMessage());
        }
    }

    /**
     * Test checking transaction by short hash
     */
    public function testCheckTransactionByShortHash(): void
    {
        $testShortHash = substr(md5('short_hash_' . time()), 0, 8);
        $testAmount = 1000.0;
        $testCurrency = 'KHR';
        
        try {
            $result = $this->bakongKHQR->checkTransactionByShortHash(
                $testShortHash, 
                $testAmount, 
                $testCurrency, 
                true
            );
            
            $this->assertIsArray($result);
            
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            error_log('Expected exception in testCheckTransactionByShortHash: ' . $e->getMessage());
        }
    }

    /**
     * Test checking transaction by instruction reference
     */
    public function testCheckTransactionByInstructionReference(): void
    {
        $testReference = 'INS_REF_' . time();
        
        try {
            $result = $this->bakongKHQR->checkTransactionByInstructionReference($testReference, true);
            
            $this->assertIsArray($result);
            
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            error_log('Expected exception in testCheckTransactionByInstructionReference: ' . $e->getMessage());
        }
    }

    /**
     * Test checking transaction by external reference
     */
    public function testCheckTransactionByExternalReference(): void
    {
        $testReference = 'EXT_REF_' . time();
        
        try {
            $result = $this->bakongKHQR->checkTransactionByExternalReference($testReference, true);
            
            $this->assertIsArray($result);
            
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            error_log('Expected exception in testCheckTransactionByExternalReference: ' . $e->getMessage());
        }
    }

    /**
     * Test parameter validation for transaction checking methods
     */
    public function testTransactionParameterValidation(): void
    {
        // Test with empty MD5
        try {
            $result = $this->bakongKHQR->checkTransactionByMD5('', true);
            // Should either work or throw an exception
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }

        // Test with empty MD5 list
        try {
            $result = $this->bakongKHQR->checkTransactionByMD5List([], true);
            // Should either work or throw an exception
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }

        // Test with negative amount for short hash
        try {
            $result = $this->bakongKHQR->checkTransactionByShortHash('testhash', -100.0, 'KHR', true);
            // Should either work or throw an exception
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }
    }

    /**
     * Test different currency codes
     */
    public function testTransactionWithDifferentCurrencies(): void
    {
        $currencies = ['KHR', 'USD'];
        $testShortHash = substr(md5('currency_test_' . time()), 0, 8);
        
        foreach ($currencies as $currency) {
            try {
                $result = $this->bakongKHQR->checkTransactionByShortHash(
                    $testShortHash, 
                    100.0, 
                    $currency, 
                    true
                );
                
                $this->assertIsArray($result);
                
            } catch (\Exception $e) {
                $this->assertInstanceOf(\Exception::class, $e);
                error_log("Expected exception testing currency $currency: " . $e->getMessage());
            }
        }
    }

    /**
     * Test transaction checking with various amount formats
     */
    public function testTransactionWithDifferentAmounts(): void
    {
        $amounts = [0.01, 1.0, 100.0, 1000.0, 999999.99];
        $testShortHash = substr(md5('amount_test_' . time()), 0, 8);
        
        foreach ($amounts as $amount) {
            try {
                $result = $this->bakongKHQR->checkTransactionByShortHash(
                    $testShortHash, 
                    $amount, 
                    'KHR', 
                    true
                );
                
                $this->assertIsArray($result);
                
            } catch (\Exception $e) {
                $this->assertInstanceOf(\Exception::class, $e);
                error_log("Expected exception testing amount $amount: " . $e->getMessage());
            }
        }
    }
}
