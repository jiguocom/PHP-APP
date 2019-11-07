<?php


namespace Interfaces;

//流水线处理-策略类
interface PriceStrategyInterface
{
    public function strategy($req);
}