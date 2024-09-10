<?php


use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $faker = Faker\Factory::create();
        $users = [];
        for ($i = 0; $i < 20; $i++) {
            // 用户表数据
            $users[] = [
                'username'      => $faker->userName,
                'nickname'      => $faker->userName,
                'password'      => sha1($faker->password),
                'sex'           => $faker->numberBetween($min = 0, $max = 1),
                'email'         => $faker->unique()->safeEmail(),
                'mobile'        => $faker->e164PhoneNumber,
                'level'         => $faker->numberBetween($min = 1, $max = 3),
                'birthday'      => $faker->date($format = 'Y-m-d', $max = 'now'),
                'money'         => $faker->numberBetween($min = 0, $max = 1000),
                'score'         => $faker->numberBetween($min = 0, $max = 1000),
                'join_time'     => date('Y-m-d H:i:s'),
                'join_ip'       => $faker->ipv4,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->insert('wa_users', $users);
        // $this->table('wa_users')->truncate();      
    }
}
