<?
if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../../../..');
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use ImarketHeplers\ImarketLogger,
    Bitrix\Main\Application;

if (!class_exists("ImarketHeplers\ImarketLogger")) {
    require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/ImarketLogger.php');
}

require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/RestAPI.php');
require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/ImarketSectionsConnections.php');
require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/workers/WorkersChecker.php');
ini_set('memory_limit', '25600M'); // много кушает оперативки!!!!
set_time_limit(0);


class UpdatePropertiesWorker {
    private $Logger; // класс для логирования
    private $connection; // подключение к БД
    private $restAPI; // класс api rest-а
    public $debugData = []; // данные для дебага
    private $workersChecker; // класс для работы с обработчиками
    private $workerData = []; // данные об обработчиках
    private $workerId = 'update_properties'; // id обработчика в табице worker_busy
    private $arModels = []; // товары из catalogApp
    private $arModelsByXML_ID = []; // товары из catalogApp
    private $arCatalogDataByXML_ID = []; // весь каталог сайта, где ключ xml_id элемента
    private $arCatalogDataById = []; // весь каталог сайта, где ключ id элемента
    private $arCAProperties = [
        "CA_ID" => [
            "NAME" => "ID в catalog.app",
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "CA_ID",
            "PROPERTY_TYPE" => "N",
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
    private $arCASiteProperties = []; // массив с созданными свойствами только для catalog.app
    private $catalogIblockId = 0;
    private $arCASectionProperties = []; // свойства из catalog.app по разделам
    private $sectionConnections = []; // связь разделов catalog.app и сайта
    private $arCASettings = [];

    public function __construct () {
        $this->Logger = new ImarketLogger("/upload/log/UpdatePropertiesWorker/");
        $this->connection = Application::getConnection();

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
    }

    public function StartWorker() {
        $this->Logger->log("LOG", "Начало обработки");
        $this->StartDebugTime(__FUNCTION__);
                $this->UpdateStatus(0);
        // проверить статус обработчика
        if (!$this->CheckStatus()) {
            $this->Logger->log("LOG", "Не нужно обрабатывать");
            return false;
        }

        // проставить статус
        $this->UpdateStatus(1);

        // получить каталог из catalogApp
        $this->GetCatalogModels();
        // получить товары сайта
        $this->GetCatalogGoods();
        // проверить, есть ли основные свойства
        $this->PrepareMainProperties();
        // получить связи разделов сайта и catalog.app
        $this->GetSectionsConnections();
        // получить свойства каталога сайта
        $this->GetCatalogProperties();
        // обновить свойства товаров
        $this->UpdateProperties();

        // обновить статус
        $this->UpdateStatus(0);

        $this->Logger->log("LOG", "Обработка закончена");

        $this->EndDebugTime(__FUNCTION__);
    }


    public function GetSectionsConnections() {
        $this->Logger->log("LOG", "Получаем сопоставления разделов");
        $this->StartDebugTime(__FUNCTION__);

        $sConnect = new ImarketSectionsConnections();
        $this->sectionConnections = $sConnect->getAll();

        $this->Logger->log("LOG", "Данные получены, всего сопоставленных разделов ".count($this->sectionConnections));

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * получить все модели из catalogApp
     */
    private function GetCatalogModels() {
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

        foreach ($this->arModels as $k => $arModel) {
            $this->arModelsByXML_ID[$arModel["externalId"]] = $arModel;
        }

        $this->Logger->log("LOG", "Всего получено моделей ".count($this->arModels));

        $this->EndDebugTime(__FUNCTION__);
    }

    /**
     * Получаем все товары каталога, ключ xml_id
     *
     */
    private function GetCatalogGoods() {
        $this->Logger->log("LOG", "Получаем товары каталога");
        $this->StartDebugTime(__FUNCTION__);

        $filter = ['IBLOCK_ID' => $this->catalogIblockId, "PREVIEW_PICTURE" => false];
        $select = ['ID', 'NAME', 'XML_ID'];
        $dbl = CIBlockElement::GetList(["ID" => "ASC"], $filter, false, ["nPageSize" => 2], $select);
        while ($arItem = $dbl->Fetch()) {
            if(!empty($arItem["XML_ID"]) && $arItem["XML_ID"] != $arItem["ID"]) {
                $this->arCatalogDataByXML_ID[$arItem["XML_ID"]] = $arItem;
            }

            $this->arCatalogDataById[$arItem["ID"]] = $arItem;
        }

        $this->Logger->log("LOG", "Получено, всего товаров: ".count($this->arCatalogDataByXML_ID));
        $this->EndDebugTime(__FUNCTION__);
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

        $this->EndDebugTime(__FUNCTION__);
    }

    private function GetCatalogProperties() {
        $this->Logger->log("LOG", "Получаем свойства каталога");
        $this->StartDebugTime(__FUNCTION__);

        $arItem["IBLOCK_ID"] = $this->catalogIblockId;
        $properties = CIBlockProperty::GetList(["name" => "asc"], ["IBLOCK_ID" => $this->catalogIblockId]);
        while($arProp = $properties->GetNext()) {
            $this->arCASiteProperties[$arProp["CODE"]] = $arProp;

            if ($arProp["PROPERTY_TYPE"] == "L") {
                $property_enums = CIBlockPropertyEnum::GetList(
                    ["SORT" => "ASC"],
                    ["IBLOCK_ID" => $this->catalogIblockId, "CODE" => $arProp["CODE"]]
                );
                while ($arEnum = $property_enums->GetNext()) {
                    $this->arCASiteProperties[$arProp["CODE"]]["ENUMS"][$arEnum["XML_ID"]] = $arEnum;
                }
            }
        }

        $this->Logger->log("LOG", "Всего получено ".count($this->arCASiteProperties));
        $this->EndDebugTime(__FUNCTION__);
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
                $this->Logger->log("LOG", "Добавляем привязку свойства ".$this->arCASiteProperties[$property["CODE"]]["NAME"]." к разделу ".$this->arCASectionProperties[$sectionId]["NAME"]);

                $sql = "INSERT INTO b_iblock_section_property (`IBLOCK_ID`, `SECTION_ID`, `PROPERTY_ID`, `SMART_FILTER`) VALUES 
                        (".$this->catalogIblockId.", ".$this->sectionConnections[$sectionId].", ".$propertyID.", 'N')";

                $this->Logger->log("LOG", $sql);

                $this->connection->query($sql);
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

        if ($ibpenum->Add($arFields)) {
            $this->Logger->log("LOG", "Значение добавлено");
        } else {
            $this->Logger->log("ERROR", "Ошибка при добавлении значения свойства \r\n".print_r($ibpenum->LAST_ERROR, true));
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

        $sql = "SELECT SECTION_ID FROM b_iblock_section_property WHERE SECTION_ID = ".$this->sectionConnections[$sectionId]." AND PROPERTY_ID = ".$propertyId." AND IBLOCK_ID = ".$this->catalogIblockId."";
        $res = $this->connection->query($sql);
        if (!$arItem = $res->fetch()) {
            $this->Logger->log("LOG", "Добавляем привязку свойства к разделу ".$this->arCASectionProperties[$sectionId]["NAME"]);

            $sql = "INSERT INTO b_iblock_section_property (`IBLOCK_ID`, `SECTION_ID`, `PROPERTY_ID`, `SMART_FILTER`) VALUES 
                        (".$this->catalogIblockId.", ".$this->sectionConnections[$sectionId].", ".$propertyId.", 'N')";

            file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/log/UpdatePropertiesWorker/s_.txt", serialize($this->sectionConnections));
            $this->connection->query($sql);
            $this->Logger->log("LOG", "Добавление свойства к разделу : " . $sql);
        }

        $this->EndDebugTime(__FUNCTION__);
    }

    private function UpdateProperties() {
        $this->Logger->log("LOG", "Получаем свойства товаров");
        $this->StartDebugTime(__FUNCTION__);
        $el = new CIBlockElement;
        $arActivationRules = [
            "IMAGES" => false,
            "PROPERTIES" => false
        ];

        $counter = 0;

        if (empty($this->arModels) || empty($this->arCatalogDataByXML_ID)) {
            $this->Logger->log("LOG", "Нет товаров для обработки");
            $this->EndDebugTime(__FUNCTION__);
            return false;
        }

        $this->Logger->log("LOG", "Всего элементов для обновления: " . count($this->arCatalogDataByXML_ID));
        $empty=0;
        $limit=5000;
        foreach ($this->arCatalogDataByXML_ID as $xmlId => $arItem) {
            print_r("{$xmlId}".PHP_EOL);
          
            if (empty($this->arModelsByXML_ID[$xmlId])) {
                continue;
            }
          
            //print_r($xmlId.PHP_EOL);
            //print_r($this->arModelsByXML_ID[$xmlId]);
            $arModelProperties = $this->restAPI->getModelProperties($this->arModelsByXML_ID[$xmlId]["id"]);
            print_r($arModelProperties);
            
            break;
            if($empty==$limit) break;
            print_r("{$empty} ".$this->arModelsByXML_ID[$xmlId]['name'].' - '.$arModelProperties['externalId'].PHP_EOL);
            $this->Logger->log("LOG",$arModelProperties);
            ob_flush();
            if (!empty($arModelProperties["ERROR"])) {
                $this->Logger->log("ERROR", "Ошибка при получении свойств! ".$arModelProperties["ERROR"]);
                continue;
            }
            
            if(empty($arModelProperties["images"])){
                $empty++;
                continue;
            }

            
            $modelSectionId = $arModelProperties["category"]["id"];
            if (empty($this->arCASectionProperties[$modelSectionId])) {
                $arSectionPropTMP = $this->restAPI->getCategoryById($modelSectionId);

                if (!empty($arSectionPropTMP["ERROR"])) {
                    $this->Logger->log("ERROR", "Ошибка при получении свойств раздела! ".$arSectionPropTMP["ERROR"]." : #".$arModelProperties['id'], $arModelProperties);
                } else {
                    $this->arCASectionProperties[$modelSectionId]["ID"] = $arSectionPropTMP["id"];
                    $this->arCASectionProperties[$modelSectionId]["NAME"] = $arSectionPropTMP["name"];

                    foreach ($arSectionPropTMP["properties"] as $arProp) {
                        $this->arCASectionProperties[$modelSectionId]["PROPERTIES"][$arProp["id"]] = $arProp;
                    }

                    $arSectionPropTMP = [];
                }
            }
            

            $arLoadProductArray = [];
            $PROP = [];
            $PROP["CA_ID"] = $arModelProperties["id"];
            try {

                if (!empty($arModelProperties["images"])) {
                    print_r($xmlId.PHP_EOL);
                    print_r($this->arModelsByXML_ID[$xmlId]);
                
                    print_r($this->arModelsByXML_ID[$xmlId]['name'].' - '.$arModelProperties['externalId'].PHP_EOL);
                    break;
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

            } catch(Exception $e) {
                //echo 'Message: ' .$e->getMessage();
            }

            
            if (empty($this->arCASiteProperties["CA_BRAND"]["ENUMS"][$this->arModelsByXML_ID[$xmlId]["vendor"]["id"]])) {
                $enumData = ['id' => $this->arModelsByXML_ID[$xmlId]["vendor"]["id"], "value" => $this->arModelsByXML_ID[$xmlId]["vendor"]["name"]];

                $this->CreatEnum($this->arCASiteProperties["CA_BRAND"], $enumData, true);
            }

            $PROP["CA_BRAND"] = $this->arCASiteProperties["CA_BRAND"]["ENUMS"][$this->arModelsByXML_ID[$xmlId]["vendor"]["id"]];

            $goodsPropertiesCount = 0;
            if(!empty($arModelProperties["propertyValues"]))  {
                foreach ($arModelProperties["propertyValues"] as $arProp) {
                    $goodsPropertiesCount++;
                    $property = $this->arCASectionProperties[$modelSectionId]["PROPERTIES"][$arProp["definitionId"]];
                    $property["code"] = "CA_".translit(preg_replace('/[^a-zA-Zа-яА-Я0-9]/ui', '', $property["name"]));
                    $property["code"] = strtoupper($property["code"]);
                    $property["code"] .= "_".$property["id"];

                    if (!empty($this->arCASiteProperties[$property["code"]]["ID"])) {
                        $this->CheckPropertySection($modelSectionId, $this->arCASiteProperties[$property["code"]]["ID"]);
                    }

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

                            if ($this->arCASiteProperties[$property["code"]]["TYPE"] != "N") {
                                $this->DeleteProperty($this->arCASiteProperties[$property["code"]]["ID"]);
                            }

                            if (empty($this->arCASiteProperties[$property["code"]])) {
                                $this->CreateProperty($arPropData, $modelSectionId);
                                $this->Logger->log("LOG", $arPropData);
                            }

                            $PROP[$this->arCASiteProperties[$property["code"]]["ID"]] = ["VALUE" => $arProp["decimalValue"], "DESCRIPTION" => $property["unit"]];

                            break;
                        case "String":

                            if ($this->arCASiteProperties[$property["code"]]["TYPE"] != "S") {
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

                            if ($this->arCASiteProperties[$property["code"]]["TYPE"] != "L") {
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

                            if ($this->arCASiteProperties[$property["code"]]["TYPE"] != "L") {
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

                            if ($this->arCASiteProperties[$property["code"]]["TYPE"] != "L") {
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

            $this->Logger->log("LOG", "Обновляем элемент: " . $arItem["ID"], $arLoadProductArray);

            if (!$el->Update($arItem["ID"], $arLoadProductArray)) {
                $this->Logger->log("ERROR", "Ошибка при обновлении свойст товара \r\n".print_r($el->LAST_ERROR, true), $arLoadProductArray);
            }
            break;
        }


        $this->Logger->log("LOG", "Всего обновлено товаров: ".$counter);
        $this->EndDebugTime(__FUNCTION__);
    }


    /**
     * Получить статус обработчика
     *
     * @return bool
     */
    private function CheckStatus() {
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
    private function UpdateStatus($status = 0) {
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
    }
}

(new UpdatePropertiesWorker())->StartWorker();
