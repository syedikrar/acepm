<?php

use App\Contracts\FieldTypeRepository;
use Illuminate\Database\Seeder;

class FieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param \App\Contracts\FieldTypeRepository $repository
     *
     * @return void
     */
    public function run(FieldTypeRepository $repository)
    {
        $repository->create([
            'name'  => 'Trello',
            'slug'  => 'trello',
            'icon'  => 'mdi-trello',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Facebook',
            'slug'  => 'facebook',
            'icon'  => 'mdi-facebook',
            'color' => 'blue',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Google Ads',
            'slug'  => 'google_ads',
            'icon'  => 'mdi-google-ads',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Google Analytics',
            'slug'  => 'google_analytics',
            'icon'  => 'mdi-google-analytics',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Klaviyo',
            'slug'  => 'klaviyo',
            'icon'  => 'mdi-email-outline',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Mail Chimp',
            'slug'  => 'mail_chimp',
            'icon'  => 'mdi-email-variant',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Hootsuite',
            'slug'  => 'hootsuite',
            'icon'  => 'mdi-owl',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Buffer',
            'slug'  => 'buffer',
            'icon'  => 'mdi-view-sequential',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Canva',
            'slug'  => 'canva',
            'icon'  => 'mdi-alpha-c-circle',
            'enabled'   => true
        ]);
        $repository->create([
            'name'  => 'Tickets',
            'slug'  => 'tickets',
            'icon'  => 'mdi-ticket-account',
            'enabled'   => true
        ]);
    }
}
