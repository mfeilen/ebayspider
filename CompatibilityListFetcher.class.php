<?php

include_once 'vendor/simplehtmldom/simplehtmldom/HtmlWeb.php';
include_once 'RequestBody.class.php';
include_once 'ResponseBody.class.php';

use simplehtmldom\HtmlDocument;

class CompatibilityListFetcher {
    const marketPlaceId = 'EBAY-DE';
    const sellerName = 'globalparts-eu';
    const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/113.0';
    const origin = 'https://www.ebay.de';
    const fetchUrl = 'https://www.ebay.de/g/api/finders?module_groups=PART_FINDER&referrer=VIEWITEM&offset=%s&module=COMPATIBILITY_TABLE';
    const productUrl = 'https://www.ebay.de/itm/%s';
    const compatibilityListSelector = 'div[id=d-motors-compatibility-table]';

    /**
     * @param int $itemId
     * @param int $categoryId
     *
     * @return array
     * @throws \Exception
     */
    public static function get(int $itemId, int $categoryId): array{
        $page = 0;
        $ret = [
            'head' => [],
            'body' => [],
        ];

        try {
            // check if the product has any compatibilityList
            if (!self::isProductWithList($itemId)) {
                return $ret;
            }
            self::spiderNap(); // take a little spider nap :-)

            // gets the list from ebay
            $apiResult = self::getApiResult($page, $itemId, $categoryId);
            if (!isset($apiResult->modules) && $apiResult->modules == null) {
                return [];
            }

            $ret = self::getParsedResult($apiResult); // first round, overwrite

            // check amount of compatibility list pages the product contains
            $maxPages = self::getMaxPages($apiResult); // return int max pages
            if ($maxPages === 1) {
                return $ret;
            }

            for ($i = 1; $i <= $maxPages-1; $i++) { // shift -1 as we multiply it with 20 later, getting the page offset 20, 40...
                self::spiderNap(); // take a little spider nap :-)
                $apiResult = self::getApiResult($i, $itemId, $categoryId);
                $parsedResult = self::getParsedResult($apiResult);
                $ret['body'] = array_merge($ret['body'], $parsedResult['body']); // append to result
            }

        } catch (Exception $e) {
            // throw up - caller must handle
            throw new Exception($e->getMessage());
        }

        return $ret;
    }


    /**
     * Checks if a product has a compatibility list
     * @param int $itemId
     *
     * @return bool
     * @throws \Exception
     */
    private static function isProductWithList(int $itemId): bool {
        $postUrl = sprintf(self::productUrl, $itemId);
        $ch = curl_init($postUrl);

        $headers = array(
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'User-Agent: '.self::userAgent,
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: de,en-US;q=0.7,en;q=0.3',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'TE: trailers',
        );
        $options = array(
            CURLOPT_RETURNTRANSFER => true,                                                        // return web page
            CURLOPT_HEADER         => false,                                                       // don't return headers
            CURLOPT_FOLLOWLOCATION => true,                                                        // follow redirects
            CURLOPT_AUTOREFERER    => true,                                                        // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 10,                                                          // timeout on connect
            CURLOPT_TIMEOUT        => 20,                                                          // timeout on response
            CURLOPT_VERBOSE        => 0,
            CURLOPT_HTTPHEADER     => $headers,
        );

        // request
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // unpack
        $response = gzdecode($response);
        if (empty($response)) {
            throw new Exception(sprintf('Could nod load initial product page from ebay server (http %d), because %s', $curl_errno, $curl_error));
        }

        // is there already a list in the document?
        $html = new HtmlDocument($response);
        $htmlData = $html->find(self::compatibilityListSelector);
        if (is_array($htmlData) && !empty($htmlData)) {
            return true;
        }
        return false;
    }


    /**
     * Sleeps for 0.5 up 2 seconds
     * @return void
     */
    private static function spiderNap(): void {
        $t = microtime(true);
        $sleepTime = (float)(rand(0,1).'.'.rand(500,999)); // 0.5 up to 2 seconds
        $sleepTime *= 1000000;
        usleep($sleepTime);
    }


