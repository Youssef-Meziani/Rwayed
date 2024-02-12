<?php

namespace App\Factory;

use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use App\Services\PasswordHasherService;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Adherent>
 *
 * @method        Adherent|Proxy                     create(array|callable $attributes = [])
 * @method static Adherent|Proxy                     createOne(array $attributes = [])
 * @method static Adherent|Proxy                     find(object|array|mixed $criteria)
 * @method static Adherent|Proxy                     findOrCreate(array $attributes)
 * @method static Adherent|Proxy                     first(string $sortedField = 'id')
 * @method static Adherent|Proxy                     last(string $sortedField = 'id')
 * @method static Adherent|Proxy                     random(array $attributes = [])
 * @method static Adherent|Proxy                     randomOrCreate(array $attributes = [])
 * @method static AdherentRepository|RepositoryProxy repository()
 * @method static Adherent[]|Proxy[]                 all()
 * @method static Adherent[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Adherent[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Adherent[]|Proxy[]                 findBy(array $attributes)
 * @method static Adherent[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Adherent[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class AdherentFactory extends ModelFactory
{
    private PasswordHasherService $passwordHasherService;
    public function __construct(PasswordHasherService $passwordHasherService)
    {
        parent::__construct();
        $this->passwordHasherService = $passwordHasherService;
    }
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->unique()->email(),
            'nom' => self::faker()->firstName(),
            'points_fidelite' => self::faker()->numberBetween(0, 1000),
            'prenom' => self::faker()->lastName(),
            'tele' => self::faker()->randomElement(['06', '07']) . self::faker()->numberBetween(1000000, 9999999),
            'date_naissance' => self::faker()->dateTimeBetween('-60 years', '-18 years'),
            'dernier_connection' => self::faker()->dateTimeBetween('-60 years', '-18 years'),
        ];
    }

    protected function initialize(): self
    {
        return $this->afterInstantiate(function(Adherent $adherent) {
            $hashedPassword = $this->passwordHasherService->hashPassword($adherent, "123azert");
                $adherent->setMotDePasse($hashedPassword);
        });
    }

    protected static function getClass(): string
    {
        return Adherent::class;
    }
}
