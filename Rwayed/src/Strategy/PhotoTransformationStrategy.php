<?php

namespace App\Strategy;

use App\DTO\PhotoDTO;
use App\Entity\Photo;

class PhotoTransformationStrategy implements TransformationStrategyInterface
{
    public function transform($dto)
    {
        if (!$dto instanceof PhotoDTO) {
            throw new \InvalidArgumentException('Expected type of PhotoDTO');
        }

        $Photo = new Photo();
        $Photo->setId($dto->id);
        $Photo->setPath($dto->path);

        return $Photo;
    }
}