    /**
     * @param $result
     *
     * @return int
     */
    private static function getMaxPages($apiResult): int {
        $pagesStr = $apiResult->modules->COMPATIBILITY_TABLE->paginatedTable->pagination->itemsPerPage->label->textSpans[0]->text; // Page 1 of ??
        $pagesStr = str_replace('Page ', '', $pagesStr);
        $exploded = explode(' of ', $pagesStr);
        return (int)end($exploded);
    }


    /**
     * @param int $pageNum
     * @param int $itemId
     * @param int $categoryId
     *
     * @return ResponseBody
     * @throws \Exception
     */
    private static function getApiResult(int $pageNum, int $itemId, int $categoryId): ResponseBody {
        $pageNum = $pageNum*20; // 0, 20, 40, 60 ebay offset
        $postUrl = sprintf(self::fetchUrl, $pageNum);
        $ch = curl_init($postUrl);

        $headers = array(
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'User-Agent: '.self::userAgent,
            'Accept: application/json',
            'Accept-Language: de,en-US;q=0.7,en;q=0.3',
            'Accept-Encoding: gzip, deflate, br',
            'Referer: '.sprintf(self::productUrl, $itemId),
            'Origin: '.self::origin,
            'Connection: keep-alive',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'TE: trailers',
        );

        $options = array(
            CURLOPT_RETURNTRANSFER => true,                                                        // return web page
            CURLOPT_HEADER         => false,                                                       // don't return headers
            CURLOPT_FOLLOWLOCATION => false,                                                       // follow redirects
            CURLOPT_AUTOREFERER    => true,                                                        // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 20,                                                          // timeout on connect
            CURLOPT_TIMEOUT        => 20,                                                          // timeout on response
            CURLOPT_POST            => 1,                                                          // sending post data
            CURLOPT_POSTFIELDS     => json_encode(self::getRequestBody($itemId, $categoryId)),     // this are my post vars
            CURLOPT_VERBOSE        => 0,
            CURLOPT_HTTPHEADER     => $headers,
        );

        // request
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // unpack
        $response = gzdecode($response);
        if (!$response) {
            throw new Exception(sprintf('Could nod load data from ebay server (http %d), because %s', $curl_errno, $curl_error));
        }

        // decode data as object
        $ret = json_decode($response);
        if ($ret === false) {
            throw new Exception(sprintf('Could nod load data from ebay server (http %d), because %s', $curl_errno, $curl_error));
        }

        // recast as ResponseBody
        $new = new ResponseBody();
        foreach($ret as $property => &$value)
        {
            $new->$property = &$value;
            unset($ret->$property);
        }
        unset($value);
        unset($object);
        return $new;
    }


    /**
     * @param $itemId
     * @param $categoryId
     *
     * @return \RequestBody
     */
    private static function getRequestBody($itemId, $categoryId) {
        $r = new RequestBody();
        $r->scopedContext = new ScopedContext();
        $r->scopedContext->catalogDetails = new CatalogDetails();
        $r->scopedContext->catalogDetails->itemId = $itemId;
        $r->scopedContext->catalogDetails->categoryId = $categoryId;
        $r->scopedContext->catalogDetails->marketplaceId = self::marketPlaceId;
        $r->scopedContext->catalogDetails->sellerName = self::sellerName;

        return $r;
    }

    /**
     * Parses the api result and returns an array containing head + data
     * @param \ResponseBody $apiResult
     *
     * @return array|array[]
     */
    private static function getParsedResult(ResponseBody $apiResult): array {
        $result = [
            'head'=> [],
            'body' => [],
        ];

        // header
        foreach($apiResult->modules->COMPATIBILITY_TABLE->paginatedTable->header->cells as $element) {
            $result['head'][] = $element->textSpans[0]->text;
        }

        // data
        foreach($apiResult->modules->COMPATIBILITY_TABLE->paginatedTable->rows as $rowData) {
            $row = [];

            // drop first line as it does not contain the data we want
            array_shift($rowData->cells);
            // get cell data
            foreach($rowData->cells as $cell) {
                $row[] = (string)$cell->textSpans[0]->text;
            }

            $result['body'][] = $row;
        }

        return $result;
    }
}

