<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::registerAutoLoadClasses(
    "imarket_catalog_app",
    array(
        "CatalogAppAgents" => "classes/CatalogAppAgents.php",
    ));
?>