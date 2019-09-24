<?php

use Faker\Generator as Faker;

$factory->define(app\Models\Feedback::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'order_id' => null,
        'service_id' => null,
        'status' => \app\Models\Feedback::STATUS_NEW ,
        'text'=> $faker->realText(200),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});




$factory->state(\app\Models\Feedback::class, 'new', [
    'status' => \app\Models\Feedback::STATUS_NEW,
]);

$factory->state(\app\Models\Feedback::class, 'deny', [
    'status' => \app\Models\Feedback::STATUS_DENY,
]);

$factory->state(\app\Models\Feedback::class, 'allow', [
    'status' => \app\Models\Feedback::STATUS_ALLOW,
]);

$factory->state(\app\Models\Feedback::class, 'service', [
    'order_id' => null,
    'service_id' => 1,
]);

$factory->state(\app\Models\Feedback::class, 'order', [
    'order_id' => 1,
    'service_id' => null,
]);

