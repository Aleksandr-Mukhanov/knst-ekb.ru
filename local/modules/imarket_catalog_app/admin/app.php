<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage("MODULE_TITLE"));

if (isset($_REQUEST["save"]) && !empty($_REQUEST["save"])) {
    if (isset($_REQUEST["update"])) {
        $rules = [
            "SAVE" => $_REQUEST["rules_save"] ? $_REQUEST["rules_save"] : 0,
            "UPDATE" => $_REQUEST["rules_update"] ? $_REQUEST["rules_update"] : 0,
            "DELETE" => 1,
            "SKIP" => $_REQUEST["rules_skip"] ? $_REQUEST["rules_skip"] : 0
        ];

        $rules = serialize($rules);

        if (isset($_REQUEST["updateId"]) && !empty($_REQUEST["updateId"])) {
            $sql = "UPDATE catalog_app_rules SET 
                    SITE_PROFILE_ID = '".$_REQUEST["siteProfileId"]."',
                    RULES = '".$rules."',
                    PRICE_ID = '".$_REQUEST["priceId"]."',
                    CATALOG_APP_ID = '".$_REQUEST["catalogAppId"]."'";
        } else {
            $sql = "INSERT INTO catalog_app_rules (`SITE_PROFILE_ID`, `RULES`, `PRICE_ID`, `CATALOG_APP_ID`) VALUES 
                ('".$_REQUEST["siteProfileId"]."', '".$rules."', '".$_REQUEST["priceId"]."', '".$_REQUEST["catalogAppId"]."')";
        }

        Application::getConnection()->query($sql);
        header('Location: /bitrix/admin/imarket_catalog_app_app.php');
        exit();
    } else {
        $propRules = "";

        if (empty($_REQUEST["auto_activate_goods_images_count"]) || empty($_REQUEST["auto_activate_goods_properties_count"])) {
            $arResult["ERRORS"][] = Loc::getMessage("PROP_RULES_ERROR");
        } else {
            $propRules = ["IMAGES" => $_REQUEST["auto_activate_goods_images_count"], "PROPERTIES" => $_REQUEST["auto_activate_goods_properties_count"]];
            $propRules = serialize($propRules);

            $sql = "UPDATE catalog_app_settings SET 
                CATALOG_APP_CATALOG_ID = '".$_REQUEST["catalogApp_catalogId"]."',
                CATALOG_APP_USER = '".$_REQUEST["catalogApp_user"]."',
                CATALOG_APP_PASSWORD = '".$_REQUEST["catalogApp_pass"]."',
                CATALOG_IBLOCK_ID = '".$_REQUEST["iblock"]."',
                CREATE_WORKER = '".$_REQUEST["create_worker"]."',
                UPDATE_WORKER = '".$_REQUEST["update_worker"]."',
                UPDATE_PROPERTY_WORKER = '".$_REQUEST["update_property_worker"]."',
                AUTO_UPDATE_GOODS = '".$_REQUEST["auto_activate_goods"]."',
                AUTO_UPDATE_RULES = '".$propRules."'";
            Application::getConnection()->query($sql);
        }
    }
}

$sql = "SELECT * FROM catalog_app_settings";
$res = Application::getConnection()->query($sql);
$arResult["SETTINGS"] = $res->fetch();
$arResult["SETTINGS"]["AUTO_UPDATE_RULES"] = unserialize($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]);

$arIblocksTypes = [];
$db_iblock_type = CIBlockType::GetList();
while ($ar_iblock_type = $db_iblock_type->Fetch()) {
    if ($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG)) {
        $arIblocksTypes[] = $arIBType;
    }
}

$arIblocks = [];
$db_iblock = CIBlock::GetList(["NAME" => "ASC"], ["ID" => $arResult["SETTINGS"]["CATALOG_IBLOCK_ID"]]);
while ($ar_iblock = $db_iblock->Fetch()) {
    $arIblocks = $ar_iblock;
}

$sql = "SELECT * FROM catalog_app_rules";
$res = Application::getConnection()->query($sql);
while($arItem = $res->fetch()) {
    $arResult["RULES"][] = $arItem;
}
?>

