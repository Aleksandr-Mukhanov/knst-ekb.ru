<?php
AddEventHandler("main", "OnBeforeProlog", "MyOnBeforePrologHandler");

function MyOnBeforePrologHandler()
{
    if ($_REQUEST['city'])
    {
        $_SESSION['city'] = $_REQUEST['city'];
    }
}

function get_cities_list()
{

}

function upload_file_in_server($arFile)
{
    $arTranslateParams = array("replace_space"=>"-","replace_other"=>"-");
    $server = \Bitrix\Main\Context::getCurrent()->getServer();
    $fileUploadDir = $server->getDocumentRoot().'/upload/user_forms/';
    $arFile['save_to'] = $fileUploadDir.'_'.date('Ymd_His_').$arFile['name'];
    if(!move_uploaded_file($arFile['tmp_name'], $arFile['save_to'])) {
        AddMessage2Log("Ошибка загрузки файла ".$arFile['name']);
    }

    $arFileBx = CFile::MakeFileArray($arFile['save_to']);

    return $arFileBx;

}

?>