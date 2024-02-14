<?php

namespace App\Factory;

use App\Entity\Technicien;
use App\Repository\TechnicienRepository;
use App\Services\PasswordHasherService;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Technicien>
 *
 * @method        Technicien|Proxy                     create(array|callable $attributes = [])
 * @method static Technicien|Proxy                     createOne(array $attributes = [])
 * @method static Technicien|Proxy                     find(object|array|mixed $criteria)
 * @method static Technicien|Proxy                     findOrCreate(array $attributes)
 * @method static Technicien|Proxy                     first(string $sortedField = 'id')
 * @method static Technicien|Proxy                     last(string $sortedField = 'id')
 * @method static Technicien|Proxy                     random(array $attributes = [])
 * @method static Technicien|Proxy                     randomOrCreate(array $attributes = [])
 * @method static TechnicienRepository|RepositoryProxy repository()
 * @method static Technicien[]|Proxy[]                 all()
 * @method static Technicien[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Technicien[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Technicien[]|Proxy[]                 findBy(array $attributes)
 * @method static Technicien[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Technicien[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class TechnicienFactory extends ModelFactory
{
    private PasswordHasherService $passwordHasherService;
    public function __construct(PasswordHasherService $passwordHasherService)
    {
        parent::__construct();
        $this->passwordHasherService = $passwordHasherService;
    }
    protected function getDefaults(): array
    {
        $status = ["actif", "en congé", "retiré"];
        return [
            'email' => self::faker()->unique()->email(),
            'nom' => self::faker()->firstName(),
            'prenom' => self::faker()->lastName(),
            'tele' => self::faker()->randomElement(['06', '07']) . self::faker()->numberBetween(1000000, 9999999),
            'date_naissance' => self::faker()->dateTimeBetween('-60 years', '-18 years'),
            'dernier_connection' => self::faker()->dateTimeBetween('-60 years', '-18 years'),
            'status' => self::faker()->randomElement($status),
            'roles' => $this->faker()->randomElement([
                ['ROLE_TECHNICIEN'],
            ]),
        ];
    }


    protected function initialize(): self
    {
        return $this->afterInstantiate(function(Technicien $technicien) {
            $hashedPassword = $this->passwordHasherService->hashPassword($technicien, "123azert");
            $technicien->setPassword($hashedPassword);
        });
    }

    protected static function getClass(): string
    {
        return Technicien::class;
    }
}
