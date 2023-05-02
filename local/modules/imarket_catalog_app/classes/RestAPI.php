<?
if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/../..');
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/catalogAppAPI.php');

use ImarketHeplers\ImarketLogger,
    Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

define(CATALOG_IBLOCK_ID, 128);
if (!class_exists("ImarketHeplers\ImarketLogger")) {
    require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/ImarketLogger.php');
}

require($_SERVER['DOCUMENT_ROOT'] . '/local/modules/imarket_catalog_app/classes/ImarketTriggers.php');


class RestAPI {
    private $Logger;
    private $connection;
    private $catalogAppAPI;
    private $pricingProfiles;
    private $catalogId = 0;
    public $debugData = [];
    private $uploadTable = 'catalog_app_data';
    private $arOurPricingProfiles = [];

    public function __construct () {
        $this->Logger = new ImarketLogger("/upload/log/RestAPI/");
        $this->connection = Application::getConnection();
        $this->catalogAppAPI = new catalogAppAPI();

        $sql = "SELECT * FROM catalog_app_settings";
        $res = $this->connection->query($sql);
        $arResult["SETTINGS"] = $res->fetch();

        if (!empty($arResult["SETTINGS"]["CATALOG_APP_CATALOG_ID"])) {
            $this->catalogId = $arResult["SETTINGS"]["CATALOG_APP_CATALOG_ID"];
        }

        $sql = "SELECT * FROM catalog_app_rules";
        $res = $this->connection->query($sql);
        while($arItem = $res->fetch()) {
            $arResult["RULES"][] = $arItem;
            $this->arOurPricingProfiles[$arItem["CATALOG_APP_ID"]] = $arItem["SITE_PROFILE_ID"];
        }
    }

    public function CheckCompletedTacks() {
        $this->Logger->log("LOG", "Проверяем завершенные задачи CatalogApp");
        $arTasks = [];
        $arTaskInfo = [];

        $this->StartDebugTime(__FUNCTION__);

        $sql = "SELECT TASK_ID FROM catalog_app_tasks WHERE NEED_START = 1";
        $res = $this->connection->query($sql);
        while($arItem = $res->fetch()) {
            $arTasks[] = $arItem;
        }

        if (!empty($arTasks)) {
            foreach ($arTasks as $arItem) {
                $info = $this->GetCompletedTaskInfo($arItem["TASK_ID"]);

                if (!empty($info)) {
                    $arTaskInfo[$info["pricingProfile"]["id"]] = $info;
                }
            }
        }

        $this->EndDebugTime(__FUNCTION__);

        return $arTaskInfo;
    }

    public function UpdateCompletedTaskStatus($taskId = 0, $status = 0) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Обновляем статус задачи ".$taskId);

        $sql = "UPDATE catalog_app_tasks SET NEED_START = ".$status.", UPDATE_DATE = '".date("d.m.Y H:i:s")."' WHERE TASK_ID = ".$taskId;
        $this->connection->query($sql);

        $this->Logger->log("LOG", "Статус задачи ".$taskId." обновлен");

