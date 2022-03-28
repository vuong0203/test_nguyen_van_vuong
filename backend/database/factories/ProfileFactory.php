<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Arr;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'birth_place' => $this->faker->realText(10),
            'birthday' => $this->faker->dateTimeBetween('+15year', '+30year')->format('Y-m-d H:i'),
            'gender' => Arr::random([
                '男性',
                '女性',
                'その他'
            ]),
            'introduction' => $this->faker->realText(50),
            'birthday_is_published' => $this->faker->boolean(50),
            'gender_is_published' => $this->faker->boolean(50),
            'image_url' => 'public/sampleImage/my-page.svg',
            'inviter_code' => $this->faker->uuid,
        ];
    }
}
