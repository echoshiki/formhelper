<?php


use Phinx\Seed\AbstractSeed;

class FormFieldValuesSeeder extends AbstractSeed
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
        $form_field_values = [];
        $form_submissions = $this->fetchAll('SELECT * FROM formhelper_form_submissions');

        foreach ($form_submissions as $form_submission) {
            // 检索出该次提交的所有字段
            $fields = $this->fetchAll('SELECT * FROM formhelper_form_fields WHERE form_id = "'. $form_submission['form_id']. '"');
            // 遍历字段填入值插入数据库
            foreach ($fields as $field) {
                // 根据字段类型填入值
                switch ($field['field_type']) {
                    case 'text':
                        $value = $faker->sentence(3);
                        break;
                    case 'textarea':
                        $value = $faker->sentence(3);
                        break;
                    case 'number':
                        $value = $faker->numberBetween($min = 0, $max = 1000);
                        break;
                    case 'select':
                        $value = $faker->randomElement(json_decode($field['options']));
                        break;
                    case 'switch':
                        $value = $faker->boolean();
                        break;
                    case 'checkbox':
                        $value = json_encode($faker->randomElements(json_decode($field['options']), 2), JSON_UNESCAPED_UNICODE);
                        break;
                    case 'date':
                        $value = $faker->date();
                        break;
                    default:
                        $value = 'unknown';
                        break;
                }

                $form_field_values[] = [
                    'form_submission_id' => $form_submission['id'],
                    'label' => $field['label'],
                    'field_type' => $field['field_type'],
                    'sort' => $field['sort'],
                    'value' => $value,
                ];

            }  
        }
         
        $this->table('formhelper_form_field_values')->insert($form_field_values)->save(); 
    }
}
