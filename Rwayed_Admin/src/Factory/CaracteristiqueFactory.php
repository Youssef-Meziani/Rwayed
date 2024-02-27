<?php

namespace App\Factory;

use App\Entity\Caracteristique;
use App\Repository\CaracteristiqueRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Caracteristique>
 *
 * @method        Caracteristique|Proxy                     create(array|callable $attributes = [])
 * @method static Caracteristique|Proxy                     createOne(array $attributes = [])
 * @method static Caracteristique|Proxy                     find(object|array|mixed $criteria)
 * @method static Caracteristique|Proxy                     findOrCreate(array $attributes)
 * @method static Caracteristique|Proxy                     first(string $sortedField = 'id')
 * @method static Caracteristique|Proxy                     last(string $sortedField = 'id')
 * @method static Caracteristique|Proxy                     random(array $attributes = [])
 * @method static Caracteristique|Proxy                     randomOrCreate(array $attributes = [])
 * @method static CaracteristiqueRepository|RepositoryProxy repository()
 * @method static Caracteristique[]|Proxy[]                 all()
 * @method static Caracteristique[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Caracteristique[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Caracteristique[]|Proxy[]                 findBy(array $attributes)
 * @method static Caracteristique[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Caracteristique[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class CaracteristiqueFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'taille' => self::faker()->bothify('###/##R##'),
            'indiceCharge' => self::faker()->numberBetween(75, 100),
            'indiceVitesse' => self::faker()->randomElement(['H', 'V', 'W']),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Caracteristique $caracteristique): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Caracteristique::class;
    }
}
