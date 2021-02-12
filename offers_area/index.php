<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->SetAdditionalCSS('/bitrix/gadgets/bitrix/_area/styles.css');

$saleIncluded = \CModule::IncludeModule('sale');

$bEdit = ($_REQUEST['gdhelphtml'] == $id) && ($_REQUEST['edit']=='true') && ($arParams["PERMISSION"] > "R");
if($_SERVER['REQUEST_METHOD'] == 'POST' && $_REQUEST['gdhelphtmlform'] == 'Y' && $_REQUEST['gdhelphtml'] == $id)
{
	$arGadget["USERDATA"] = Array("content"=>$_POST["html_content"]);
	$arGadget["FORCE_REDIRECT"] = true;
}
$arData = $arGadget["USERDATA"];

$arFilter = array();
$orders = [];

if ($saleIncluded) {
    $orders = \CSaleOrder::GetList(array('ID' => 'DESC'));
    $count = $orders->SelectedRowsCount();
} else {
    $count = $orders;
}

$content = $arData["content"] = count($count);

if (empty($count%2)) {
    $postfix = 'а';
} else if ($count == 1) {
    $postfix = '';
} else {
    $postfix = 'ов';
}

?>

<?if(!$bEdit):?>

    <?
    if($content)
    {
        $parser = new CTextParser();
        $parser->allow = array(
            "HTML"=>($arParams["MODE"] != "AI" ? "N" : "Y"),
            "ANCHOR"=>"Y",
            "BIU"=>"Y",
            "IMG"=>"Y",
            "QUOTE"=>"Y",
            "CODE"=>"Y",
            "FONT"=>"Y",
            "LIST"=>"Y",
            "SMILES"=>"N",
            "NL2BR"=>"N",
            "VIDEO"=>"N",
            "TABLE"=>"Y",
            "CUT_ANCHOR"=>"N",
            "ALIGN"=>"Y"
        );
        $parser->parser_nofollow = "Y";

        echo '<a class="gdproductareachlink" >У вас <strong>'.$content.' </strong> объект'.$postfix.'.</a>';
    }
    else
    {
        if($arParams["PERMISSION"]>"R")
            echo GetMessage("GD_HTML_AREA_NO_CONTENT");
    }
    ?>
    <BR/>
    <BR/>
<?endif?>