<?if (!isset($_REQUEST["edit"])) :?>
    <h2>Основные настройки</h2>

    <?if (!empty($arResult["ERRORS"])) :?>
        <div class="adm-info-message-wrap adm-info-message-red">
            <div class="adm-info-message">
                <div class="adm-info-message-title">Ошибка</div>
                <?foreach ($arResult["ERRORS"] as $i => $error) :?>
                    <?=($i+1)."."?> <?=$error?>
                <?endforeach;?>
                <div class="adm-info-message-icon"></div>
            </div>
        </div>
    <?endif;?>

    <form method="POST" action="/bitrix/admin/imarket_catalog_app_app.php" enctype="multipart/form-data"name="imarket_catalog_app_app_form">
        <div class="adm-detail-block">
            <div class="adm-detail-content-wrap">
                <div class="adm-detail-content" id="edit1">
                    <div class="adm-detail-content-item-block">
                        <table class="adm-detail-content-table edit-table">
                            <tbody>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">Тип инфоблока:</td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <select name="ibtype" id="ibtype">
                                            <?foreach ($arIblocksTypes as $arItem) :?>
                                                <option value="<?=$arItem["ID"]?>" <?if ($arItem["ID"] == $arIblocks["IBLOCK_TYPE_ID"]) :?>selected<?endif;?>><?=$arItem["NAME"]?></option>
                                            <?endforeach;?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">Инфоблок:</td>
                                    <td width="60%" class="adm-detail-content-cell-r" id="idlockSelectArea">
                                        <select name="iblock" id="iblock">
                                            <option value="<?=$arIblocks["ID"]?>"><?=$arIblocks["NAME"]?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="create_worker">Включить синхронизацию каталога (создание товаров):</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="checkbox" value="1" <?if($arResult["SETTINGS"]["CREATE_WORKER"]):?>checked<?endif;?> name="create_worker" id="create_worker" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="create_worker">Включить обновление каталога (цены):</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="checkbox" value="1" <?if($arResult["SETTINGS"]["UPDATE_WORKER"]):?>checked<?endif;?> name="update_worker" id="update_worker" />
                                    </td>
                                </tr>

                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="create_worker">Включить обновление каталога (свойства):</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="checkbox" value="1" <?if($arResult["SETTINGS"]["UPDATE_PROPERTY_WORKER"]):?>checked<?endif;?> name="update_property_worker" id="update_property_worker" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="create_worker">Логин авторизации в catalog.app:</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="text" value="<?=$arResult["SETTINGS"]["CATALOG_APP_USER"]?>" name="catalogApp_user" id="catalogApp_user" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="create_worker">Пароль авторизации в catalog.app:</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="text" value="<?=$arResult["SETTINGS"]["CATALOG_APP_PASSWORD"]?>" name="catalogApp_pass" id="catalogApp_pass" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="catalogApp_catalogId">ID каталога в catalog.app:</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="text" value="<?=$arResult["SETTINGS"]["CATALOG_APP_CATALOG_ID"]?>" name="catalogApp_catalogId" id="catalogApp_catalogId" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="auto_activate_goods">Автоматически активировать товары:</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="checkbox" value="1" <?if($arResult["SETTINGS"]["AUTO_UPDATE_GOODS"] || $_REQUEST["auto_activate_goods"]):?>checked<?endif;?> name="auto_activate_goods" id="auto_activate_goods" />
                                    </td>
                                </tr>
                                <tr class="auto_update_rules_area" <?if($arResult["SETTINGS"]["AUTO_UPDATE_GOODS"] || $_REQUEST["auto_activate_goods"]):?>style="display: table-row;"<?endif;?>>
                                    <td colspan="2">
                                        <table class="adm-detail-content-table edit-table">
                                            <tr>
                                                <td width="40%" class="adm-detail-content-cell-l" valign="top">
                                                    <label for="auto_activate_goods_images_count">Настройка заполненности картинок*:</label>
                                                </td>
                                                <td width="60%" class="adm-detail-content-cell-r">
                                                    <div>
                                                        <input type="radio" value="1" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["IMAGES"] == 1 || $_REQUEST["auto_activate_goods_images_count"] == 1):?>checked=""<?endif;?> id="auto_activate_goods_images_count_1" name="auto_activate_goods_images_count" />
                                                        <label for="auto_activate_goods_images_count_1">1 изображение</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="2" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["IMAGES"] == 2 || $_REQUEST["auto_activate_goods_images_count"] == 2):?>checked=""<?endif;?> id="auto_activate_goods_images_count_2" name="auto_activate_goods_images_count" />
                                                        <label for="auto_activate_goods_images_count_2">2 изображение</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="3" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["IMAGES"] == 3 || $_REQUEST["auto_activate_goods_images_count"] == 3):?>checked=""<?endif;?> id="auto_activate_goods_images_count_3" name="auto_activate_goods_images_count" />
                                                        <label for="auto_activate_goods_images_count_3">3 изображение</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="4" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["IMAGES"] == 4 || $_REQUEST["auto_activate_goods_images_count"] == 4):?>checked=""<?endif;?> id="auto_activate_goods_images_count_4" name="auto_activate_goods_images_count" />
                                                        <label for="auto_activate_goods_images_count_4">4 изображение</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="5" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["IMAGES"] == 5 || $_REQUEST["auto_activate_goods_images_count"] == 5):?>checked=""<?endif;?> id="auto_activate_goods_images_count_5" name="auto_activate_goods_images_count" />
                                                        <label for="auto_activate_goods_images_count_5">5 изображение</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40%" class="adm-detail-content-cell-l" valign="top">
                                                    <label for="auto_activate_goods_properties_count">Настройка заполненности свойств*:</label>
                                                </td>
                                                <td width="60%" class="adm-detail-content-cell-r">
                                                    <div>
                                                        <input type="radio" value="5" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["PROPERTIES"] || $_REQUEST["auto_activate_goods_properties_count"] == 5):?>checked=""<?endif;?> id="auto_activate_goods_properties_count_5" name="auto_activate_goods_properties_count" />
                                                        <label for="auto_activate_goods_properties_count_5">5% свойств</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="10" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["PROPERTIES"] == 10 || $_REQUEST["auto_activate_goods_properties_count"] == 10):?>checked=""<?endif;?> id="auto_activate_goods_properties_count_10" name="auto_activate_goods_properties_count" />
                                                        <label for="auto_activate_goods_properties_count_10">10% свойств</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="30" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["PROPERTIES"] == 30 || $_REQUEST["auto_activate_goods_properties_count"] == 30):?>checked=""<?endif;?> id="auto_activate_goods_properties_count_30" name="auto_activate_goods_properties_count" />
                                                        <label for="auto_activate_goods_properties_count_30">30% свойств</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="50" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["PROPERTIES"] == 50 || $_REQUEST["auto_activate_goods_properties_count"] == 50):?>checked=""<?endif;?> id="auto_activate_goods_properties_count_50" name="auto_activate_goods_properties_count" />
                                                        <label for="auto_activate_goods_properties_count_50">50% свойств</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="90" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["PROPERTIES"] == 90 || $_REQUEST["auto_activate_goods_properties_count"] == 90):?>checked=""<?endif;?> id="auto_activate_goods_properties_count_90" name="auto_activate_goods_properties_count" />
                                                        <label for="auto_activate_goods_properties_count_90">90% свойств</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" value="100" <?if($arResult["SETTINGS"]["AUTO_UPDATE_RULES"]["PROPERTIES"] == 100 || $_REQUEST["auto_activate_goods_properties_count"] == 100):?>checked=""<?endif;?> id="auto_activate_goods_properties_count_100" name="auto_activate_goods_properties_count" />
                                                        <label for="auto_activate_goods_properties_count_100">100% свойств</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <tr>
                                <td>
                                    <a href="/bitrix/admin/perfmon_table.php?PAGEN_1=1&SIZEN_1=20&lang=ru&table_name=catalog_app_section_connections" target="_blank">
                                        Посмотреть сопоставленные разделы
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/bitrix/admin/perfmon_table.php?lang=ru&table_name=catalog_app_tasks" target="_blank">
                                        Посмотреть задачи
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/bitrix/admin/perfmon_table.php?lang=ru&table_name=catalog_app_workers" target="_blank">
                                        Посмотреть обработчики
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="adm-detail-content-btns-wrap">
                    <div class="adm-detail-content-btns">
                        <input type="submit" class="adm-btn-save" name="save" id="save" value="Сохранить">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <style>
        .auto_update_rules_area {
            display: none;
        }
    </style>

    <h2>Настройка правил</h2>
    <style>
        .edit-button {
            position: unset;
        }

        .edit-button:before {
            left: 29px;
            top: 19px;
        }

        .adm-list-table-cell {
            background: #fff;
            border: none;
            color: #000;
            font-size: 13px;
            height: 15px;
            padding: 11px 0 10px 16px;
            text-align: left;
            vertical-align: top;
        }
    </style>
    <div class="adm-list-table-layout">
        <div class="adm-list-table-wrap adm-list-table-without-footer">
            <div class="adm-list-table-top">
                <a href="/bitrix/admin/imarket_catalog_app_app.php?edit=1" class="adm-btn adm-btn-save adm-btn-add" title="" id="btn_new">Добавить</a>
            </div>
            <form method="POST" action="/bitrix/admin/imarket_catalog_app_app.php" enctype="multipart/form-data"name="imarket_catalog_app_app_form">
                <table class="adm-list-table">
                    <thead>
                        <tr class="adm-list-table-header">
                            <td class="adm-list-table-cell"></td>
                            <td class="adm-list-table-cell">
                                <div class="adm-list-table-cell-inner">ID профиля на сайте</div>
                            </td>
                            <td class="adm-list-table-cell">
                                <div class="adm-list-table-cell-inner">ID типа цены</div>
                            </td>
                            <td class="adm-list-table-cell">
                                <div class="adm-list-table-cell-inner">ID ценообразования в каталог апп</div>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    <?foreach ($arResult["RULES"] as $arItem) :?>
                        <tr class="adm-list-table-row">
                            <td class="adm-list-table-cell" style="position: relative;">
                                <div class="adm-small-button adm-table-setting edit-button"
                                     title="Изменить"
                                     onclick="location.href='/bitrix/admin/imarket_catalog_app_app.php?edit=1&id=<?=$arItem["ID"]?>'; return false;"></div>
                            </td>
                            <td class="adm-list-table-cell">
                                <?=$arItem["SITE_PROFILE_ID"]?>
                            </td>
                            <td class="adm-list-table-cell">
                                <?=$arItem["PRICE_ID"]?>
                            </td>
                            <td class="adm-list-table-cell">
                                <?=$arItem["CATALOG_APP_ID"]?>
                            </td>
                        </tr>
                    <?endforeach;?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
