<?php
/**
 * Created by PhpStorm.
 * User: vfilippov
 * Date: 06.04.16
 * Time: 20:25
 */
define("STOP_STATISTICS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!CModule::IncludeModule("pweb.tenderix"))
    return;
IncludeModuleLangFile(__FILE__);

$result = array();

$MONTH_NAME = array(
    "1" => "Янв",
    "2" => "Фев",
    "3" => "Мар",
    "4" => "Апр",
    "5" => "Май",
    "6" => "Июн",
    "7" => "Июл",
    "8" => "Авг",
    "9" => "Сен",
    "10" => "Окт",
    "11" => "Ноя",
    "12" => "Дек",
);

$year = (isset($_REQUEST["year"]) ? intval($_REQUEST["year"]) : date('Y'));

$min_year = $year;
$max_year = $year;

$color = array(
    "#FF0F00",
    "#FF6600",
    "#FF9E01",
    "#FCD202",
    "#F8FF01",
    "#B0DE09",
    "#04D215",
    "#0D8ECF",
    "#0D52D1",
    "#2A0CD0",
    "#8A0CCF",
    "#CD0D74"
);

switch ($_REQUEST['action']) {
    case "lots_itogo":

        if($min_year != $max_year) {
            for($i=$min_year; $i <= $max_year; $i++ ) {
                $result["date"][] = $i;
                $result["value1"][] = CTenderixStatistic::LotsCount($i,"year_all_for_graph", $sections = Array());
            }
            $result = json_encode($result);
        } elseif($min_year == $max_year) {
            $result["date"][0] = '01-01-'.date($max_year);
            $result["value1"][0] = "0";
            foreach ($MONTH_NAME as $km => $vm) {
				
                $lastday = date('d', mktime(23, 59, 59, $km + 1, 0, date($max_year)));
                $result["date"][] = $lastday . '-' . (strlen($km) == 1 ? '0' : '') . $km . '-' . date($max_year);
                $result["value1"][] = CTenderixStatistic::LotsCount($max_year, "all_for_graph", $km, Array());
                if ($km >= date('n') && $max_year == date('Y')) {
                    break;
                }
            }
            $result = json_encode($result);
        }
    break;

    case "lots_price":

        if($min_year != $max_year) {
            for($i=$min_year; $i <= $max_year; $i++ ) {
                $result["month"][] = $i;
                $result["color"][] = $color[rand(0, count($color))];
                $result["mln"][] = floatval(CTenderixStatistic::LotsPrice($i,"year_all"));
            }
            $result = json_encode($result);
        } elseif($min_year == $max_year) {
            foreach ($MONTH_NAME as $km => $vm) {
                $result["month"][] = $vm;
                $result["color"][] = $color[rand(0, count($color))];
                $result["mln"][] = floatval(CTenderixStatistic::LotsPrice($max_year,"all", $km, $sections = Array()));
                if ($km >= date('n') && $max_year == date('Y')) {
                    break;
                }
            }
            $result = json_encode($result);
        }
        break;

    case "count_users":

        if($min_year != $max_year) {
            for($i=$min_year; $i <= $max_year; $i++ ) {
                $result["year"][] = $i;
                $result["color"][] = $color[rand(0, count($color))];
                $result["users"][] = CTenderixStatistic::UsersCount($i, "year_new");
            }
            $result = json_encode($result);
        } elseif($min_year == $max_year) {
            if(isset($_REQUEST["type"]) && $_REQUEST["type"] == "graph") {
                $result["year"][0] = '01-01-'.date($max_year);
                $result["users"][0] = "0";
                foreach ($MONTH_NAME as $km => $vm) {
                    $lastday = date('d', mktime(23, 59, 59, $km + 1, 0, date($max_year)));
                    $result["year"][] = $lastday . '-' . (strlen($km) == 1 ? '0' : '') . $km . '-' . date($max_year);
                    $result["users"][] = CTenderixStatistic::UsersCount($max_year, "new", $km);
                    if ($km >= date('n') && $max_year == date('Y')) {
                        break;
                    }
                }
            } elseif(isset($_REQUEST["type"]) && $_REQUEST["type"] == "chart") {
                foreach ($MONTH_NAME as $km => $vm) {
                    $result["year"][] = $vm;
                    $result["color"][] = $color[rand(0, count($color))];
                    $result["users"][] = CTenderixStatistic::UsersCount($max_year, "new", $km);
                    if ($km >= date('n') && $max_year == date('Y')) {
                        break;
                    }
                }
            }
            $result = json_encode($result);
        }
        break;

    case "effect_lots":
        $itogo_fail = 0;
        $itogo_win = 0;
        if($min_year != $max_year) {
            for($i=$min_year; $i <= $max_year; $i++ ) {
                $result["year"][] = $i;
                $result["itogo_fail"][] = CTenderixStatistic::LotsCount($i,"year_all_fail_for_graph", $sections = Array());
                $result["itogo_win"][] = CTenderixStatistic::LotsCount($i,"year_all_win_for_graph", $sections = Array());
            }

            $result = json_encode($result);
        } elseif($min_year == $max_year) {
            foreach ($MONTH_NAME as $km => $vm) {
                $result["year"][] = $vm;
                $result["itogo_fail"][] = CTenderixStatistic::LotsCount($max_year,"all_fail_for_graph", $km, $sections = Array());
                $result["itogo_win"][] = CTenderixStatistic::LotsCount($max_year,"all_win_for_graph", $km, $sections = Array());
                if ($km >= date('n') && $max_year == date('Y')) {
                    break;
                }
            }
            $result = json_encode($result);
        }
        break;
}

//__($result);

echo $result;