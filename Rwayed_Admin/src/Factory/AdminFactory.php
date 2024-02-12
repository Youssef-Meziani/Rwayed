<?php

namespace App\Factory;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use App\Services\PasswordHasherService;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Admin>
 *
 * @method        Admin|Proxy                     create(array|callable $attributes = [])
 * @method static Admin|Proxy                     createOne(array $attributes = [])
 * @method static Admin|Proxy                     find(object|array|mixed $criteria)
 * @method static Admin|Proxy                     findOrCreate(array $attributes)
 * @method static Admin|Proxy                     first(string $sortedField = 'id')
 * @method static Admin|Proxy                     last(string $sortedField = 'id')
 * @method static Admin|Proxy                     random(array $attributes = [])
 * @method static Admin|Proxy                     randomOrCreate(array $attributes = [])
 * @method static AdminRepository|RepositoryProxy repository()
 * @method static Admin[]|Proxy[]                 all()
 * @method static Admin[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Admin[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Admin[]|Proxy[]                 findBy(array $attributes)
 * @method static Admin[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Admin[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class AdminFactory extends ModelFactory
{
    private PasswordHasherService $passwordHasherService;
    public function __construct(PasswordHasherService $passwordHasherService)
    {
        parent::__construct();
        $this->passwordHasherService = $passwordHasherService;
    }
    protected function getDefaults(): array
    {
        $rang = ["Directeur", "Manager", "Chef de service"];
        return [
            'email' => self::faker()->unique()->email(),
            'nom' => self::faker()->firstName(),
            'prenom' => self::faker()->lastName(),
            'tele' => self::faker()->randomElement(['06', '07']) . self::faker()->numberBetween(1000000, 9999999),
            'date_naissance' => self::faker()->dateTimeBetween('-60 years', '-18 years'),
            'dernier_connection' => self::faker()->dateTimeBetween('-60 years', '-18 years'),
            'rang' => self::faker()->randomElement($rang),
            'is_super' => self::faker()->boolean()
        ];
    }


    protected function initialize(): self
    {
        return $this->afterInstantiate(function(Admin $admin) {
            $hashedPassword = $this->passwordHasherService->hashPassword($admin, "123azert");
            $admin->setMotDePasse($hashedPassword);
        });
    }

    protected static function getClass(): string
    {
        return Admin::class;
    }
}
