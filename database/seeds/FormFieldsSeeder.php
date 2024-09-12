<?php


use Phinx\Seed\AbstractSeed;

class FormFieldsSeeder extends AbstractSeed
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
        $formFields = [];
        $forms = $this->fetchAll('SELECT id FROM formhelper_forms');

        foreach ($forms as $form) {
            for ($i = 0; $i < rand(5, 10); $i++) {
                $form_fields[] = [
                    'form_id' => $form['id'],
                    'label' =>$faker->randomElement(['字段01', '字段02', '字段03', '字段04', '字段05', '字段06', '字段07', '字段08', '字段09', '字段10']),
                    'field_type' => $faker->randomElement(['text', 'textarea', 'select', 'checkbox', 'radio', 'date', 'number']),
                    'options' => json_encode($faker->randomElements(['苹果', '梨', '西瓜', '水蜜桃', '火龙果', '柠檬', '橙子', '樱桃'], 3)),
                    'required' => rand(0, 1),
                    'sort' => rand(0, 9),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        $this->table('formhelper_form_fields')->insert($form_fields)->save();
    }
}
