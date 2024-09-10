<?php


use Phinx\Seed\AbstractSeed;

class FormSubmissionsSeeder extends AbstractSeed
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
        $form_submissions = [];
        $forms = $this->fetchAll('SELECT id, user_id FROM formhelper_forms');
        $user_ids = $this->fetchAll('SELECT id FROM wa_users');

        foreach ($forms as $form) {
            for ($i = 0; $i < rand(1, 9); $i++) {
                $form_submissions[] = [
                    'form_id' => $form['id'],
                    'user_id' => $user_ids[array_rand($user_ids)]['id'],
                    'submitted_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        $this->table('formhelper_form_submissions')->insert($form_submissions)->save();
    }
}
