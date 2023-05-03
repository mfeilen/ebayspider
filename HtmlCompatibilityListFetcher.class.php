<?php

// via autoload
// include_once 'vendor/autoload.php';
// use simplehtmldom\HtmlNode;
// use simplehtmldom\HtmlWeb;

// manual include w/o autoloader
include_once 'vendor/simplehtmldom/simplehtmldom/HtmlWeb.php';
use simplehtmldom\HtmlWeb;

/**
 * HtmlCompatibilityListFetcher class only supports to parse the compatibility list from the first page
 * As the rest is loaded via ajax, this does not support pagination
 */
class HtmlCompatibilityListFetcher {

    const compatibilityListSelector = 'div[id=d-motors-compatibility-table]';
    const compatibilityListHeaderSelector = 'tr[data-testid=ux-table-section-head-row]';
    const compatibilityListDataSelector = 'tr[data-testid=ux-table-section-body-row]';

    /**
     * Returns the compatibility list from an ebay product page
     * @param string $url
     * @param string $selector
     *
     * @return array[]
     * @throws \Exception
     */
    public static function GetCompatibilityList($url, $selector = '') {

        $ret = [
            'head' => [],
            'body' => [],
        ];

        if ($selector === '') {
            $selector = self::compatibilityListSelector;
        }

        // fetch data
        $client = new HtmlWeb();
        $html = $client->load($url);
        if ($html === null) {
            throw new Exception(sprintf("could not download html data from url '%s'", $url));
        }

        // prepare result
        $htmlData = $html->find($selector);
        if (!is_array($htmlData)) {
            throw new Exception(sprintf("no html data found using url '%s'", $url));
        }
        $ret['head'] = self::getCompatibilityListHeader($htmlData);
        $ret['body'] = self::getCompatibilityListData($htmlData);

        return $ret;
    }

    /**
     * Returns the header text array of strings
     * @param HtmlNode[] $htmlData
     *
     * @return string[]
     */
    private static function getCompatibilityListHeader($htmlData) {
        $ret = [];

        /**
         * @var $rows HtmlNode[]
         */
        $rows = $htmlData[0]->find(self::compatibilityListHeaderSelector);
        if (!is_array($rows)) {
            return $ret;
        }
        foreach($rows as $row) {
            if (!isset($row->nodes)) {
                continue;
            }
            foreach($row->nodes as $node) {
                $txt = $node->text();
                if (empty($txt)) {
                    continue;
                }
                $ret[] = $txt;
            }
        }
        return $ret;
    }

    /**
     * Returns the list data as 2-dim-array
     * @param HtmlNode[]$htmlData
     *
     * @return string[]
     */
    private static function getCompatibilityListData($htmlData) {
        $ret = [];

        /**
         * @var $tbody HtmlNode[]
         */
        $tbody = $htmlData[0]->find('tbody');
        if (!is_array($tbody)) {
            return $ret;
        }

        /**
         * @var $rows HtmlNode[]
         */
        $rows = $tbody[0]->find(self::compatibilityListDataSelector);
        if (!is_array($rows)) {
            return $ret;
        }
        foreach($rows as $row) {
            if (!isset($row->nodes)) {
                continue;
            }
            $rowData = [];
            foreach($row->nodes as $node) {
                $txt = $node->text();
                if (empty($txt)) {
                    continue;
                }
                $rowData[] = $txt;
            }
            $ret[] = $rowData;
        }

        return $ret;
    }
}
