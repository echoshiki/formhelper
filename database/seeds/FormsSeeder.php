<?php


use Phinx\Seed\AbstractSeed;

class FormsSeeder extends AbstractSeed
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
        $forms = [];
        $users = $this->fetchAll('SELECT id, username FROM wa_users WHERE id < 5');

        for ($i = 0; $i < 30; $i++) {
            $userid = $faker->randomElement($users)['id'];
            $username = $this->fetchRow('SELECT username FROM wa_users WHERE id = '. $userid);
            $username = $username['username'];
            $expired_at = $faker->dateTimeBetween('-1 week', '+1 year')->format('Y-m-d H:i:s');
            $created_at = $faker->dateTimeBetween('-2 year', 'now')->format('Y-m-d H:i:s');
            $forms[] = [
                'user_id' => $userid,
                'title' => $username.'在'.$created_at.'创建的表单',
                'description' => $faker->sentence,
                'expired_at' => $expired_at,
                'created_at' => $created_at,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->table('formhelper_forms')->insert($forms)->save();
    }
}