        $this->EndDebugTime(__FUNCTION__);
    }

    private function GetCompletedTaskInfo($taskId = 0) {
        $this->Logger->log("LOG", "Получаем информацию по завершенной задаче CatalogApp ".$taskId);
        $this->StartDebugTime(__FUNCTION__);

        $arrTaskInfo = $this->catalogAppAPI->getPricingTaskInfo($taskId);

        $this->EndDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получили информацию о задаче ".$taskId);
        return $arrTaskInfo;
    }

    /**
     * Получаем профили ценообразования
     * TODO: В дальнейшем хранить профили у себя
     */
    public function GetPricingProfiles() {
        $this->Logger->log("LOG", "Получаем профили ценообразования");
        $this->pricingProfiles = $this->catalogAppAPI->getPricingProfiles();

        if (empty($this->pricingProfiles)) {
            $this->Logger->log("ERROR", "Не получили профили ценообразования");
        }
    }

    /**
     * Получаем товары по профилям
     *
     * @param int $pricingProfiles
     */
    public function GetProfilePrices($pricingProfiles = []) {
        $this->Logger->log("LOG", "Получаем товары по профилям");
        $arData = [];

        if (!empty($pricingProfiles)) {
            $this->arOurPricingProfiles = $pricingProfiles;
        }

        if (!empty($this->arOurPricingProfiles)) {
            $limit = 10000;

            foreach ($this->arOurPricingProfiles as $profileId => $imarketProfileId) {
                $this->Logger->log("LOG", "Получаем данные для профиля ".$profileId);
                $offset = 0;
                $iteration = 0;
                $lastCatalogAppId = 0;

                do {
                    $profileRequestResult = $this->catalogAppAPI->getProfilePrices($this->catalogId, $profileId, $offset, $limit, $lastCatalogAppId);

                    if (!empty($arProfileData[$imarketProfileId])) {
                        $arProfileData[$imarketProfileId] = array_merge($arProfileData[$imarketProfileId], $profileRequestResult);
                    } else {
                        $arProfileData[$imarketProfileId] = $profileRequestResult;
                    }

                    if ($offset == 0) {
                        $offset = $limit + 1;
                    } else {
                        $offset = $limit * $iteration + 1;
                    }

                    $iteration++;
                    if ($lastCatalogAppId != $profileRequestResult[count($profileRequestResult) - 1]["id"]) {
                        $lastCatalogAppId = $profileRequestResult[count($profileRequestResult) - 1]["id"];
                    } else {
                        $profileRequestResult = [];
                    }

                    $this->Logger->log("LOG", "Всего обработано: ".count($arProfileData[$imarketProfileId]));
                } while (!empty($profileRequestResult) && is_array($profileRequestResult));
            }

            if (!empty($arProfileData)) {
                foreach ($arProfileData as $profileId => $arCatalogData) {
                    foreach ($arCatalogData as $arItem) {
                        $arItem["model"]["id"] = $this->PrepareCatalogAppId($arItem["model"]["id"]);

                        $arrCatalogAppIds[] = $arItem["model"]["id"];
                        $arrCatalogAppId_to_XML_ID[$arItem["model"]["id"]] = $arItem["model"]["externalId"];
                    }
                }

                if (!empty($arrCatalogAppIds)) {
//                    $arrNeedAddCatalogAppId = $this->CheckCatalogAppId($arrCatalogAppIds);

//                    if (!empty($arrNeedAddCatalogAppId)) {
//                        foreach ($arrNeedAddCatalogAppId as $id) {
//                            $this->SetCatalogAppID($arrCatalogAppId_to_XML_ID[$id]);
//                        }
//                    }
                }

                $this->Logger->log("LOG", "Формируем данные");
                foreach ($arProfileData as $profileId => $arCatalogData) {
                    foreach ($arCatalogData as $arItem) {
                        $arItem["nPCatalogAppId"] = $arItem["model"]["id"];
                        $arItem["model"]["id"] = $this->PrepareCatalogAppId($arItem["model"]["id"]);

                        $arData[$arItem["model"]["externalId"]] = [
                            'id' => $arItem["model"]["externalId"], // xml_id элемента
                            'siteId' => '', // id элемента на сайте
                            'deliveryTime' => $arItem["deliveryTime"], // время доставки
                            'deliveryCountryTime' => $arItem["deliveryCountryTime"], // время доставки по стране
                            'deliveryTownPrice' => $arItem["deliveryTownPrice"], // стоимосто доставки в минске
                            'deliveryCountryPrice' => $arItem["deliveryCountryPrice"], // стоимость доставки по стране
                            'installmentPrice' => $arItem["installmentPrice"], // цена в рассрочку
                            'maxInstallmentCost' => $arItem["maxInstallmentCost"], // от какой цены применять возможную рассрочку
                            'inStockAmount' => $arItem["inStockAmount"], // количество на складе
                            'minRetailPrice' => $arItem["model"]["minRetailPrice"], // МРЦ
                            'categoryId' => $arItem["model"]["categoryId"], // id категории в каталог апп
                            'category' => '', // название категории в каталог апп
                            'vendor' => $arItem["model"]["vendor"], // брэнд
                            'model' => $arItem["model"]["modelName"], // модель
                            'article' => '', // артикул
                            'price' => $arItem["price"], // цена
                            'originalPrice' => $arItem["originalPrice"], // цена закупки
                            'currency' => '', // валюта
                            'comment' => '', // какой то комментарий
                            'producer' => '', // произваодитель
                            'importer' => '', // импортер
                            'serviceCenters' => '', // сервисный центр
                            'warranty' => '', // гарантия
                            'productLifeTime' => '', // срок службы
                            'color' => '', // цвет
                            'ean' => '', // еан
                            'profileId' => $profileId, // id профиля на сайте
                            'nPCatalogAppId' => $arItem["nPCatalogAppId"], // id каталог апп
                            'catalogAppId' => $arItem["model"]["id"], // id каталог апп
                            'profit' => $arItem["price"] - $arItem["supplierPrice"], // профит
                            'supplierId' => $arItem["supplierId"], // id поставщика
                            'exportAttributeId' => $arItem["exportAttributeId"], // id атрибутов экспорта, производитель, сервисный центр и т.д.
                        ];
                    }
                }
            }
        }

        $this->Logger->log("LOG", "Всего сформировано: ".count($arData));

        return $arData;
    }

    /**
     * Проверяет товары в которых нет id catalog app из переданного массива
     *
     * @param array $arGoodsIds
     * @return array|void
     */
    public function CheckCatalogAppId ($arGoodsIds = []) {
        $this->Logger->log("LOG", "Проверяем есть ли id catalogApp у товаров");

        if (empty($arGoodsIds)) {
            $this->Logger->log("LOG", "Нет товаров для проверки");
            return;
        }

        $this->StartDebugTime(__FUNCTION__);

        $arrNeeded = $arGoodsIds;
        $arExistId = [];
        $arrNoCatalogAppId = [];

        $chunks = array_chunk($arGoodsIds, 50000);
        foreach ($chunks as $chunk) {
            $arSelect = ["ID", "PROPERTY_CATALOG_APP_ID"];
            $arFilter = ["IBLOCK_ID" => CATALOG_IBLOCK_ID, "PROPERTY_CATALOG_APP_ID" => $chunk];
            $rsResCat = CIBlockElement::GetList(Array("ID" => "ASC"), $arFilter, false, false, $arSelect);
            while($arItem = $rsResCat->GetNext()) {
                $arExistId[] =  $arItem["PROPERTY_CATALOG_APP_ID_VALUE"];
            }
        }

        $arrNoCatalogAppId = array_diff($arrNeeded, $arExistId);
        $this->Logger->log("LOG", "Товаров без catalogApp id: ".count($arrNoCatalogAppId));

        $this->EndDebugTime(__FUNCTION__);

        return $arrNoCatalogAppId;
    }

    public function CheckLine() {
    }

    public function GetCatalogAppCatalog() {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем весь каталог из выгрузки");

        $arData = [];
        $sql = "SELECT * FROM ".$this->uploadTable;
        $res = $this->connection->query($sql);
        while ($arItem = $res->fetch()) {
            $arData[$arItem["SITE_PROFILE_ID"]][$arItem["CATALOG_APP_ID"]] = $arItem;
        }

        $this->Logger->log("LOG", "Получено, всего данных ".count($arData));
        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }

    public function GetExportAttributes($profileId = 0) {
        $this->Logger->log("LOG", "данные по поставщикам, сервисным центрам...");
        $arData = [];

        $this->Logger->log("LOG", "Получаем данные для профиля ".$profileId);
        $offset = 0;
        $iteration = 0;
        $limit = 10000;

        do {
            $profileRequestResult = $this->catalogAppAPI->GetExportAttributes($this->catalogId, $profileId, $offset, $limit);

            if (!empty($arProfileData)) {
                $arProfileData = array_merge($arProfileData, $profileRequestResult);
            } else {
                $arProfileData = $profileRequestResult;
            }

            if ($offset == 0) {
                $offset = $limit + 1;
            } else {
                $offset = $limit * $iteration + 1;
            }

            $iteration++;
            $this->Logger->log("LOG", "Всего обработано: ".count($arProfileData));
        } while (!empty($profileRequestResult) && is_array($profileRequestResult));

        $this->Logger->log("LOG", "Всего получено: ".count($arProfileData));

        return $arProfileData;
    }

    public function GetSuppliers($id = 0) {
        $this->Logger->log("LOG", "Получаем данные о поставщиках");
        $offset = 0;
        $iteration = 0;
        $limit = 10000;

        do {
            $profileRequestResult = $this->catalogAppAPI->getSuppliers($this->catalogId, $offset, $limit, $id);

            if (!empty($arProfileData)) {
                $arProfileData = array_merge($arProfileData, $profileRequestResult);
            } else {
                $arProfileData = $profileRequestResult;
            }

            if ($offset == 0) {
                $offset = $limit + 1;
            } else {
                $offset = $limit * $iteration + 1;
            }

            $iteration++;
            $this->Logger->log("LOG", "Всего обработано: ".count($arProfileData));
        } while (!empty($profileRequestResult) && is_array($profileRequestResult));

        $this->Logger->log("LOG", "Всего получено: ".count($arProfileData));

        return $arProfileData;
    }

    
        /**
     * Получение товаров из catalogApp
     */
    public function GetLimitModels ($xml_id=false,$offset=0,$limit=50) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем товары из catalogApp");
        
        $iteration = 0;
        $arData = [];
        $lastCatalogAppId = 0;
        $ImarketTriggers = new ImarketTriggers();

  //      do {
            $profileRequestResult = $this->catalogAppAPI->getModels($xml_id, '', $offset, $limit, $lastCatalogAppId);

            if (!empty($profileRequestResult["ERROR"])) {
                $this->Logger->log("ERROR", "Ошибка при получении данных о товаре ".print_r($profileRequestResult["ERROR"], true));
                $ImarketTriggers->SetError(["Проблемы с получением моделей из CatalogApp: \r\n".$profileRequestResult["ERROR"]."\r\n".__FILE__]);
                $ImarketTriggers->SendTriggerErrors();
                return;
            }

            if (!empty($profileRequestResult)) {

                    $arData = $profileRequestResult;
            }

         //   print_r("Получено: ".count($arData).PHP_EOL);
            ob_flush();
            $this->Logger->log("LOG", "Получено: ".count($arData));
