<?php
namespace App\Strategy;

interface TransformationStrategyInterface
{
    public function transform($dto); // design pattern pour convertir donne PneuDTO en Pneu
}