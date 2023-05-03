<?php

require_once '../CompatibilityListFetcher.class.php';

try {
    // ebayItemId, ebayCategoryId - both required!
    $itemId = 353849294383;
    $categoryId = 46104;
    $list = CompatibilityListFetcher::Get($itemId, $categoryId);
    print_r($list['head']);
    print_r($list['body']);

} catch (Exception $e) {

    // your error management goes here
    echo $e->getMessage();
    exit;
}