<?else :?>
    <?$APPLICATION->SetTitle("Добавление правил обработки");?>
    <?if (!empty($_REQUEST["id"])) {
        $APPLICATION->SetTitle("Редактирование правила обработки ".$_REQUEST["id"]);

        $sql = "SELECT * FROM catalog_app_rules WHERE ID = ".$_REQUEST["id"];
        $res = Application::getConnection()->query($sql);
        $arResult["EDIT"] = $res->fetch();

        $arResult["EDIT"]["RULES"] = unserialize($arResult["EDIT"]["RULES"]);
    }?>

    <form method="POST" action="/bitrix/admin/imarket_catalog_app_app.php" enctype="multipart/form-data"name="imarket_catalog_app_app_form">
        <input type="hidden" name="update" value="1" />
        <input type="hidden" name="updateId" value="<?=$_REQUEST["id"]?>" />
        <div class="adm-detail-block">
            <div class="adm-detail-content-wrap">
                <div class="adm-detail-content" id="edit1">
                    <div class="adm-detail-content-item-block">
                        <table class="adm-detail-content-table edit-table">
                            <tbody>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">ID профиля на сайте:</td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="text" value="<?=$arResult["EDIT"]["SITE_PROFILE_ID"]?>" name="siteProfileId" id="siteProfileId" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">ID типа цены:</td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="text" value="<?=$arResult["EDIT"]["PRICE_ID"]?>" name="priceId" id="priceId" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">ID ценообразования в каталог апп:</td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="text" value="<?=$arResult["EDIT"]["CATALOG_APP_ID"]?>" name="catalogAppId" id="catalogAppId" />
                                    </td>
                                </tr>

                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="rules_save">Сохранять данные в таблицу:</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="checkbox" value="1" <?if($arResult["EDIT"]["RULES"]["SAVE"]):?>checked<?endif;?> name="rules_save" id="rules_save" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="rules_update">Обновлять цены:</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="checkbox" value="1" <?if($arResult["EDIT"]["RULES"]["UPDATE"]):?>checked<?endif;?> name="rules_update" id="rules_update" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%" class="adm-detail-content-cell-l">
                                        <label for="rules_skip">Пропускать при обработке:</label>
                                    </td>
                                    <td width="60%" class="adm-detail-content-cell-r">
                                        <input type="checkbox" value="1" <?if($arResult["EDIT"]["RULES"]["SKIP"]):?>checked<?endif;?> name="rules_skip" id="rules_skip" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="adm-detail-content-btns-wrap">
                    <div class="adm-detail-content-btns">
                        <input type="submit" class="adm-btn-save" name="save" id="save" value="Сохранить">
                        <input type="button" value="Отменить" name="cancel" onclick="window.location='/bitrix/admin/imarket_catalog_app_app.php'" title="Не сохранять и вернуться">
                    </div>
                </div>
            </div>
        </div>
    </form>
<?endif;?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).on("change", "#ibtype", function () {
        $.ajax({
            url: '/local/modules/imarket_catalog_app/install/ajax.php',
            data: 'action=get_iblocks&ibtype=' + $(this).val(),
            type: 'post',
            beforeSend: function () {

            },
            success: function (msg) {
                $("#idlockSelectArea").html(msg);
            }
        }).done(function () {

        });
    });

    $(document).on("click", "#auto_activate_goods", function () {
        if ($(this).is(":checked")) {
            $(".auto_update_rules_area").show();
        } else {
            $(".auto_update_rules_area").hide();
        }
    });
</script>

<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
