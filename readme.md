# Ebay Spider
Supports:
* Download the compatibility list using an ebay product page

Usage
```bash
git clone https://github.com/mfeilen/ebayspider
cd ebayspider
composer install
```

In your PHP code use:
```php
require_once 'ebayspider/CompatibilityListFetcher.class.php';

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
```
