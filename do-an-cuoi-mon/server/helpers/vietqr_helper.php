<?php

/**
 * Class VietQR
 *
 * Helper to generate QR code payload for VietQR standard.
 * Based on NAPAS 247 specifications.
 *
 * @see https://vietqr.net/vietqr-api
 */
class VietQR
{
    /**
     * @var string The version of the QR standard.
     */
    private const PAYLOAD_FORMAT_INDICATOR = '000201';

    /**
     * @var string Initiation method (11 for static QR, 12 for dynamic QR).
     */
    private const POINT_OF_INITIATION_METHOD = '010212';

    /**
     * @var string Merchant account information GUID.
     */
    private const GUID = '0010A000000727';

    /**
     * @var string Merchant account information category.
     */
    private const MERCHANT_CATEGORY_CODE = '5303704';

    /**
     * @var string Transaction currency (VND).
     */
    private const TRANSACTION_CURRENCY = '5802VN';

    /**
     * @var string Country code (VN).
     */
    private const COUNTRY_CODE = '62070708';

    /**
     * @var string CRC16 checksum ID.
     */
    private const CRC16_ID = '6304';

    /**
     * Generates the VietQR payload string.
     *
     * @param string $bin The bank's BIN code.
     * @param string $accountNumber The bank account number.
     * @param int $amount The transaction amount.
     * @param string $description The transaction description (e.g., order ID).
     * @return string The formatted VietQR payload string.
     */
    public static function generatePayload(string $bin, string $accountNumber, int $amount, string $description = ''): string
    {
        // 1. Merchant Account Information
        $merchantInfo = self::buildMerchantInfo($bin, $accountNumber);
        $consumerInfo = '38' . self::getValueLength($merchantInfo);
        $payload = $consumerInfo . $merchantInfo;

        // 2. Merchant Category Code (Unused for personal transfers, but required by standard)
        $payload .= self::MERCHANT_CATEGORY_CODE;

        // 3. Transaction Currency
        $payload .= self::TRANSACTION_CURRENCY;
        
        // 4. Transaction Amount
        $payload .= '54' . self::getValueLength($amount) . $amount;

        // 5. Country Code
        $payload .= self::COUNTRY_CODE;
        
        // 6. Additional Data (for transaction description)
        $payload .= '62' . self::buildAdditionalData($description);

        // Prepend static fields
        $fullPayload = self::PAYLOAD_FORMAT_INDICATOR . self::POINT_OF_INITIATION_METHOD . $payload;

        // 7. Calculate and append CRC16 checksum
        $crc16 = self::crc16($fullPayload . self::CRC16_ID);
        $fullPayload .= self::CRC16_ID . $crc16;

        return $fullPayload;
    }

    /**
     * Builds the merchant account information block.
     *
     * @param string $bin
     * @param string $accountNumber
     * @return string
     */
    private static function buildMerchantInfo(string $bin, string $accountNumber): string
    {
        $networkInfo = '01' . self::getValueLength($bin) . $bin;
        $bankInfo = '02' . self::getValueLength($accountNumber) . $accountNumber;
        return self::GUID . $networkInfo . $bankInfo;
    }

    /**
     * Builds the additional data block for the description.
     *
     * @param string $description
     * @return string
     */
    private static function buildAdditionalData(string $description): string
    {
        // Purpose of Transaction (Ma don hang)
        $purpose = '08' . self::getValueLength($description) . $description;
        
        $data = $purpose;
        return self::getValueLength($data) . $data;
    }

    /**
     * Formats the length of a value as a two-digit string.
     *
     * @param mixed $value
     * @return string
     */
    private static function getValueLength($value): string
    {
        return sprintf('%02d', strlen($value));
    }

    /**
     * Calculates CRC16-CCITT-FALSE checksum.
     *
     * @param string $data The data to checksum.
     * @return string The checksum as a 4-character uppercase hex string.
     */
    private static function crc16(string $data): string
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return strtoupper(sprintf('%04X', $crc));
    }
}