//        } while (!empty($profileRequestResult) && is_array($profileRequestResult));

        $this->Logger->log("LOG", "Всего товаров получено: ".count($arData));

        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }
    
            /**
     * Получение товаров из catalogApp
     */
    public function GetLimitPriceModels($xml_id=false,$offset=0,$limit=50) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем товары из catalogApp");
        
        $iteration = 0;
        $arData = [];
 
      //  $ImarketTriggers = new ImarketTriggers();

  //      do {
            $profileRequestResult = $this->catalogAppAPI->getPriceModels($offset, $limit,1);

            if (!empty($profileRequestResult["ERROR"])) {
                $this->Logger->log("ERROR", "Ошибка при получении данных о товаре ".print_r($profileRequestResult["ERROR"], true));
              //  $ImarketTriggers->SetError(["Проблемы с получением моделей из CatalogApp: \r\n".$profileRequestResult["ERROR"]."\r\n".__FILE__]);
               // $ImarketTriggers->SendTriggerErrors();
                return;
            }

            if (!empty($profileRequestResult)) {

                foreach ($profileRequestResult as $price) {
                    if($price['model']) {
                        
                        $model=$price['model'];
                        unset($price['model']);
                        $model['price']=$price;
                        $arData[] = $model;
                    }
                    
                }
                    
            }

         //   print_r("Получено: ".count($arData).PHP_EOL);
            ob_flush();
            $this->Logger->log("LOG", "Получено: ".count($arData));
         //   print_r($arData);
