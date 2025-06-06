<?php

declare(strict_types=1);

namespace Piseth\BakongKhqr\APIs;

use Piseth\BakongKhqr\Configs\Constants;
use Piseth\BakongKhqr\Utils\PostUtils;
use Piseth\BakongKhqr\Utils\StringUtils;

class Transaction
{
    /**
     * @return array<string, mixed>
     */
    public static function checkTransactionByMD5(string $token, string $md5, bool $isTest = false): array
    {
        if (StringUtils::isEmpty($token) || StringUtils::isEmpty($md5)) {
            throw new \InvalidArgumentException('Token and MD5 cannot be blank');
        }

        $url = $isTest ? Constants::SIT_CHECK_TRANSACTION_MD5_URL : Constants::CHECK_TRANSACTION_MD5_URL;

        return PostUtils::post_data_to_url($url, ['md5' => $md5], $token);
    }

    /**
     * @param  array<string>  $md5Array
     * @return array<string, mixed>
     */
    public static function checkTransactionByMD5List(string $token, array $md5Array, bool $isTest = false): array
    {
        if (StringUtils::isEmpty($token) || $md5Array === []) {
            throw new \InvalidArgumentException('Token and MD5 array cannot be blank');
        }

        $url = $isTest ? Constants::SIT_CHECK_TRANSACTION_MD5_LIST_URL : Constants::CHECK_TRANSACTION_MD5_LIST_URL;

        return PostUtils::post_data_to_url($url, $md5Array, $token);
    }

    /**
     * @return array<string, mixed>
     */
    public static function checkTransactionByFullHash(string $token, string $fullHash, bool $isTest = false): array
    {
        if (StringUtils::isEmpty($token) || StringUtils::isEmpty($fullHash)) {
            throw new \InvalidArgumentException('Token and Hash cannot be blank');
        }

        $url = $isTest ? Constants::SIT_CHECK_TRANSACTION_FULL_HASH_URL : Constants::CHECK_TRANSACTION_FULL_HASH_URL;

        return PostUtils::post_data_to_url($url, ['hash' => $fullHash], $token);
    }

    /**
     * @param  array<string>  $fullHashArray
     * @return array<string, mixed>
     */
    public static function checkTransactionByFullHashList(string $token, array $fullHashArray, bool $isTest = false): array
    {
        if (StringUtils::isEmpty($token) || $fullHashArray === []) {
            throw new \InvalidArgumentException('Token and Hash array cannot be blank');
        }

        $url = $isTest ? Constants::SIT_CHECK_TRANSACTION_FULL_HASH_LIST_URL : Constants::CHECK_TRANSACTION_FULL_HASH_LIST_URL;

        return PostUtils::post_data_to_url($url, $fullHashArray, $token);
    }

    /**
     * @return array<string, mixed>
     */
    public static function checkTransactionByShortHash(string $token, string $hash, float $amount, string $currency, bool $isTest = false): array
    {
        if (StringUtils::isEmpty($token) || StringUtils::isEmpty($hash)) {
            throw new \InvalidArgumentException('Token, hash, amount and currency cannot be blank');
        }

        $url = $isTest ? Constants::SIT_CHECK_TRANSACTION_SHORT_HASH_URL : Constants::CHECK_TRANSACTION_SHORT_HASH_URL;

        return PostUtils::post_data_to_url($url, ['hash' => $hash, 'amount' => $amount, 'currency' => $currency], $token);
    }

    /**
     * @return array<string, mixed>
     */
    public static function checkTransactionByInstructionReference(string $token, string $ref, bool $isTest = false): array
    {
        if (StringUtils::isEmpty($token) || StringUtils::isEmpty($ref)) {
            throw new \InvalidArgumentException('Token and Reference cannot be blank');
        }

        $url = $isTest ? Constants::SIT_CHECK_TRANSACTION_INSTRUCTION_REF_URL : Constants::CHECK_TRANSACTION_INSTRUCTION_REF_URL;

        return PostUtils::post_data_to_url($url, ['instructionRef' => $ref], $token);
    }

    /**
     * @return array<string, mixed>
     */
    public static function checkTransactionByExternalReference(string $token, string $ref, bool $isTest = false): array
    {
        if (StringUtils::isEmpty($token) || StringUtils::isEmpty($ref)) {
            throw new \InvalidArgumentException('Token and Reference cannot be blank');
        }

        $url = $isTest ? Constants::SIT_CHECK_TRANSACTION_EXTERNAL_REF_URL : Constants::CHECK_TRANSACTION_EXTERNAL_REF_URL;

        return PostUtils::post_data_to_url($url, ['externalRef' => $ref], $token);
    }
}
