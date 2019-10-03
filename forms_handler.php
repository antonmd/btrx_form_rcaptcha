<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$APPLICATION->RestartBuffer();
header('Content-Type: text/html; charset='.LANG_CHARSET);

define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);

//Получаем и обрабатываем POST запрос
$request = $context->getRequest();
$gRecaptchaResponse = $request->getPost("gRecaptchaResponse");
//Получаем ответ от сервера гугл
if($gRecaptchaResponse) {
    function getCaptcha("_YOUR_SECRET_CODE_") {
        $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=_YOUR_SITE_CODE_&response=_YOUR_SECRET_CODE_");
        $Return = json_decode($Response);
        return $Return;
    }
//    Если ответ меньше 0.5 прекращаем обработку запроса и выводим сообщение что запрос от робота
   $Return = getCaptcha($gRecaptchaResponse);
    if($Return->success == true && $Return->score < 0.5){
       die("Возможно вы робот!");
    }
}
//Если ответ больше 0.5 продолжаем обработку запроса
$subject = $request->getPost("messageType");
$from = $request->getPost("Name");
$phone = $request->getPost("Phone");
$name = $subject . ' от ' . $from . ' ' . $phone;
$detail_text = $subject . ' от ' . $from . ' ' . $phone;
if ($request->getPost("Text")) {
    $text = $request->getPost('Text');
    $detail_text = $detail_text . " " . $text;
}

//Отправляем данные из формы в инфоблок
$el = new CIBlockElement;

$arLoadProductArray = Array(
    "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
    "IBLOCK_ID"      => "_YOUR_BLOCK_ID_",
    "ACTIVE"         => "Y",           // активен
    "NAME"           => $name,
    "DETAIL_TEXT"    => $detail_text,
);

    $el->Add($arLoadProductArray);
//Сообщаем пользователю об успешной регистрации запроса или отправляем сообщение об ошибке
    if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        echo "<p class='alert-success'>Ваще сообщение зарегистрировано: " . $PRODUCT_ID . "</p>";
    }
    else {
        echo "<p class='alert-warning'>Ошибка: " . $el->LAST_ERROR;
    }
//Отправляем оповещение по почте с данными из формы
    $arEventFields = array("AUTHOR" => $name, "PHONE" => $phone, "TEXT" => $detail_text);
    CEvent::SendImmediate("PB_BACKCALL_FORM_EVENT", "s1", $arEventFields);