//        } while (!empty($profileRequestResult) && is_array($profileRequestResult));

        $this->Logger->log("LOG", "Всего товаров получено: ".count($arData));

        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }
    
    public function GetCacheModels ($xml_id = '',$offset=0,$limit=10000) {
        $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/upload/log/UpdatePropertiesWorker/debugData_.txt");
        $models=json_decode($file,true);
        foreach ($models as $key => $model) {
            if(!$model['externalId']) {
                $models[$key]['externalId']='ca'.$model['id'];
         //       print_r($models[$key]['externalId'].PHP_EOL);
           //     ob_flush();
            }
            
        }
        return $models;
    }

    public function GetModifiedModels ($xml_id = '',$offset=0,$limit=1000) {
  //      $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/upload/log/UpdatePropertiesWorker/debugData_.txt");
  //      $models=json_decode($file,true);
        
        $last_update=Option::get('imarket_catalog_app', "last_update",0);
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем товары из catalogApp");
        $models = [];

        $models = $this->catalogAppAPI->getModifiedModels($xml_id,$offset,$limit,$last_update);


        print_r("Получено: ".count($models).PHP_EOL);
        ob_flush();

        $this->Logger->log("LOG", "Всего товаров получено: ".count($models));

        $this->EndDebugTime(__FUNCTION__);
        return $models;
    }    
    
    public function GetModifiedCards ($xml_id = '',$offset=0,$limit=1000) {
  //      $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/upload/log/UpdatePropertiesWorker/debugData_.txt");
  //      $models=json_decode($file,true);
        
        $last_update=Option::get('imarket_catalog_app', "last_update",0);
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем товары из catalogApp");
        $models = [];

        $models = $this->catalogAppAPI->getModifiedCards($xml_id,$offset,$limit,$last_update);


        print_r("Получено: ".count($models).PHP_EOL);
        ob_flush();

        $this->Logger->log("LOG", "Всего товаров получено: ".count($models));

        $this->EndDebugTime(__FUNCTION__);
        return $models;
    }  
    
    public function SaveLastUpdate($a=false, $b=false,$c=false) {
        Option::set('imarket_catalog_app', "last_update",date("Y-m-d"));
        Option::set('imarket_catalog_app', "models_offset[GetModifiedModels]",0);
        Option::set('imarket_catalog_app', "models_offset[GetModifiedCards]",0);
        return false;
    }
    
    /**
     * Получение товаров из catalogApp
     */
    public function GetModels ($xml_id = '',$offset=0,$limit=10000) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем товары из catalogApp");
        $iteration = 0;
        $arData = [];
        $lastCatalogAppId = 0;
        $ImarketTriggers = new ImarketTriggers();

        do {
            $profileRequestResult = $this->catalogAppAPI->getModels($xml_id, '', $offset, $limit, $lastCatalogAppId);

            if (!empty($profileRequestResult["ERROR"])) {
                $this->Logger->log("ERROR", "Ошибка при получении данных о товаре ".print_r($profileRequestResult["ERROR"], true));
                $ImarketTriggers->SetError(["Проблемы с получением моделей из CatalogApp: \r\n".$profileRequestResult["ERROR"]."\r\n".__FILE__]);
                $ImarketTriggers->SendTriggerErrors();
                return;
            }

            if (!empty($profileRequestResult)) {
                if (!empty($arData)) {
                    $arData = array_merge($arData, $profileRequestResult);
                } else {
                    $arData = $profileRequestResult;
                }
            }

            $iteration++;
            if ($offset == 0) {
                $offset = $limit;
            } else {
                $offset = $limit * $iteration;
            }

            if (!empty($xml_id)) {
                $profileRequestResult = [];
            }

            if ($lastCatalogAppId != $profileRequestResult[count($profileRequestResult) - 1]["id"]) {
                $lastCatalogAppId = $profileRequestResult[count($profileRequestResult) - 1]["id"];
            } else {
                $profileRequestResult = [];
            }
            print_r("Получено!: ".count($arData).PHP_EOL);
            ob_flush();
            $this->Logger->log("LOG", "Получено: ".count($arData));
        } while (!empty($profileRequestResult) && is_array($profileRequestResult));

        $this->Logger->log("LOG", "Всего товаров получено: ".count($arData));

        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }

    /**
     * Получение брендов из catalogApp
     */
    public function GetVendors ($id = 0) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем брендов из catalogApp");
        $offset = 0;
        $iteration = 0;
        $limit = 10000;
        $arData = [];
        $ImarketTriggers = new ImarketTriggers();

        do {
            $profileRequestResult = $this->catalogAppAPI->getVendors(317, 0, $offset, $limit, $id);

            if (!empty($profileRequestResult["ERROR"])) {
                $this->Logger->log("ERROR", "Ошибка при получении данных ".print_r($profileRequestResult["ERROR"], true));
                $ImarketTriggers->SetError(["Проблемы с получением брендов из CatalogApp: \r\n".$profileRequestResult["ERROR"]."\r\n".__FILE__]);
                $ImarketTriggers->SendTriggerErrors();
                return false;
            }

            if (!empty($profileRequestResult)) {
                if (!empty($arData)) {
                    $arData = array_merge($arData, $profileRequestResult);
                } else {
                    $arData = $profileRequestResult;
                }
            }

            $iteration++;
            if ($offset == 0) {
                $offset = $limit;
            } else {
                $offset = $limit * $iteration;
            }
            $this->Logger->log("LOG", "Получено: ".count($arData));
        } while (!empty($profileRequestResult) && is_array($profileRequestResult));

        $this->Logger->log("LOG", "Всего получено: ".count($arData));

        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }

    /**
     * Получение разделов из catalogApp
     */
    public function GetCategories ($id = 0) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем категории из catalogApp");
        $offset = 0;
        $iteration = 0;
        $limit = 10000;
        $arData = [];
        $ImarketTriggers = new ImarketTriggers();

        do {
            $profileRequestResult = $this->catalogAppAPI->getCategories(317, 0, $offset, $limit, $id);

            if (!empty($profileRequestResult["ERROR"])) {
                $this->Logger->log("ERROR", "Ошибка при получении данных ".print_r($profileRequestResult["ERROR"], true));
                $ImarketTriggers->SetError(["Проблемы с получением разделов из CatalogApp: \r\n".$profileRequestResult["ERROR"]."\r\n".__FILE__]);
                $ImarketTriggers->SendTriggerErrors();
                return false;
            }

            if (!empty($profileRequestResult)) {
                if (!empty($arData)) {
                    $arData = array_merge($arData, $profileRequestResult);
                } else {
                    $arData = $profileRequestResult;
                }
            }

            $iteration++;
            if ($offset == 0) {
                $offset = $limit;
            } else {
                $offset = $limit * $iteration;
            }
            $this->Logger->log("LOG", "Получено: ".count($arData));
        } while (!empty($profileRequestResult) && is_array($profileRequestResult));

        $this->Logger->log("LOG", "Всего получено: ".count($arData));

        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }

    public function GetBankInstallments($profileId = 0) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем банковские рассрочки");
        $arData = [];

        $arData = $this->catalogAppAPI->getInstallments($this->catalogId, $profileId);

        $this->Logger->log("LOG", "Всего получено: ".count($arData));
        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }

    public function getModelProperties($modelId = 0,$get_model=true) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем свойства товара");

        if (empty($modelId)) {
            $this->Logger->log("ERROR", "Нет id модели");
            return $this->Logger->lastError;
        }

        $arData = $this->catalogAppAPI->getProductProps($modelId,$get_model);

        $this->Logger->log("LOG", "Всего получено: ".count($arData));
        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }

    public function getCategoryById($sectionId = 0,$includeParentProps=false) {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем раздел");

        if (empty($sectionId)) {
            $this->Logger->log("ERROR", "Нет id раздела");
            return $this->Logger->lastError;
        }

        $arData = $this->catalogAppAPI->getCategoryById($this->catalogId, $sectionId,$includeParentProps);

        $this->Logger->log("LOG", "Раздел получен");
        $this->EndDebugTime(__FUNCTION__);

        return $arData;
    }

    #Helpers#
    public function PrepareCatalogAppId($id = 0) {
        if (empty($id)) {
            return false;
        }

        $this->StartDebugTime(__FUNCTION__);
        $newId = $id;

        $idLen = strlen($id);
        if ($idLen < 7) {
            $newId = '';

            for ($i = 0; $i < (7 - $idLen); $i++) {
                $newId .= '0';
            }

            $newId .= $id;
        }

        $this->EndDebugTime(__FUNCTION__);

        return $newId;
    }

    /**
     * Установка id из каталог апп
     */
    public function SetCatalogAppID ($xml_id = '') {
        $this->StartDebugTime(__FUNCTION__);
        $this->Logger->log("LOG", "Получаем catalogApp id для товара ".$xml_id);
        $offset = 0;
        $iteration = 0;
        $limit = 10000;
        $ImarketTriggers = new ImarketTriggers();

        do {
            $profileRequestResult = $this->catalogAppAPI->getModels($xml_id, '', $offset, $limit);

            if (!empty($profileRequestResult["ERROR"])) {
                $this->Logger->log("ERROR", "Ошибка при получении данных о товаре ".print_r($profileRequestResult["ERROR"], true));
                $ImarketTriggers->SetError(["Проблемы с получением моделей из CatalogApp: \r\n".$profileRequestResult["ERROR"]."\r\n".__FILE__]);
                $ImarketTriggers->SendTriggerErrors();
                return;
            }

            if (!empty($profileRequestResult)) {
                if (!empty($arData)) {
                    $arData = array_merge($arData, $profileRequestResult);
                } else {
                    $arData = $profileRequestResult;
                }
            }

            $iteration++;
            if ($offset == 0) {
                $offset = $limit;
            } else {
                $offset = $limit * $iteration;
            }
        } while (!empty($profileRequestResult) && is_array($profileRequestResult));

        if (!empty($arData)) {
            $this->Logger->log("LOG", "Обновляем catalogApp id");
            $newData = [];
            foreach ($arData as $k => $arItem) {
                $arItem["id"] = $this->PrepareCatalogAppId($arItem["id"]);

                $newData[$arItem["externalId"]] = $arItem;
            }

            $arSelect = ["ID", "XML_ID", "PROPERTY_CATALOG_APP_ID"];
            $arFilter = ["IBLOCK_ID" => CATALOG_IBLOCK_ID, "PROPERTY_CATALOG_APP_ID" => false];
            $rsResCat = CIBlockElement::GetList(Array("ID" => "ASC"), $arFilter, false, false, $arSelect);
            while($arItem = $rsResCat->GetNext()) {
                if (empty($arItem["PROPERTY_CATALOG_APP_ID_VALUE"]) && !empty($newData[$arItem["XML_ID"]]["id"])) {
                    CIBlockElement::SetPropertyValuesEx($arItem["ID"], CATALOG_IBLOCK_ID, array(95218 => $newData[$arItem["XML_ID"]]["id"]));
                }
            }
        }

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
    #Helpers#
}
