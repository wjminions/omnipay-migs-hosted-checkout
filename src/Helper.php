<?php
namespace Omnipay\MigsHostedCheckout;

/**
 * Class Helper
 *
 * @package Omnipay\MigsHostedCheckout
 */
class Helper
{
    public static function parse_from_nvp($string)
    {
        $array     = array();
        if (strlen($string) != 0) {
            $pairArray = explode("&", $string);
            foreach ($pairArray as $pair) {
                $param                       = explode("=", $pair);
                $array[urldecode($param[0])] = urldecode($param[1]);
            }
        }

        return $array;
    }

    // Send transaction to payment server
    public static function SendTransaction($gatewayUrl, $data, $request)
    {
        // initialise cURL object/options
        $ch = curl_init();

        // configure cURL proxy options by calling this function
        // If proxy server is defined, set cURL option
        if ($data['proxyServer'] != "") {
            curl_setopt($ch, CURLOPT_PROXY, $data['proxyServer']);
            curl_setopt($ch, $data['proxyCurlOption'], $data['proxyCurlValue']);
        }

        // If proxy authentication is defined, set cURL option
        if ($data['proxyAuth'] != "")
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $data['proxyAuth']);

        // configure cURL certificate verification settings by calling this function
        // if user has given a path to a certificate bundle, set cURL object to check against them
        if ($data['certificatePath'] != "") {
            curl_setopt($ch, CURLOPT_CAINFO, $data['certificatePath']);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $data['certificateVerifyPeer']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $data['certificateVerifyHost']);

        // [Snippet] howToPost - start
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        // [Snippet] howToPost - end

        // [Snippet] howToSetURL - start
        curl_setopt($ch, CURLOPT_URL, $gatewayUrl);
        // [Snippet] howToSetURL - end

        // [Snippet] howToSetHeaders - start
        // set the content length HTTP header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Length: " . strlen($request)));

        // set the charset to UTF-8 (requirement of payment server)
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));
        // [Snippet] howToSetHeaders - end

        // tells cURL to return the result if successful, of FALSE if the operation failed
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // this is used for debugging only. This would not be used in your integration, as DEBUG should be set to FALSE
        if ($data['debug']) {
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        }

        // [Snippet] executeSendTransaction - start
        // send the transaction
        $response = curl_exec($ch);
        // [Snippet] executeSendTransaction - end

        // this is used for debugging only. This would not be used in your integration, as DEBUG should be set to FALSE
        if ($data['debug']) {
            $requestHeaders = curl_getinfo($ch);
            $response       = $requestHeaders["request_header"] . $response;
        }

        // assigns the cURL error to response if something went wrong so the caller can echo the error
        if (curl_error($ch)) {
            $response = "cURL Error: " . curl_errno($ch) . " - " . curl_error($ch);
        }

        // free cURL resources/session
        curl_close($ch);

        // respond with the transaction result, or a cURL error message if it failed
        return $response;
    }

    // [Snippet] howToConvertFormData - start
    // Form NVP formatted request and append merchantId, apiPassword & apiUsername
    public static function ParseRequest($data, $formData)
    {
        $request = "";

        if (count($formData) == 0)
            return "";

        foreach ($formData as $fieldName => $fieldValue) {
            if (strlen($fieldValue) > 0 && $fieldName != "merchant" && $fieldName != "apiPassword" && $fieldName != "apiUsername") {
                // replace underscores in the fieldnames with decimals
                for ($i = 0; $i < strlen($fieldName); $i++) {
                    if ($fieldName[$i] == '_')
                        $fieldName[$i] = '.';
                }
                $request .= $fieldName . "=" . urlencode($fieldValue) . "&";
            }
        }

        // [Snippet] howToSetCredentials - start
        // For NVP, authentication details are passed in the body as Name-Value-Pairs, just like any other data field
        $request .= "merchant=" . urlencode($data['merchantId']) . "&";
        $request .= "apiPassword=" . urlencode($data['password']) . "&";
        $request .= "apiUsername=" . urlencode($data['apiUsername']);

        // [Snippet] howToSetCredentials - end

        return $request;
    }
    // [Snippet] howToConvertFormData - end
}
