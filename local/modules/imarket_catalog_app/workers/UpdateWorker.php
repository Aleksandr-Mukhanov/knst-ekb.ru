<?
/**
 * обработчик для создания новых товаров, создает болванки товаров,
 * что бы парсера заполняли нужные поля, цены, доступность и прочее обновлвяется в обработчике UpdateWorker
 *
 * Запуск на кроне раз в час
 */

if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../../../..');
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use ImarketHeplers\ImarketLogger,
    Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use \Bitrix\Catalog\Model\Product;
use \Bitrix\Catalog\Model\Price;

if (!class_exists("ImarketHeplers\ImarketLogger")) {
    require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/ImarketLogger.php');
}

require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/RestAPI.php');
require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/ImarketSectionsConnections.php');
require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/workers/WorkersChecker.php');
ini_set('memory_limit', '25600M'); // много кушает оперативки!!!!
set_time_limit(0);


class UpdateWorker {
    private $Logger; // класс для логирования
    private $connection; // подключение к БД
    private $restAPI; // класс api rest-а
    public $debugData = []; // данные для дебага
    private $workersChecker; // класс для работы с обработчиками
    private $workerData = []; // данные об обработчиках
    private $workerId = 'update'; // id обработчика в табице worker_busy
    private $arModels = []; // товары из catalogApp
    private $arCatalogDataByXML_ID = []; // весь каталог сайта, где ключ xml_id элемента
    private $arCatalogDataById = []; // весь каталог сайта, где ключ id элемента
    private $arCatalogsDiff = []; // массив с товарами, которые необходимо добавить в каталог сайта
    private $arVendorsIds = []; // массив id брендов в catalogApp, которые нужно получить, формируются в CompareCatalogs
    private $arCategoriesIds = []; // массив id разделов в catalogApp, которые нужно получить, формируются в CompareCatalogs
    private $arVendors = []; // массив брендов из catalogApp
    private $arSiteVendors = []; // массив брендов сайта
    private $arSiteVendorsByXmlId = []; // массив брендов сайта по ключу xml_id
    private $arCategories = []; // массив разделов из catalogApp
    private $arSiteCategories = []; // массив разделов сайта
    private $arSiteCategoriesByXmlId = []; // массив разделов сайта, ключ внешний код
    private $sectionConnections = []; // сопоставление разделов catalogApp и сайта, ключ - id catalogApp
    private $catalogIblockId = 0;
    private $endSections = [];
    private $arCASectionProperties = []; // свойства из catalog.app по разделам
    private $arCASiteProperties = []; // массив с созданными свойствами только для catalog.app
    private $arCASettings = [];
    private $arSuppliers=[];

    private $arCAProperties = [
        "CA_ID" => [
            "NAME" => "ID в catalog.app",
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "CA_ID",
            "PROPERTY_TYPE" => "N",
            "IBLOCK_ID" => 0
        ],
        "CA_SUPPLIER" => [
            "NAME" => "Поставщик",
            "ACTIVE" => "N",
            "SORT" => "100",
            "CODE" => "CA_SUPPLIER",
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => 0
        ],
        "CA_BRAND" => [
            "NAME" => "Производитель",
            "ACTIVE" => "Y",
            "SORT" => "110",
            "CODE" => "CA_BRAND",
            "PROPERTY_TYPE" => "L",
            "IBLOCK_ID" => 0
        ],
        "CA_MORE_PHOTO" => [
            "NAME" => "Доп. Изображения",
            "ACTIVE" => "Y",
            "SORT" => "120",
            "CODE" => "CA_MORE_PHOTO",
            "PROPERTY_TYPE" => "F",
            "IBLOCK_ID" => 0,
            "MULTIPLE" => "Y",
        ],
        "CA_AUTO_ACTIVATE" => [
            "NAME" => "Автоматическая активация",
            "ACTIVE" => "Y",
            "SORT" => "130",
            "CODE" => "CA_AUTO_ACTIVATE",
            "PROPERTY_TYPE" => "N",
            "IBLOCK_ID" => 0,
            "MULTIPLE" => "Y",
        ]
    ];
    private $isPriceFirst=false;

    public function __construct () {
        $this->Logger = new ImarketLogger("/upload/log/UpdateWorker/");
        $this->connection = Application::getConnection();
        $last_update=Option::get('imarket_catalog_app', "last_update",0);
        
        
        $this->restAPI = new RestAPI();
        $this->workersChecker = new WorkersChecker();
        CModule::IncludeModule("iblock");


        $sql = "SELECT * FROM catalog_app_settings";
        $res = $this->connection->query($sql);
        $this->arCASettings = $res->fetch();
        $this->arCASettings["AUTO_UPDATE_RULES"] = unserialize($this->arCASettings["AUTO_UPDATE_RULES"]);

        if (!empty($this->arCASettings["CATALOG_APP_CATALOG_ID"])) {
            $this->catalogIblockId = $this->arCASettings["CATALOG_IBLOCK_ID"];
        }
        if(!$last_update) {
            $sql = "SELECT MIN(DATE_CREATE) as last_update FROM b_iblock_element where iblock_id={$this->catalogIblockId}";
            print_r($sql.PHP_EOL);
            $luRes = $this->connection->query($sql);
            if ($res = $luRes->Fetch()) {
                $last_update=date('Y-m-d',strtotime($res['last_update']));
                print_r('Last update: '.$last_update.PHP_EOL);
                Option::set('imarket_catalog_app', "last_update",$last_update);
            }
        }

    }

