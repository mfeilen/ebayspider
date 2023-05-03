<?php

require_once '../EbaySpider.class.php';

try {
    // only supports to read the first page of a compatibility list
    $list = HtmlCompatibilityListFetcher::GetCompatibilityList('https://some/ebay/product');
    print_r($list['head']);
    print_r($list['body']);

} catch (Exception $e) {

    echo $e->getMessage();
}
