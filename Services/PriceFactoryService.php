<?php


namespace Services;

//流水处理
use Logic\PriceDistributionStrategyLogic;
use Logic\PriceExcelStrategyLogic;
use Logic\PriceSelfOperatedStrategyLogic;

class PriceFactoryService
{
    //自营
    public static $SELF_OPERATED = 1;

    //分销
    public static $DISTRIBUTION = 2;

    //表格
    public static $EXCEL = 3;

    public static function factory($type, $req){

        $resObj = null;

        switch ($type){
            case self::$SELF_OPERATED:
                $resObj = new PriceSelfOperatedStrategyLogic();
                break;
            case self::$DISTRIBUTION:
                $resObj = new PriceDistributionStrategyLogic();
                break;
            case self::$EXCEL:
                $resObj = new PriceExcelStrategyLogic();
                break;
        }

        return $resObj->strategy($req);
    }
}