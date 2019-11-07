<?php


namespace Services;

//流水处理
use Logic\CostPriceExcelStrategyLogic;
use Logic\CostPriceYouZanStrategyLogic;

class PriceFactoryService
{
    //有赞
    public static $YOUZAN = 1;

    //表格
    public static $EXCEL = 2;

    public static function factory($type, $req){

        $resObj = null;

        switch ($type){
            case self::$YOUZAN:
                $resObj = new CostPriceYouZanStrategyLogic();
                break;
            case self::$EXCEL:
                $resObj = new CostPriceExcelStrategyLogic();
                break;
        }

        return $resObj->strategy($req);
    }
}