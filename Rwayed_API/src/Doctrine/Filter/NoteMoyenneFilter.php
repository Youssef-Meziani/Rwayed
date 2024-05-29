<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class NoteMoyenneFilter implements FilterInterface
{
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $filters = $context['filters'] ?? [];
        if (!isset($filters['noteMoyenne'])) {
            return;
        }

        $value = $filters['noteMoyenne'];
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('(%s.scoreTotal / %s.nombreEvaluations) >= :minNote AND (%s.scoreTotal / %s.nombreEvaluations) < :maxNote', $rootAlias, $rootAlias, $rootAlias, $rootAlias))
            ->setParameter('minNote', $value)
            ->setParameter('maxNote', $value + 1);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'noteMoyenne' => [
                'property' => 'noteMoyenne',
                'type' => Type::BUILTIN_TYPE_FLOAT,
                'required' => false,
                'openapi' => [
                    'description' => 'Filter pneu by note moyenne',
                    'name' => 'noteMoyenne',
                    'type' => 'float',
                ],
            ],
        ];
    }
}
