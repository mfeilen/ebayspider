<?php

class RequestBody {
    /**
     * @var ScopedContext
     */
    public $scopedContext;

}

class ScopedContext {
    /**
     * @var CatalogDetails
     */
    public $catalogDetails;

}

class CatalogDetails {
    /**
     * @var string
     */
    public $itemId;

    /**
     * @var string
     */
    public $sellerName;

    /**
     * @var string
     */
    public $categoryId;

    /**
     * @var string
     */
    public $marketplaceId;

}
