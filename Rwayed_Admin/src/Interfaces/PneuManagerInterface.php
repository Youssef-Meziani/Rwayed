<?php

namespace App\Interfaces;

use App\Entity\Pneu;
use Symfony\Component\HttpFoundation\JsonResponse;

interface PneuManagerInterface
{
    public function createPneu(Pneu $pneu, array $photoFiles): void;
    public function deletePneu(string $identifier): JsonResponse;
    public function editPneu(Pneu $pneu): void;
}
