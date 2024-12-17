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

        // 标题组合元素
        $topPart = [
            '钢铁厂',
            '外贸公司',
            '五金市场',
            '猎头公司',
            '食品厂',
            '金融机构',
            '学校',
            '食堂',
            '餐饮连锁',
            '销售集团',
        ];

        $middlePart = [
            '圣诞节活动',
            '户外团建',
            '联欢晚会',
            '观看电影',
            '年货采购'
        ];

        $bottomPart = [
            '问卷调查',
            '报名表',
            '摸底排查',
            '意见收集',
            '匿名调查'
        ];

        for ($i = 0; $i < 30; $i++) {
            $userid = $faker->randomElement($users)['id'];
            $username = $this->fetchRow('SELECT username FROM wa_users WHERE id = '. $userid);
            $username = $username['username'];
            $started_at = $faker->dateTimeBetween('-2 year', 'now')->format('Y-m-d H:i:s');
            $expired_at = $faker->dateTimeBetween('-1 week', '+2 year')->format('Y-m-d H:i:s');
            $created_at = $faker->dateTimeBetween('-2 year', 'now')->format('Y-m-d H:i:s');
            $forms[] = [
                'user_id' => $userid,
                'title' => '某'.$faker->randomElement($topPart).'关于'.$faker->randomElement($middlePart).'的'.$faker->randomElement($bottomPart),
                'description' => $faker->sentence,
                'started_at' => $started_at,
                'expired_at' => $expired_at,
                'created_at' => $created_at,
                'limited' => $faker->numberBetween($min = 0, $max = 1000),
                'single' => rand(0, 1),
                'disabled' => rand(0, 1),
                'logged' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->table('formhelper_forms')->insert($forms)->save();
    }
}
