<?php


namespace Interfaces;

//成本处理-策略类
interface CostPriceStrategyInterface
{
    public function strategy($req);
}