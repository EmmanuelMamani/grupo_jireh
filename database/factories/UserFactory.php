<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     * Columnas reales de `users`: CI, Nombre, Email, Telefono, Rol,
     * Usuario, Contrasenia, Activo (ver 2014_10_12_000000_create_users_table).
     *
     * @return array
     */
    public function definition()
    {
        return [
            'CI' => $this->faker->unique()->numberBetween(100000, 99999999),
            'Nombre' => $this->faker->name(),
            'Email' => $this->faker->unique()->safeEmail(),
            'Telefono' => $this->faker->unique()->numberBetween(60000000, 79999999),
            'Rol' => 'Empleado',
            'Usuario' => $this->faker->unique()->userName(),
            'Contrasenia' => 'password',
            'Activo' => true,
        ];
    }
}
