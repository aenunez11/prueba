<?php

use Faker\Generator as Faker;
use App\Product;
use App\User;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description'=>$faker->paragraph(1),
        'quantity' => $faker->numberBetween(1,10),
        'status' => $faker->randomElement([Product::PRODUCT_DISPONIBLE,Product::PRODUCT_NO_DISPONIBLE]),
        'image' => $faker->randomElement(['logo.png','hombre.jpg','mujer.jpg']),
        //'seller_id' => User::inRandomOrder()->first()->id,
        'seller_id' => User::all()->random()->id,
    ];
});
