<?
if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../../..');
}

class CatalogAppAgents {

    public static function CreateWorker () {
        require($_SERVER["DOCUMENT_ROOT"]."/local/modules/imarket_catalog_app/workers/CreateWorker.php");
        //return "CatalogAppAgents::CreateWorker();";
    }

    public static function UpdateWorker () {
        require($_SERVER["DOCUMENT_ROOT"]."/local/modules/imarket_catalog_app/workers/UpdateWorker.php");
        //return "CatalogAppAgents::UpdateWorker();";
        return "CatalogAppAgents::UpdateWorker();";
    }

    public static function UpdatePropertyWorker () {
        require($_SERVER["DOCUMENT_ROOT"]."/local/modules/imarket_catalog_app/workers/UpdatePropertiesWorker.php");
        //return "CatalogAppAgents::UpdatePropertyWorker();";
    }

    public static function WorkersChecker () {
        require($_SERVER["DOCUMENT_ROOT"]."/local/modules/imarket_catalog_app/classes/checkWorkers.php");
        //return "CatalogAppAgents::WorkersChecker();";
    }
}

?>