    /**
     * Старт работы обработчика
     *
     * @return bool
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    public function StartWorker() {
        $this->Logger->log("LOG", "Начало обработки");
        $this->StartDebugTime(__FUNCTION__);

        // проверить статус обработчика
        if (!$this->CheckStatus()) {
            $this->Logger->log("LOG", "Не нужно обрабатывать");
            return false;
        }

        // проставить статус
        $this->UpdateStatus(1);
        // получить каталог из catalogApp
        
        // получить товары сайта
        //$this->GetCatalogGoods();
        // получить разделы из catalogApp
        $this->GetCategories();
        // получить разницу разделов
        $this->CheckSectionsDiff();
       
        $this->LoadAppCategoryProperties();
        $this->GetSuppliers();
        // проверить, есть ли основные свойства
        $this->PrepareMainProperties();
        // получить товары, которые нужно создать
        $this->CompareCatalogs();

//        if (!empty($this->arCatalogsDiff)) {
//            pr(count($this->arCatalogsDiff), true);

            // получить разделы сайта
            $this->GetSiteCategories();
            // получить сопоставление разделов
            $this->GetSectionsConnections();
            // создать новые товары
        //    $this->CreateDiffGoods();
      //  print_r('AddGoods'.PHP_EOL);
            $only_add=false;

            $this->AddGoods($only_add);
        //    $offset=Option::set('imarket_catalog_app', "models_offset[GetLimitPriceModels]",0);
   
  
            //$offset=Option::get('imarket_catalog_app', "models_offset[GetLimitPriceModels]",0);

            //print_r("Offset: {$offset}".PHP_EOL);

          //      } else {
        
    //        $this->Logger->log("LOG", "Нет товаров для добавления");
      //  }

        // обновить статус
        $this->UpdateStatus(0);

        

        $this->EndDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Обработка закончена",$this->debugData);
    }

    private function PrepareMainProperties() {
        $this->Logger->log("LOG", "Проверям созданы ли основные свойства");
        $this->StartDebugTime(__FUNCTION__);

        foreach ($this->arCAProperties as $code => $arItem) {
            $arItem["IBLOCK_ID"] = $this->catalogIblockId;
            $properties = CIBlockProperty::GetList(["name" => "asc"], ["IBLOCK_ID" => $this->catalogIblockId, "CODE" => $code]);
            if(!$arProp = $properties->GetNext()) {
                $this->CreateProperty($arItem);
            } else {
                $this->arCASiteProperties[$arProp["CODE"]] = $arProp;
            }
        }
        $properties = CIBlockProperty::GetList(["id" => "asc"], ["IBLOCK_ID" => $this->catalogIblockId]);
        while($arProp = $properties->GetNext()) {
            if(!$this->arCASiteProperties[$arProp["CODE"]]) {
                $this->arCASiteProperties[$arProp["CODE"]] = $arProp;
            }
        }
        print_r('Всего свойств в базе: '.count($this->arCASiteProperties).PHP_EOL);
        $this->EndDebugTime(__FUNCTION__);
    }
    /**
     * получить все модели из catalogApp
     */
    public function GetCatalogModels() {
        $this->Logger->log("LOG", "Получение моделей каталога");
        $this->StartDebugTime(__FUNCTION__);

         if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/upload/log/UpdatePropertiesWorker/debugData_.txt")) {
            $this->arModels = $this->restAPI->GetModels();
            $this->arModels = array_reverse($this->arModels, true);
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/upload/log/UpdatePropertiesWorker/debugData_.txt", json_encode($this->arModels,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        } else {
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/upload/log/UpdatePropertiesWorker/debugData_.txt");
            $this->arModels = json_decode($file,true);
        }
        /*$this->arModels = $this->restAPI->GetModels();
        $this->arModels = array_reverse($this->arModels, true);
        file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/log/CreateWorker/debugData_.txt", serialize($this->arModels));
*/ print_r("Всего получено моделей ".count($this->arModels));
        $this->Logger->log("LOG", "Всего получено моделей ".count($this->arModels));

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * Получаем все товары каталога, ключ xml_id
     *
     */
    public function GetCatalogGoods() {
        $this->Logger->log("LOG", "Получаем товары каталога");
        $this->StartDebugTime(__FUNCTION__);

        $filter = ['IBLOCK_ID' => $this->catalogIblockId];
        $select = ['ID', 'NAME', 'XML_ID'];
        $dbl = CIBlockElement::GetList(["ID" => "ASC"], $filter, false, false, $select);
        while ($arItem = $dbl->Fetch()) {
            if(!empty($arItem["XML_ID"]) && $arItem["XML_ID"] != $arItem["ID"]) {
                $this->arCatalogDataByXML_ID[$arItem["XML_ID"]] = $arItem;
            }

            $this->arCatalogDataById[$arItem["ID"]] = $arItem;
        }

        $this->Logger->log("LOG", "Получено, всего товаров: ".count($this->arCatalogDataByXML_ID));
        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * получить товары, которые нужно добавить
     */
    public function CompareCatalogs() {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Сравниваем каталог catalogApp с каталогом сайта");

        foreach ($this->arModels as $k => $arItem) {
            // товара нет в каталоге сайта
            if (empty($this->arCatalogDataByXML_ID[$arItem["externalId"]])/*&& ($arItem["category"]["id"] == 83 || $arItem["category"]["id"] == 2312)*/) { // TODO убрать разделы !!!
                $this->arCatalogsDiff[$arItem["externalId"]] = $arItem;

                if (!in_array($arItem["vendor"]["id"], $this->arVendorsIds)) {
                    $this->arVendorsIds[] = $arItem["vendor"]["id"];
                }

                if (!in_array($arItem["category"]["id"], $this->arCategoriesIds)) {
                    $this->arCategoriesIds[] = $arItem["category"]["id"];
                }
            }
        }

        $this->Logger->log("LOG", "Нужно добавить ".count($this->arCatalogsDiff)." товаров");
        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * Получить бренды из catalogApp
     */
    public function GetVendors() {
        $this->Logger->log("LOG", "Получаем бредны товаров из catalogApp");
        $this->StartDebugTime(__FUNCTION__);

        $arVendors = $this->restAPI->GetVendors();

        foreach ($arVendors as $arVendor) {
            $this->arVendors[$arVendor["id"]] = $arVendor;
        }

        $this->Logger->log("LOG", "Всего брендов ".count($this->arVendors));

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * получить бренды сайта
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function GetSiteVendorList () {
        $this->Logger->log("LOG", "Получение всех брендов на сайте");
        $this->StartDebugTime(__FUNCTION__);
        $sql = 'SELECT ID, VALUE, XML_ID FROM b_iblock_property_enum WHERE PROPERTY_ID = 57893';
        $dbl = $this->connection->query($sql);
        while ($arItem = $dbl->fetch()) {
            $arItem["LOVER_NAME"] = strtolower($arItem["VALUE"]);
            $this->arSiteVendors[$arItem["LOVER_NAME"]] = $arItem;
            $this->arSiteVendorsByXmlId[$arItem["XML_ID"]] = $arItem;
        }

        $this->Logger->log("LOG", "Всего получено ".count($this->arSiteVendors));
        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * получить разделы из catalogApp
     */
    public function GetCategories() {
        $this->Logger->log("LOG", "Получаем разделы товаров из catalogApp");
        $this->StartDebugTime(__FUNCTION__);

        $arCategories = $this->restAPI->GetCategories();

        foreach ($arCategories as $arCategory) {
            $this->arCategories[$arCategory["id"]] = $arCategory;
        }

        $this->Logger->log("LOG", "Всего разделов ".count($this->arCategories));

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * получить разделы сайта
     */
    public function GetSiteCategories() {
        $this->Logger->log("LOG", "Получаем разделы сайта");
        $this->StartDebugTime(__FUNCTION__);

        $this->arSiteCategories = [];
        $this->arSiteCategoriesByXmlId = [];

        $arFilter = array('IBLOCK_ID' => $this->catalogIblockId);
        $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter, false, ["ID", "NAME", "XML_ID", "IBLOCK_SECTION_ID"]);
        while ($arSect = $rsSect->GetNext()) {
            $this->arSiteCategories[$arSect["ID"]] = $arSect;
            $this->arSiteCategoriesByXmlId[$arSect["XML_ID"]] = $arSect;
            
        }

        $this->Logger->log("LOG", "Всего получено ".count($this->arSiteCategories));

        $this->EndDebugTime(__FUNCTION__);
    }

    private function CheckSectionsDiff () {
        $this->Logger->log("LOG", "Проверка разделов");
        $this->StartDebugTime(__FUNCTION__);

        $this->GetSiteCategories();
        $counter = 0;
        $arTree = [];

        if (!empty($this->arCategories)) {
            foreach ($this->arCategories as $id => $arItem) {

                if (empty($this->arSiteCategoriesByXmlId[$id])) {
                    $this->CreateSection($id);
                    $counter++;
                }

                if (empty($arItem["parentId"])) {
                    $arTree[$id] = $this->GetSubSections($id);
                }
            }

            foreach ($this->arCategories as $id => $arItem) {
                if (!empty($this->arSiteCategoriesByXmlId[$id])) {
                    if (!empty($arItem["parentId"])) {
                        $this->UpdateParensSection($id, $arItem["parentId"]);
                    }
                }
            }

            if (count($this->arCategories) < count($this->arSiteCategoriesByXmlId)) {
                foreach ($this->arSiteCategoriesByXmlId as $id => $arItem) {
                    if (empty($this->arCategories[$id])) {
                        $this->Logger->log("LOG", "Нужно удалить раздел ".$arItem["NAME"]);
                        $this->DeleteSiteSection($id);
                    }
                }
            }

            $this->UpdateSectionsConnections();
        }

//        pr("OK", true);

        $this->Logger->log("LOG", "Добавлено разделов ".$counter);
        $this->EndDebugTime(__FUNCTION__);
    }

    private function UpdateParensSection ($sectionId = 0, $parentSectionId = 0) {
        
        $this->StartDebugTime(__FUNCTION__);

        if (empty($sectionId) || empty($parentSectionId)) {
            $this->Logger->log("LOG", "Не указан id раздела!");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        
        $siteSectionId = $this->arSiteCategoriesByXmlId[$sectionId]["ID"];
        $old=$this->arSiteCategoriesByXmlId[$sectionId]['IBLOCK_SECTION_ID'];
        $siteParentSectionId = $this->arSiteCategoriesByXmlId[$parentSectionId]["ID"] ? $this->arSiteCategoriesByXmlId[$parentSectionId]["ID"] : 0;
        
        if($old!=$siteParentSectionId) {
            $this->Logger->log("LOG", "Обновляем привязку раздела ".$this->arCategories[$sectionId]["name"]. " к ".$this->arCategories[$parentSectionId]["name"]);
            $this->Logger->log("LOG", "Старая привязка:{$old} Новая привязка:{$siteParentSectionId}");
            $bs = new CIBlockSection;
            $arFields = Array("IBLOCK_SECTION_ID" => $siteParentSectionId);
            $bs->Update($siteSectionId, $arFields);

            $this->Logger->log("LOG", "Привязка раздела обновлена");
        }
        $this->EndDebugTime(__FUNCTION__);
    }

    private function GetSubSections ($sectionId = 0) {
        $this->Logger->log("LOG", "Получаем подразделы для раздела ".$this->arCategories[$sectionId]["name"]."[$sectionId]");
        $this->StartDebugTime(__FUNCTION__);

        if (empty($sectionId)) {
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        $arSubs = [];
        $arParentIds = [];
        foreach ($this->arCategories as $id => $arItem) {
            if (!in_array($arItem["parentId"], $arParentIds)) {
                $arParentIds[] = $arItem["parentId"];
            }

            if ($arItem["parentId"] == $sectionId) {
                $children = $this->GetSubSections($id);
                if ($children) {
                    $arItem['subsections'] = $children;
                }
                //else {
                 //   $this->endSections[] = $arItem;
               // }

                $arSubs[$id] = $arItem;
            }
        }

        foreach ($this->arCategories as $id => $arItem) {
            //if (!in_array($arItem["id"], $arParentIds)) {
//                $this->endSections[$arItem['id']] = $arItem;
            ///}
        }

        $this->EndDebugTime(__FUNCTION__);

        return $arSubs;
    }

    private function CreateSection ($sectionId = 0) {
        $this->Logger->log("LOG", "Создаем раздел на сайте ".$this->arCategories[$sectionId]["name"]);
        $this->StartDebugTime(__FUNCTION__);
        $result = false;

        if (empty($sectionId)) {
            $this->Logger->log("LOG", "Нет id раздела");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        $bs = new CIBlockSection;

        $arSection = $this->arCategories[$sectionId];
        $parentSectionId = 0;

        if(!empty($arSection["parentId"])) {
            $parentSection = $this->arSiteCategoriesByXmlId[$arSection["parentId"]];
            $parentSectionId = $parentSection['IBLOCK_SECTION_ID'];
        }

        $image = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/local/modules/imarket_catalog_app/img/no-photo.png");
        $code = translit($arSection["name"]);
        $code = strtolower($code);
        $arFields = Array(
            "ACTIVE"            => "Y",
            "IBLOCK_SECTION_ID" => $parentSectionId,
            "IBLOCK_ID"         => $this->catalogIblockId,
            "NAME"              => $arSection["name"],
            "SORT"              => $arSection["id"],
            "XML_ID"            => $arSection["id"],
            "PICTURE"           => $image,
            "CODE"              => $code."_".$arSection["id"]
        );

        if (!$bs->Add($arFields)) {
            $this->Logger->log("ERROR", "Ошибка при добавлении раздела ".$arSection["name"]."\r\n".$bs->LAST_ERROR."\r\n".print_r($arFields, true));
        } else {
            $result = true;
            $this->Logger->log("LOG", "Добалвен раздел ".$arSection["name"]);

            $arFilter = array('IBLOCK_ID' => $this->catalogIblockId, "XML_ID" => $arSection["id"]);
            $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter, false, ["ID", "NAME", "XML_ID"]);
            while ($arSect = $rsSect->GetNext()) {
                $this->arSiteCategoriesByXmlId[$arSect["XML_ID"]] = $arSect;
            }
        }

        $this->EndDebugTime(__FUNCTION__);

        return $result;
    }

    private function DeleteSiteSection ($sectionId = 0) {
        $this->Logger->log("LOG", "Удаление раздела ".$this->arSiteCategoriesByXmlId[$sectionId]["NAME"]);
        $this->StartDebugTime(__FUNCTION__);

        global $DB;
        $siteSectionId = $this->arSiteCategoriesByXmlId[$sectionId]["ID"];
        $DB->StartTransaction();
        if (!CIBlockSection::Delete($siteSectionId)) {
            $this->Logger->log("ERROR", "Ошибка при удалении раздела");
            $DB->Rollback();
        } else
            $DB->Commit();

        $sConnect = new ImarketSectionsConnections();
        $sConnect->deleteRows([$siteSectionId]);

        $this->EndDebugTime(__FUNCTION__);
    }

    private function UpdateSectionsConnections () {
        $this->Logger->log("LOG", "Обновления сопоставления разделов");
        $this->StartDebugTime(__FUNCTION__);

        $this->GetSectionsConnections();

        if (!empty($this->sectionConnections)) {
            foreach ($this->sectionConnections as $catalogAppId => $arItem) {
                if (!empty($this->endSections[$catalogAppId])) {
                    $CASection = $this->endSections[$catalogAppId];

                    if ($CASection["name"] != $arItem["CATALOG_APP_SECTION_NAME"]) {
                        $sql = "UPDATE catalog_app_section_connections SET 
                            CATALOG_APP_SECTION_NAME = '".$CASection["name"]."' 
                            WHERE ID = ".$arItem["ID"];
                        $this->connection->query($sql);
                    }
                } else {
                    $sql = "INSERT INTO catalog_app_section_connections 
                        (`CATALOG_APP_SECTION_NAME`, `CATALOG_APP_SECTION_ID`, `SITE_SECTION_NAME`, `SITE_SECTION_ID`) VALUES 
                        ('".$arItem["name"]."', '".$catalogAppId."', '".$this->arSiteCategoriesByXmlId[$catalogAppId]["NAME"]."', '".$this->arSiteCategoriesByXmlId[$catalogAppId]["ID"]."')";
                    $this->connection->query($sql);
                }
            }
        } else {
            foreach ($this->endSections as $id => $arItem) {
                $sql = "INSERT INTO catalog_app_section_connections 
                        (`CATALOG_APP_SECTION_NAME`, `CATALOG_APP_SECTION_ID`, `SITE_SECTION_NAME`, `SITE_SECTION_ID`) VALUES 
                        ('".$arItem["name"]."', '".$arItem["id"]."', '".$this->arSiteCategoriesByXmlId[$arItem["id"]]["NAME"]."', '".$this->arSiteCategoriesByXmlId[$arItem["id"]]["ID"]."')";
                $this->connection->query($sql);
            }
        }

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * Получить товары со скидкой, нужно для сравнения какие товары отключить, а какие нет
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    public function GetSectionsConnections() {
        $this->Logger->log("LOG", "Получаем сопоставления разделов");
        $this->StartDebugTime(__FUNCTION__);

        $sConnect = new ImarketSectionsConnections();
        $this->sectionConnections = $sConnect->getAll();

        $this->Logger->log("LOG", "Данные получены, всего сопоставленных разделов ".count($this->sectionConnections));

        $this->EndDebugTime(__FUNCTION__);
    }

    private function getSectionId($arItem) {
            $sectionId=false;
            if(is_array($arItem)) {
                $cat_id=$arItem["category"]["id"];
            } else {
                $cat_id= intval($arItem);
            }
            if($cat_id) {
                $sectionId = $this->sectionConnections[$cat_id];
                if(!$sectionId && $cat_id) {
                    $sectionId=$this->arSiteCategoriesByXmlId[$cat_id]['ID'];
                }
            }
            return $sectionId;
    }
    /**
     * создать товары на сайте
     *
     * @return bool
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function AddGoods($only_add=false) {
        $this->Logger->log("LOG", "Создание товаров на сайте");
        $this->StartDebugTime(__FUNCTION__);
        //$this->GetCatalogModels();
        $max_iter=1000000;
        //$this->StartDebugTime("restAPI->GetLimitModels");
        
        if($this->isPriceFirst) {
            $limit=1000;
            $funcs=['GetLimitPriceModels','GetLimitModels'];
        } else if($only_add){
            $limit=10000;
            $max_iter=1;
            $funcs=['GetCacheModels'];
            
        }   else { 
            $limit=1000;
            $funcs=['GetLimitModels','GetModifiedModels','GetModifiedCards','SaveLastUpdate'];
        }
        foreach ($funcs as $func) {
            $iter=1;
            $offset=Option::get('imarket_catalog_app', "models_offset[{$func}]",0);


      //      print_r("Offset: {$offset}".PHP_EOL);
            $arActivationRules = [
                "IMAGES" => false,
                "PROPERTIES" => false
            ];
            
            $new_offset=$offset;
            $this->Logger->log("LOG", "Отступ: {$offset} Лимит: {$limit} Функция: {$func}");
            print_r("Отступ: {$offset} Лимит: {$limit} Функция {$func}".PHP_EOL);
            while ($this->arCatalogsDiff = $this->restAPI->$func(false,$offset,$limit)) {
            //$this->EndDebugTime("restAPI->GetLimitModels");
             $cnt= count($this->arCatalogsDiff);
            print_r('Итерация: '.$iter.PHP_EOL." Отступ: {$offset} Получено: {$cnt}");
            ob_flush();
            $this->Logger->log("LOG", 'Итерация: '.$iter.PHP_EOL." Отступ: {$offset}");
            if (empty($this->arCatalogsDiff)) {
                $this->Logger->log("LOG", "Нет товаров для создания");
                $this->EndDebugTime(__FUNCTION__);
                return false;
            }

            $el = new CIBlockElement;
            $addedCount = 0;
            $updatedCount = 0;
            $all_diff=0;
            $skipped=0;
            $xml_ids=[];
            $unique=0;
            foreach ($this->arCatalogsDiff as $arItem) {
                if($xml_ids[$arItem['externalId']]) {
                    $xml_ids[$arItem['externalId']]=$xml_ids[$arItem['externalId']]+1;
                    print_r("{$xml_ids[$arItem['externalId']]} ".$arItem['externalId'].PHP_EOL);
                  //  print_r($arItem);
                  
                } else {
                    $xml_ids[$arItem['externalId']]=1;
                    $unique++;
                };
            }
            print_r("Уникальных: ".$unique.PHP_EOL);
        
       

            foreach ($this->arCatalogsDiff as $arItemKey=>$arItem) {
                $this->StartDebugTime("AddGood");
                

                $filter = ['IBLOCK_ID' => $this->catalogIblockId,'XML_ID'=>$arItem["externalId"]];
                $select = ['ID','NAME', 'XML_ID'];
                $dbl = CIBlockElement::GetList(["ID" => "ASC"], $filter, false, false, $select);
                $update_id=false;
                if($model=$dbl->Fetch()) {
                    if($only_add) {$cont=" Пропускаем.";}
                    else {$cont=' ';}
                    print_r("{$skipped} Key:{$arItemKey}  Товар уже есть в базе [".$model["XML_ID"]."] ".$cont.PHP_EOL);
                    $this->Logger->log("LOG", "{$skipped} Key:{$arItemKey} Товар уже есть в базе [".$model["XML_ID"]."] ".$cont);
                    $update_id=$model["ID"];
                    ob_flush();
                    $skipped++;
                    if($only_add) continue;
                }
                
                $this->StartDebugTime("restAPI->getModelProperties");
                $props=$this->restAPI->getModelProperties($arItem['id'],false);
                $arModelProperties= array_merge($arItem,$props);
                $this->EndDebugTime("restAPI->getModelProperties");

                $modelSectionId = $arItem["category"]["id"];
                $this->getAppCategoryProperties($modelSectionId);

                $sectionId = $this->getSectionId($arItem);
     //           print_r('Section ID:'.$sectionId.PHP_EOL);

                if($sectionId) {
                    $name = $this->getModelName($arItem); 
                    $code = $this->getCode($arItem);

                    //$image = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/local/modules/imarket_catalog_app/img/no-photo.png");

                    $arLoadProductArray = [
                        "IBLOCK_SECTION_ID" => $sectionId,
                        "IBLOCK_ID"      => $this->catalogIblockId,
                        "NAME"           => $name,
                        "CODE"           => $code,
                        "ACTIVE"         => "N",
                        "XML_ID"         => $arItem["externalId"],
                        //"DETAIL_PICTURE" => $image,
                        //"PREVIEW_PICTURE" => $image
                    ];
                    if($arModelProperties['description']&&$arModelProperties['description']['value']) {
                        $arLoadProductArray['DETAIL_TEXT']=$arModelProperties['description']['value'];
                    }

                //    print_r("До свойств:".PHP_EOL);
                    $this->getBitrixProperties($arModelProperties,$arLoadProductArray,$update_id);
                  //  print_r("После свойств:".PHP_EOL);
                 //   print_r($arLoadProductArray);

                    $this->Logger->log("LOG", "Добавление товара [".$arItem["id"]."] ".$name);
                 //   print_r("Добавление товара [".$arItem["id"]."] ".$name. print_r($arLoadProductArray,true));

                    if($update_id) {
                        $res=$el->Update($update_id,$arLoadProductArray);
                        if($res) {
                         //   print_r("Товар {$update_id} обновлен \r\n");
                            $this->Logger->log("LOG", "Товар {$update_id} обновлен \r\n");
                            $this->UpdateProduct($update_id,$arModelProperties);
                            
                            $updatedCount++;

                        } else {
                            $this->Logger->log("ERROR", "Товар не обновлен \r\n".print_r($el->LAST_ERROR, true));
                        }

                    } else
                    if($el_id=$el->Add($arLoadProductArray)) {
                       // print_r("Товар успешно добавлен {$el_id}");
                        $this->Logger->log("LOG", "Товар успешно добавлен");
                        $this->UpdateProduct($el_id,$arModelProperties);
                        $addedCount++;
                    } else {
                        $this->Logger->log("ERROR", "Товар не добавлен \r\n".print_r($el->LAST_ERROR, true));


                    }
                    
                    
                } else {
                    $this->Logger->log("ERROR", "Категория товара не определена \r\n".print_r($arItem, true));
                }
                $new_offset++;
                $offset=Option::set('imarket_catalog_app', "models_offset[{$func}]",$new_offset);
                $diff=$this->EndDebugTime("AddGood");
                $all_diff+=$diff;
                if($update_id) {
                    print_r("{$updatedCount} товаров обновлено. Последний ID={$update_id} Время {$diff} c. Всего прошло {$all_diff} c".PHP_EOL);
                    $this->Logger->log("LOG", "{$updatedCount} товаров обновлено. Последний ID={$update_id} Время {$diff} c. Всего прошло {$all_diff} c");
                } else {
                    print_r("{$addedCount} товаров добавлено. Последний ID={$el_id} Время {$diff} c. Всего прошло {$all_diff} c".PHP_EOL);
                    $this->Logger->log("LOG", "{$addedCount} товаров добавлено. Последний ID={$el_id} Время {$diff} c. Всего прошло {$all_diff} c");

                }
                ob_flush();
            }
            $offset=Option::get('imarket_catalog_app', "models_offset[{$func}]",0);
            $iter++;
            
            if($iter>=$max_iter) break;
            }
            if($iter>=$max_iter) break;
        }
        $this->Logger->log("LOG", "Всего успешно добавлено ".$addedCount);
        $this->SaveAppCategoryProperties();
        $this->EndDebugTime(__FUNCTION__);
    }
    
    
    private function getBitrixProperties($arModelProperties,&$arLoadProductArray,$update=false) {
        $this->StartDebugTime("getBitrixProperties");
        $PROP = [];
        $arTmpModelProperties=[];
        $modelSectionId = $arModelProperties["category"]["id"];
        $PROP["CA_ID"] = $arModelProperties["id"];
        
        if($arModelProperties['price'] && $arModelProperties['price']['supplierId'] && $this->arSuppliers[$arModelProperties['price']['supplierId']]) {
            $PROP["CA_SUPPLIER"]=$this->arSuppliers[$arModelProperties['price']['supplierId']]['name'];
           // print_r('Поставщик:'.$PROP["CA_SUPPLIER"]);
        }
        try {
            $this->StartDebugTime("saveImage");
            if(!empty($arModelProperties["images"])&& (sizeof($arModelProperties["images"])>0) && $update) {
                
                $arTmpModelProperties["images"] = $arModelProperties["images"];

                    /*
                    * В апи попадаются файлы с расширением .unknown и некорректным type text/html
                    */

                    
                foreach($arTmpModelProperties["images"] as $key => $item){
                    $arTmpImage = CFile::MakeFileArray($item);
                    if(strpos($arTmpImage['name'], ".unknown") || $arTmpImage['type'] == "text/html") {
                        unset($arModelProperties["images"][$key]);
                    }
                    
                }
                sort($arModelProperties["images"]);
                    
                
                
              //  print_r(PHP_EOL.'Картинки: '.print_r($arModelProperties["images"],true).' элемент '.$update.PHP_EOL);
                
                $arImages = array();
                $res = CIBlockElement::GetProperty($this->catalogIblockId, $update, Array("sort"=>"asc"), array("CODE" => "CA_MORE_PHOTO"));
                while ($ob = $res->GetNext())
                {
                    
                    $arFile=CFile::GetFileArray($ob['VALUE']);
                    $arImages[$ob['VALUE']]=$arFile['ORIGINAL_NAME'];
                }
                //print_r(PHP_EOL."Уже в базе:" .print_r($arImages,true).PHP_EOL);
                
                $addImages=[];
                foreach ($arModelProperties["images"] as $imId => $imgUrl) {
                    $parts=explode('/', $imgUrl);
                    if(sizeof($parts)>0) {
                        $fileName=array_pop($parts);
                        if(!in_array($fileName, $arImages)) $addImages[]=$imgUrl;
                    }
                }
                //print_r("Картинки к добавлению: ".PHP_EOL);
                
                //print_r($addImages);
                if($addImages) {
                    
                    
                    $PROP[$this->arCASiteProperties["CA_MORE_PHOTO"]["ID"]] = [];
                    if(!$arImages) {
                        $mainImage = CFile::MakeFileArray($addImages[0], "image/jpeg");
                        $arLoadProductArray["DETAIL_PICTURE"] = $mainImage;
                        $arLoadProductArray["PREVIEW_PICTURE"] = $mainImage;
                        $PROP[$this->arCASiteProperties["CA_MORE_PHOTO"]["ID"]][]=$mainImage;
                    }
                    foreach ($addImages as $imId=>$item) {

                        if( (!$arImages && ($imId>0)) || $arImages) {
                            $PROP[$this->arCASiteProperties["CA_MORE_PHOTO"]["ID"]][] = CFile::MakeFileArray($item, "image/jpeg");
                        }

                    }
                    
                  //  print_r($PROP);
                    
                }
                
                
                
            }
            if (!empty($arModelProperties["images"]) && !$update) {
                $arTmpModelProperties["images"] = $arModelProperties["images"];

                    /*
                    * В апи попадаются файлы с расширением .unknown и некорректным type text/html
                    */

                    
                foreach($arTmpModelProperties["images"] as $key => $item){
                    $arTmpImage = CFile::MakeFileArray($item);
                    if(strpos($arTmpImage['name'], ".unknown") || $arTmpImage['type'] == "text/html") {
                        unset($arModelProperties["images"][$key]);
                    }
                    
                }

                sort($arModelProperties["images"]);
                    
                $mainImage = CFile::MakeFileArray($arModelProperties["images"][0], "image/jpeg");
                $arLoadProductArray["DETAIL_PICTURE"] = $mainImage;
                $arLoadProductArray["PREVIEW_PICTURE"] = $mainImage;

                foreach ($arModelProperties["images"] as $k => $item) {
                    if (empty($this->arCASiteProperties["CA_MORE_PHOTO"])) {
                        $this->CreateProperty($this->arCAProperties["CA_MORE_PHOTO"], 0);
                    }

                    $PROP[$this->arCASiteProperties["CA_MORE_PHOTO"]["ID"]] = [];

                    if ($k == 0) {
                        $PROP[$this->arCASiteProperties["CA_MORE_PHOTO"]["ID"]][] = $mainImage;
                    } else {
                        $PROP[$this->arCASiteProperties["CA_MORE_PHOTO"]["ID"]][] = CFile::MakeFileArray($item, "image/jpeg");
                    }
                }

                if (count($PROP[$this->arCASiteProperties["CA_MORE_PHOTO"]["ID"]]) >= $this->arCASettings["AUTO_UPDATE_RULES"]["IMAGES"]) {
                    $arActivationRules["IMAGES"] = true;
                }
            }
            $this->EndDebugTime("saveImage");
        } catch(Exception $e) {
                //echo 'Message: ' .$e->getMessage();
        }
      

        $this->StartDebugTime("setBrand");
        if ($arModelProperties['vendor'] && $arModelProperties['vendor']['id'] && empty($this->arCASiteProperties["CA_BRAND"]["ENUMS"][$arModelProperties['vendor']['id']])) {
                $enumData = ['id' => $arModelProperties['vendor']['id'], "value" => $arModelProperties['vendor']["name"]];
           
                $this->CreatEnum($this->arCASiteProperties["CA_BRAND"], $enumData, true);
       
            }

            $PROP["CA_BRAND"] = $this->arCASiteProperties["CA_BRAND"]["ENUMS"][$arModelProperties['vendor']['id']];
        $this->EndDebugTime("setBrand");
            $goodsPropertiesCount = 0;
          //  print_r($arModelProperties);
            if(!empty($arModelProperties["propertyValues"]))  {
                foreach ($arModelProperties["propertyValues"] as $arProp) {
             //       print_r("arProp definitionId: ".$arProp["definitionId"].PHP_EOL);
               //     print_r($arProp);
                 //   print_r(PHP_EOL."---------------------------".PHP_EOL);
                    $goodsPropertiesCount++;
                    $property = $this->arCASectionProperties[$arModelProperties['category']['id']]["PROPERTIES"][$arProp["definitionId"]];
                    if($property['id'] && intval($property['id'])) {
                    $property["code"] = "CA_".translit(preg_replace('/[^a-zA-Zа-яА-Я0-9]/ui', '', $property["name"]));
                    $property["code"] = strtoupper($property["code"]);
                    $property["code"] .= "_".$property["id"];

                    if (!empty($this->arCASiteProperties[$property["code"]]["ID"])) {
                         $this->CheckPropertySection($modelSectionId, $this->arCASiteProperties[$property["code"]]["ID"]);
                    }
                  //  print_r("Property: ".PHP_EOL);
                  //  print_r($property);
                   // print_r(PHP_EOL."---------------------------".PHP_EOL);
                    $arPropData = [
                        "NAME" => $property["name"],
                        "ACTIVE" => "Y",
                        "SORT" => "500",
                        "CODE" => $property["code"],
                        "PROPERTY_TYPE" => "S",
                        "IBLOCK_ID" => $this->catalogIblockId
                    ];
                    
                    switch($property["type"]) {
                        case "Decimal":
                            $arPropData["PROPERTY_TYPE"] = "N";
                            $arPropData["WITH_DESCRIPTION"] = "Y";

                            
                            if ($this->arCASiteProperties[$property["code"]] && $this->arCASiteProperties[$property["code"]]["PROPERTY_TYPE"] != "N") {
                              //  print_r($property["code"]." ".print_r($this->arCASiteProperties[$property["code"]],true)." нужно N".PHP_EOL);
                                $this->DeleteProperty($this->arCASiteProperties[$property["code"]]["ID"]);
                            }

                            if (empty($this->arCASiteProperties[$property["code"]])) {
                                $this->CreateProperty($arPropData, $modelSectionId);
                                $this->Logger->log("LOG", $arPropData);
                            }

                            $PROP[$this->arCASiteProperties[$property["code"]]["ID"]] = ["VALUE" => $arProp["decimalValue"], "DESCRIPTION" => $property["unit"]];

                            break;
                        case "String":
                            
                            if ($this->arCASiteProperties[$property["code"]] && $this->arCASiteProperties[$property["code"]]["PROPERTY_TYPE"] != "S") {
                              //  print_r($property["code"]." ".print_r($this->arCASiteProperties[$property["code"]],true)." нужно S".PHP_EOL);
                                $this->DeleteProperty($this->arCASiteProperties[$property["code"]]["ID"]);
                            }

                            if (empty($this->arCASiteProperties[$property["code"]])) {
                                $this->CreateProperty($arPropData, $modelSectionId);
                                $this->Logger->log("LOG", $arPropData);
                            }

                            $PROP[$this->arCASiteProperties[$property["code"]]["ID"]] = $arProp["stringValue"];
                            break;
                        case "Enum":
                            $arPropData["PROPERTY_TYPE"] = "L";

                            if ($this->arCASiteProperties[$property["code"]] && $this->arCASiteProperties[$property["code"]]["PROPERTY_TYPE"] != "L") {
                             //   print_r($this->arCASiteProperties[$property["code"]]["PROPERTY_TYPE"]." нужно L".PHP_EOL);
                                $this->DeleteProperty($this->arCASiteProperties[$property["code"]]["ID"]);
                            }

                            if (empty($this->arCASiteProperties[$property["code"]])) {
                                $this->CreateProperty($arPropData, $modelSectionId);
                                $this->Logger->log("LOG", $arPropData);
                            }

                            if (empty($this->arCASiteProperties[$property["code"]]["ENUMS"][$arProp["enumValue"]["id"]])) {
                                $this->CreatEnum($this->arCASiteProperties[$property["code"]], $arProp["enumValue"],true);
                            }

                            $PROP[$this->arCASiteProperties[$property["code"]]["ID"]] = $this->arCASiteProperties[$property["code"]]["ENUMS"][$arProp["enumValue"]["id"]]["ID"];
                            break;
                        case "Flag":
                            $arPropData["PROPERTY_TYPE"] = "L";
                            $arPropData["MULTIPLE"] = "Y";

                            if ($this->arCASiteProperties[$property["code"]] && $this->arCASiteProperties[$property["code"]]["PROPERTY_TYPE"] != "L") {
                             //   print_r($this->arCASiteProperties[$property["code"]]["PROPERTY_TYPE"]." нужно L".PHP_EOL);
                                $this->DeleteProperty($this->arCASiteProperties[$property["code"]]["ID"]);
                            }

                            if (empty($this->arCASiteProperties[$property["code"]])) {
                                $this->CreateProperty($arPropData, $modelSectionId);
                                $this->Logger->log("LOG", $arPropData);
                            }

                            foreach ($arProp["flagValues"] as $arEnums) {
                                if (empty($this->arCASiteProperties[$property["code"]]["ENUMS"][$arEnums["propertyEnum"]["id"]])) {
                                    $this->CreatEnum($this->arCASiteProperties[$property["code"]], $arEnums["propertyEnum"], true);
                                }
                            }

                            foreach ($arProp["flagValues"] as $arEnums) {
                                $PROP[$this->arCASiteProperties[$property["code"]]["ID"]][] = $this->arCASiteProperties[$property["code"]]["ENUMS"][$arEnums["propertyEnum"]["id"]]["ID"];
                            }
                            break;
                        case "Boolean":
                            $arPropData["PROPERTY_TYPE"] = "L";
                            $arPropData["LIST_TYPE"] = "C";

                            if ($this->arCASiteProperties[$property["code"]] && $this->arCASiteProperties[$property["code"]]["PROPERTY_TYPE"] != "L") {
                               // print_r($this->arCASiteProperties[$property["code"]]["PROPERTY_TYPE"]." нужно L".PHP_EOL);
                                $this->DeleteProperty($this->arCASiteProperties[$property["code"]]["ID"]);
                            }

                            if (empty($this->arCASiteProperties[$property["code"]])) {
                                $this->CreateProperty($arPropData, $modelSectionId);
                                $this->Logger->log("LOG", $arPropData);
                                $this->CreatEnum($this->arCASiteProperties[$property["code"]], ["id" => 0, "value" => 0, "DEF" => "Y"]);
                                $this->CreatEnum($this->arCASiteProperties[$property["code"]], ["id" => 1, "value" => 1], true);
                            }

                            $PROP[$this->arCASiteProperties[$property["code"]]["ID"]] = $arProp["booleanValue"] ?
                                $this->arCASiteProperties[$property["code"]]["ENUMS"][1]["ID"] :
                                $this->arCASiteProperties[$property["code"]]["ENUMS"][0]["ID"];

                            break;
                        default:
                            break;
                    }
                
                    }
                }
            }
        $totalSectionProperties = count($this->arCASectionProperties[$modelSectionId]["PROPERTIES"]);
        $currentGoodsPropertiesPercent = round($goodsPropertiesCount * 100 / $totalSectionProperties);

        if ($currentGoodsPropertiesPercent >= $this->arCASettings["AUTO_UPDATE_RULES"]["PROPERTIES"]) {
            $arActivationRules["PROPERTIES"] = true;
        }
            

        if ($arActivationRules["IMAGES"] && $arActivationRules["PROPERTIES"]) {
            $PROP["CA_AUTO_ACTIVATE"] = 1;
        } else {
                $PROP["CA_AUTO_ACTIVATE"] = 1;
        }
        $arLoadProductArray["PROPERTY_VALUES"] = $PROP;
        $this->EndDebugTime("getBitrixProperties");
        return $PROP;
    }
    
       private function CreateProperty ($arData = [], $sectionId = 0) {
        $this->Logger->log("LOG", "Создание свойства ".$arData["NAME"]);
        $this->StartDebugTime(__FUNCTION__);
        $iblockProperty = new CIBlockProperty;

        if ($propertyID = $iblockProperty->Add($arData)) {
            $property = CIBlockProperty::GetByID($propertyID, $this->catalogIblockId)->GetNext();
            $this->arCASiteProperties[$property["CODE"]] = $property;
            $this->Logger->log("LOG", "Свойство ".$arData["NAME"]." добавлено");

            if ($arData["PROPERTY_TYPE"] == "L" && !empty($sectionId)) {
                $this->Logger->log("LOG", "Списочное свойство, добавляем ззначения");

                $expld_code = explode("_", $property["CODE"]);
                $CAPropId = end($expld_code);

                if (empty($this->arCASectionProperties[$sectionId]["PROPERTIES"][$CAPropId]["enumValues"])) {
                    $this->Logger->log("LOG", "Нет значений для добавления");
                } else {
                    foreach ($this->arCASectionProperties[$sectionId]["PROPERTIES"][$CAPropId]["enumValues"] as $arEnum) {
                        $this->CreatEnum($this->arCASiteProperties[$property["CODE"]], $arEnum);
                    }

                    $property_enums = CIBlockPropertyEnum::GetList(
                        ["SORT" => "ASC"],
                        ["IBLOCK_ID" => $this->catalogIblockId, "CODE" => $property["CODE"]]
                    );
                    while ($arEnum = $property_enums->GetNext()) {
                        $this->arCASiteProperties[$property["CODE"]]["ENUMS"][$arEnum["XML_ID"]] = $arEnum;
                    }
                }
            }

            if (!empty($sectionId)) {
                
                $bxCatId= $this->getSectionId($sectionId);
                if($bxCatId) {
                    $this->Logger->log("LOG", "Добавляем привязку свойства ".$this->arCASiteProperties[$property["CODE"]]["NAME"]." к разделу ".$this->arCASectionProperties[$sectionId]["NAME"]);

                    $sql = "INSERT INTO b_iblock_section_property (`IBLOCK_ID`, `SECTION_ID`, `PROPERTY_ID`, `SMART_FILTER`) VALUES 
                            (".$this->catalogIblockId.", {$bxCatId}, ".$propertyID.", 'N')";

                    $this->Logger->log("LOG", $sql);

                    $this->connection->query($sql);
                } else {
                    $this->Logger->log("ERROR", "Не найдена категория в битрикс для раздела {$sectionId}");
                }
            }
        } else {
            $this->Logger->log("ERROR", "Ошибка при создании свойства ".$iblockProperty->LAST_ERROR."\r\n".print_r($arData, true));
        }

        $this->EndDebugTime(__FUNCTION__);
    }

    
    private function DeleteProperty ($propertyId = 0) {
        $this->Logger->log("LOG", "Получаем свойства товаров");
        $this->StartDebugTime(__FUNCTION__);

        if (empty($propertyId)) {
            $this->Logger->log("LOG", "Не указан id свойства");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        if (!CIBlockProperty::Delete($propertyId)) {
            $this->Logger->log("ERROR", "Не удалось удалть свойство");
        }

        $this->EndDebugTime(__FUNCTION__);
    }
    private function CheckPropertySection ($sectionId = 0, $propertyId = 0) {
        $this->Logger->log("LOG", "Проверяем привязку свойства к разделу");
        $this->StartDebugTime(__FUNCTION__);

        if (empty($sectionId)) {
            $this->Logger->log("LOG", "Нет id раздела");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        if (empty($propertyId)) {
            $this->Logger->log("LOG", "Нет id свойства");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }
        $bxCatId= $this->getSectionId($sectionId);
        if(!$bxCatId) {
            $this->Logger->log("LOG", "Не найдена категория в битрикс для {$sectionId}");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }
        $sql = "SELECT SECTION_ID FROM b_iblock_section_property WHERE SECTION_ID = {$bxCatId} AND PROPERTY_ID = ".$propertyId." AND IBLOCK_ID = ".$this->catalogIblockId."";
        $res = $this->connection->query($sql);
        if (!$arItem = $res->fetch()) {
            $this->Logger->log("LOG", "Добавляем привязку свойства к разделу ".$this->arCASectionProperties[$sectionId]["NAME"]);

            $sql = "INSERT INTO b_iblock_section_property (`IBLOCK_ID`, `SECTION_ID`, `PROPERTY_ID`, `SMART_FILTER`) VALUES 
                        (".$this->catalogIblockId.", {$bxCatId}, ".$propertyId.", 'N')";

            $this->connection->query($sql);
            $this->Logger->log("LOG", "Добавление свойства к разделу : " . $sql);
        }

        $this->EndDebugTime(__FUNCTION__);
    }
    
    private function CreatEnum ($property = [], $enumData = [], $getEnumFlag = false) {
        $this->Logger->log("LOG", "Добавляем значение свойства");
        $this->StartDebugTime(__FUNCTION__);

        if (empty($property)) {
            $this->Logger->log("LOG", "Нет данных о свойстве");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        if (empty($enumData)) {
            $this->Logger->log("LOG", "Нет данных о значении");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        $ibpenum = new CIBlockPropertyEnum;

        $arFields = [
            'PROPERTY_ID' => $property["ID"],
            'VALUE' => $enumData["value"],
            'XML_ID' => $enumData['id']
        ];

        if (isset($enumData["DEF"])) {
            $arFields["DEF"] = $enumData["DEF"];
        }

   //     print_r($arFields);
        $property_enums = CIBlockPropertyEnum::GetList(
                ["SORT" => "ASC"],
                ["PROPERTY_ID" => $arFields['PROPERTY_ID'], "XML_ID" => $arFields['XML_ID']]
            );
        $update_id=false;
        if($prop_enum=$property_enums->Fetch()) {
            if($prop_enum['VALUE']!=$arFields['VALUE']) {
                if ($ibpenum->Update($prop_enum['id'],$arFields)) {
                    $this->Logger->log("LOG", "Значение обновлено");
                } else {
                    $this->Logger->log("ERROR", "Ошибка при добавлении значения свойства \r\n".print_r($ibpenum->LAST_ERROR, true));
                }
            }
        } else {
       
            if ($ibpenum->Add($arFields)) {
                $this->Logger->log("LOG", "Значение добавлено");
            } else {
                $this->Logger->log("ERROR", "Ошибка при добавлении значения свойства".PHP_EOL. print_r($arFields,true)." \r\n".print_r($ibpenum->LAST_ERROR, true));
            }
        }
        if ($getEnumFlag) {
            $property_enums = CIBlockPropertyEnum::GetList(
                ["SORT" => "ASC"],
                ["IBLOCK_ID" => $this->catalogIblockId, "CODE" => $property["CODE"]]
            );
            while ($arEnum = $property_enums->GetNext()) {
                $this->arCASiteProperties[$property["CODE"]]["ENUMS"][$arEnum["XML_ID"]] = $arEnum;
            }
        }

        $this->EndDebugTime(__FUNCTION__);
    }
    
    private function getCode($arItem) {
        
        $name=$this->getModelName($arItem);
        $catalogAppId = $this->restAPI->PrepareCatalogAppId($arItem["id"]);


        $code=translit($name);
        $code .= "_".$catalogAppId;
        $code = strtolower($code);
        return $code;
    }
    private function getAppCategoryProperties($modelSectionId) {
        $this->StartDebugTime(__FUNCTION__."({$modelSectionId})");
        //$modelSectionId = $arItem["category"]["id"];
            if (empty($this->arCASectionProperties[$modelSectionId])) {
                $arSectionPropTMP = $this->restAPI->getCategoryById($modelSectionId,true);
//                print_r($arSectionPropTMP);
                
                
                
                if (!empty($arSectionPropTMP["ERROR"])) {
                    $this->Logger->log("ERROR", "Ошибка при получении свойств раздела! ".$arSectionPropTMP["ERROR"]." : #".$modelSectionId);
                } else {
                    $this->arCASectionProperties[$modelSectionId]["ID"] = $arSectionPropTMP["id"];
                    $this->arCASectionProperties[$modelSectionId]["NAME"] = $arSectionPropTMP["name"];

                    foreach ($arSectionPropTMP["properties"] as $arProp) {
                        $this->arCASectionProperties[$modelSectionId]["PROPERTIES"][$arProp["id"]] = $arProp;
                    }

                    $arSectionPropTMP = [];
                }
            }
            $this->EndDebugTime(__FUNCTION__."({$modelSectionId})");
        
    }

    /**
     * создать товары на сайте
     *
     * @return bool
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function CreateDiffGoods() {
        $this->Logger->log("LOG", "Создание товаров на сайте");
        $this->StartDebugTime(__FUNCTION__);

        if (empty($this->arCatalogsDiff)) {
            $this->Logger->log("LOG", "Нет товаров для создания");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        $el = new CIBlockElement;
        $addedCount = 0;
        foreach ($this->arCatalogsDiff as $arItem) {
//            if ($arItem["externalId"] != "aeea2ff1-6608-11e6-80ba-52540065dbdf") {
//                continue;
//            }

         //   $sectionId = $this->sectionConnections[$arItem["category"]["id"]];
            $sectionId= $this->getSectionId($arItem);
            if(!$sectionId)
            if (!empty($this->arCategories[$arItem["category"]["id"]]["singularName"])) {
                $sectionName = $this->arCategories[$arItem["category"]["id"]]["singularName"];
            } else {
                $sectionName = $arItem["category"]["name"];
            }

            $vName = strtolower($arItem["vendor"]["name"]);

            $name = trim($sectionName). " ".trim($vName)." ".trim($arItem["name"]);

            if (!empty($arItem["color"])) {
                $name = trim($name);
                $name .= " ".trim($arItem["color"]);
            }

            if (!empty($arItem["article"]) && ($arItem["article"] != $arItem["name"])) {
                $name = trim($name);
                $name .= " [".trim($arItem["article"])."]";
            }

            $catalogAppId = $this->restAPI->PrepareCatalogAppId($arItem["id"]);

            $code = translit($name);
            $code .= "_".$catalogAppId;
            $code = strtolower($code);
            //$image = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/local/modules/imarket_catalog_app/img/no-photo.png");

            $arLoadProductArray = [
                "IBLOCK_SECTION_ID" => $sectionId,
                "IBLOCK_ID"      => $this->catalogIblockId,
                "NAME"           => $name,
                "CODE"           => $code,
                "ACTIVE"         => "N",
                "XML_ID"         => $arItem["externalId"],
                //"DETAIL_PICTURE" => $image,
                //"PREVIEW_PICTURE" => $image
            ];

            $this->Logger->log("LOG", "Добавление товара [".$arItem["id"]."] ".$name);
            if(!$el->Add($arLoadProductArray)) {
                $this->Logger->log("ERROR", "Товар не добавлен \r\n".print_r($el->LAST_ERROR, true));
            } else {
                $this->Logger->log("LOG", "Товар успешно добавлен");
                $addedCount++;
            }
        }

        $this->Logger->log("LOG", "Всего успешно добавлено ".$addedCount);

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function CreateNewVendors () {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Создание новых брендов");
        $translitParams = ['replace_space' => '-', 'replace_other' => ''];

        $arNeedAddVendors = [];
        $vendorXmlToValue = [];
        foreach ($this->arSiteVendors as $siteVendorName => $arSiteVendor) {
            $vendorXmlToValue[$arSiteVendor['XML_ID']] = $arSiteVendor['VALUE'];
        }

        $arDiffVendors = [];
        foreach ($this->arCatalogsDiff as $arItem) {
            if (!in_array($arItem['vendor']['id'], $arDiffVendors)) {
                $arDiffVendors[] = $arItem['vendor']['id'];
            }
        }

        foreach ($arDiffVendors as $diffVendorId) {
            $CAVendor = $this->arVendors[$diffVendorId]["name"];
            $CAVendorL = strtolower($this->arVendors[$diffVendorId]["name"]);

            if (empty($this->arSiteVendors[$CAVendorL])) {
                $CAXML_ID = CUtil::translit($CAVendor, 'ru', $translitParams);
                $CAXML_ID = strtolower($CAXML_ID);
                if (empty($this->arSiteVendorsByXmlId[$CAXML_ID])) {
                    $arNeedAddVendors[] = ["NAME" => $CAVendor, "XML_ID" => $CAXML_ID];
                } else {
                    $arNeedAddVendors[] = ["NAME" => $CAVendor, "XML_ID" => $CAXML_ID."_".$diffVendorId];
                }
            }
        }

        if (empty($arNeedAddVendors)) {
            $this->Logger->log("LOG", "Нет брендов для добавления");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        $this->Logger->log("LOG", "Нужно добавить ".count($arNeedAddVendors)." брендов");

        $arAddedXML = [];
        $chunks = array_chunk($arNeedAddVendors, 10000);
        $bc = 0;
        foreach ($chunks as $k => $chunk) {
            $sql = 'INSERT INTO b_iblock_property_enum (`PROPERTY_ID`, `VALUE`, `DEF`, `SORT`, `XML_ID`) VALUES ';

            foreach ($chunk as $k => $arItem) {
                if($k > 0) {
                    $sql .= ", ";
                }

                $sql .= "(57893, '" . addslashes($arItem["NAME"]) . "', 'N', 500, '" . $arItem["XML_ID"] . "')";
                $arAddedXML[] = $arItem["XML_ID"];
                $bc++;
            }

            $this->connection->query($sql);
            $this->Logger->log("LOG", "Добавлено ".$bc." записей");
        }

        // получить добавленные бренды
        $el = new CIBlockElement();
        $sql = "SELECT VALUE, ID FROM b_iblock_property_enum WHERE XML_ID IN ('".implode("','", $arAddedXML)."')";
        $res = $this->connection->query($sql);
        while($arItem = $res->fetch()) {
            $this->Logger->log("LOG", "Добавление нового бренда ".$arItem["VALUE"]." в инфоблок");
            $props = ['BRAND' => $arItem["ID"]];
            $fields = [
                'IBLOCK_ID' => BRANDS_IBLOCK_ID,
                'NAME' => $arItem["VALUE"],
                'ACTIVE' => 'Y',
                'PROPERTY_VALUES' => $props
            ];

            if (!$newBrandId = $el->Add($fields)) {
                $this->Logger->log("ERROR", "Ошибка при добавлении нового бренда ".$arItem["VALUE"].
                    " в инфоблок \r\n".print_r($el->LAST_ERROR, true));
            } else {
                $this->Logger->log("LOG", "Бренд ".$arItem["VALUE"]." успешно добавлен");
            }
        }

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * Получить статус обработчика
     *
     * @return bool
     */
    public function CheckStatus() {
        $this->StartDebugTime(__FUNCTION__);
        $this->workerData = $this->workersChecker->Check($this->workerId);
        $this->EndDebugTime(__FUNCTION__);

        if ($this->workerData[$this->workerId]["BUSY"] == 1) {
            return false;
        }

        return true;
    }

    /**
     * Обновить статус обработчика
     * TODO возможно перенести в класс WorkersChecker
     * @param int $status
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function UpdateStatus($status = 0) {
        $this->StartDebugTime(__FUNCTION__);
        $this->workersChecker->UpdateWorkerStatus($this->workerId, $status);

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * Дебаг, замер времени начало
     *
     * @param string $function
     */
    private function StartDebugTime($function = "") {
        if (empty($function)) {
            return;
        }

        $start = microtime(true);
        $this->debugData[$function] = ["Function" => $function, "Start" => $start];
    }

    /**
     * Дебаг, замер времени конец
     *
     * @param string $function
     */
    private function EndDebugTime($function = "") {
        if (empty($function)) {
            return;
        }

        $finish = microtime(true);
        $diff = $finish - $this->debugData[$function]["Start"];
        $diff = round($diff, 4);
        $this->debugData[$function]["Time"] = $diff;
      //  print_r("{$function} - $diff".PHP_EOL);
        return $diff;
    }

    private function getModelName($arItem) {
    /*      if (!empty($this->arCategories[$arItem["category"]["id"]]["singularName"])) {
                $sectionName = $this->arCategories[$arItem["category"]["id"]]["singularName"];
            } else {
                $sectionName = $arItem["category"]["name"];
            }
*/
            $vName = trim(mb_strtolower($arItem["vendor"]["name"]));

            
            $name=trim($arItem["name"]);


            if (!empty($arItem["color"])) {
                $name = trim($name);
                $name .= " ".trim($arItem["color"]);
            }
            if(!empty($vName)) {$name=$name." {$vname}";}
             
/*            if (!empty($arItem["article"]) && ($arItem["article"] != $arItem["name"])) {
                $name = trim($name);
                $name .= " [".trim($arItem["article"])."]";
            }
*/      return $name;
    }

    public function LoadAppCategoryProperties() {
        $this->StartDebugTime(__FUNCTION__);
        $fileName=$_SERVER["DOCUMENT_ROOT"] . "/upload/propertiesCache.json";
        $this->Logger->log("LOG", "Загрузка файла со свойствами разделов {$fileName}");
        $file = file_get_contents($fileName);
        if($file) {
            $this->arCASectionProperties=json_decode($file,true);
            print_r("Загружены свойства для разделов ". implode(",",array_keys($this->arCASectionProperties)).PHP_EOL);
        }
        $this->EndDebugTime(__FUNCTION__);
    }

    public function SaveAppCategoryProperties() {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Сохранение файла со свойствами разделов {$fileName}");
        $fileName=$_SERVER["DOCUMENT_ROOT"] . "/upload/propertiesCache.json";
        file_put_contents($fileName, json_encode($this->arCASectionProperties,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        $this->EndDebugTime(__FUNCTION__);
        

    }

    public function UpdateProduct($id, $arProduct) {
        $existProduct = Product::getCacheItem($id,true);
        /*ID - код товара (элемента каталога - обязательный);
        AVAILABLE - доступность товара к покупке (Y/N, поле обновляется автоматически);

        QUANTITY - количество товара на складе;
        CAN_BUY_ZERO - флаг (Y/N/D)* "разрешить покупку при отсутствии товара";


        PURCHASING_PRICE - закупочная цена. При указании в массиве этого ключа необходимо обязательно указать и валюту PURCHASING_CURRENCY, иначе метод возвратит false.
        PURCHASING_CURRENCY - валюта закупочной цены;

*/
        $arFields=["ID"=>$id,"CAN_BUY_ZERO"=>'Y'];
        if($arProduct['price'] && $arProduct['price']['supplierPrice']) {
            $arFields['AVAILABLE']='Y';
            $arFields['PURCHASING_PRICE']=$arProduct['price']['supplierPrice'];
            $arFields['PURCHASING_CURRENCY']='RUB';
            
            
        }
        if($arProduct['price'] && $arProduct['price']['inStockAmount]']) {
            $arFields['QUANTITY']=$arProduct['price']['inStockAmount]'];
            
            
        }
        $product_id=false;
        if(!empty($existProduct)){
                $res=Product::update(intval($arFields['ID']),$arFields);
                if($res->isSuccess()) {
                    $product_id=$res->getId();
                 //   print_r(PHP_EOL."Продукт обновлен. Id={$res->getId()}".PHP_EOL);
                    
                } else {
                   // print_r($res->getErrorMessages());
                }
        } else {
                $res=Product::add($arFields);
                if($res->isSuccess()) {
                    $product_id=$res->getId();
                    //print_r(PHP_EOL."Продукт добавлен. Id={$res->getId()}".PHP_EOL);
                    
                } else {
                    //print_r($res->getErrorMessages());
                }
        }
        if($product_id && $arProduct['price'] && $arProduct['price']['price']) {
            $dbPrice = Price::getList([
                                "filter" => array(
                                    "PRODUCT_ID" => $product_id,
                                    "CATALOG_GROUP_ID" => 1
                                )
                            ]);
            
            if($priceRes=$dbPrice->fetch()) {
                
          //      print_r("Цена найдена. Обновляем цену. Id={$priceRes['ID']}".PHP_EOL);
                $result = Price::update($priceRes['ID'], ['PRICE'=>$arProduct['price']['price']], true);
                if($result->isSuccess()) {
            //        print_r("Цена обновлена. Id={$result->getId()}".PHP_EOL);
                } else {
                     print_r($result->getErrorMessages());
                }
                                
            } else {
                $arFields=['PRODUCT_ID' => $product_id,
                    'CATALOG_GROUP_ID' => 1,
                    'PRICE' => $arProduct['price']['price'],
                    'CURRENCY' => 'RUB'];
                $result = Price::add($arFields);
                if($result->isSuccess()) {
              //      print_r("Цена добавлена. Id={$result->getId()}".PHP_EOL);
                } else {
                     print_r($result->getErrorMessages());
                }
            }
        }
    }
    
    private function GetSuppliers() {
        $this->Logger->log("LOG", "Получаем данные о поставщиках");
        $this->StartDebugTime(__FUNCTION__);

        if (empty($this->arSuppliers)) {
            $arSuppliers = $this->restAPI->GetSuppliers();

            foreach ($arSuppliers as $arItem) {
                $this->arSuppliers[$arItem["id"]] = $arItem;
            }
        }

        $this->Logger->log("LOG", "Всего получено: ".count($this->arSuppliers));

        $this->EndDebugTime(__FUNCTION__);
    }

}


(new UpdateWorker())->StartWorker();
