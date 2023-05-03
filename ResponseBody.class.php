<?php

class ResponseBody {
    /**
     * @var Modules
     */
    public $modules;
}


class Modules {
    /**
     * @var COMPATIBILITY_TABLE
     */
    public $COMPATIBILITY_TABLE;

}

class COMPATIBILITY_TABLE {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var PaginatedTable
     */
    public $paginatedTable;
    /**
     * @var Title
     */
    public $title;
}

class TextSpans {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var string
     */
    public $text;
    /**
     * @var CellsAction
     */
    public $action;
    /**
     * @var string
     */
    public $accessibilityText;
}

class Title {
    /**
     * @var string
     */
    public $_type;

    /**
     * @var TextSpans[]
     */
    public $textSpans;

}

class HeaderCells {
    /**
     * @var string
     */
    public $_type; //String
    /**
     * @var TextSpans[]
     */
    public $textSpans;

}
class Header {
    /**
     * @var string
     */
    public $_type;

    /**
     * @var HeaderCells[]
     */
    public $cells;

}

class Caption {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var TextSpans[]
     */
    public $textSpans;

}

class TextualDisplays {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var TextSpans[]
     */
    public $textSpans;

}
class ClientPresentationMetadata {
    public $presentationType; //String
}


class ViewMore {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var TextSpans[]
     */
    public $textSpans;
}


class ViewLess {
    public $_type; //String
    /**
     * @var TextSpans[]
     */
    public $textSpans;

}
class ExpandCollapseControls {
    /**
     * @var string
     */
    public $_type;
    public $viewMore; //ViewMore
    public $viewLess; //ViewLess
}

class RowCells {
    /**
     * @var string
     */
    public $_type;

    /**
     * @var TextualDisplays[]
     */
    public $textualDisplays;
    /**
     * @var \ExpandCollapseControls
     */
    public $expandCollapseControls;
    /**
     * @var string
     */
    public $uxComponentHint;

}
class Rows {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var RowCells[]
     */
    public $cells;
}

class Params {
    /**
     * @var string
     */
    public $marketplaceId;
    /**
     * @var string
     */
    public $itemId;
    /**
     * @var string
     */
    public $referrer;
    /**
     * @var string
     */
    public $offset;
    /**
     * @var string
     */
    public $module;
    /**
     * @var string
     */
    public $sellerName;
    /**
     * @var string
     */
    public $module_groups;
    /**
     * @var string
     */
    public $categoryId;
}

class CellsAction {
    public $_type; //String
    public $type; //String
    public $clientPresentationMetadata; //ClientPresentationMetadata
}

class PaginationAction {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var string
     */
    public $type;
    public $params; //Params
    public $trackingList;  //array( undefined )
    /**
     * @var bool
     */
    public $signInRequired;
    /**
     * @var string
     */
    public $URL; //String
}
class Previous {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var PaginationAction
     */
    public $action;

    /**
     * @var string
     */
    public $accessibilityText;

}
class Next {
    /**
     * @var string
     */
    public $_type;
    public $accessibilityText; //String

}

class Pages {
    public $_type; //String
    public $text; //String
    public $action; //Action
    public $selected; //boolean

}

class Label {
    /**
     * @var string
     */
    public $_type;
    /**
     * @var TextSpans[]
     */
    public $textSpans;
}

class ItemsPerPage {
    /**
     * @var Label
     */
    public $label;
    /**
     * @var string
     */
    public $currentOption;
    /**
     * @var object
     */
    public $options;
}

class Pagination {
    /**
     * @var Previous
     */
    public $previous;

    /**
     * @var Next
     */
    public $next;

    /**
     * @var Pages[]
     */
    public $pages;

    /**
     * @var ItemsPerPage
     */
    public $itemsPerPage;

}

class PaginatedTable {
    /**
     * @var String
     */
    public $_type;
    /**
     * @var Title
     */
    public $title;
    /**
     * @var Header
     */
    public $header;
    /**
     * @var Caption
     */
    public $caption;
    /**
     * @var Rows[]
     */
    public $rows;
    /**
     * @var Pagination
     */
    public $pagination;
}
