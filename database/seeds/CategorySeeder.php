<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //--------------------------------//
        DB::table('categories')->insert([
            'title' => 'Graphics & Design',
        ]);
        DB::table('categories')->insert([
            'title' => 'Logo Design',
            'parent' => 1
        ]);
        DB::table('categories')->insert([
            'title' => 'Brand Style Guides',
            'parent' => 1
        ]);
        DB::table('categories')->insert([
            'title' => 'Game Art',
            'parent' => 1
        ]);
        DB::table('categories')->insert([
            'title' => 'Graphics for Streamers',
            'parent' => 1
        ]);
        DB::table('categories')->insert([
            'title' => 'Business Cards & Stationery',
            'parent' => 1
        ]);
        DB::table('categories')->insert([
            'title' => 'Pattern Design',
            'parent' => 1
        ]);
        DB::table('categories')->insert([
            'title' => 'Brochure Design',
            'parent' => 1
        ]);
        //--------------------------------//

        //--------------------------------//
        DB::table('categories')->insert([
            'title' => 'Digital Marketing',
        ]);
        DB::table('categories')->insert([
            'title' => 'Social Media Marketing',
            'parent' => 9
        ]);
        DB::table('categories')->insert([
            'title' => 'Search Engine Optimization (SEO)',
            'parent' => 9
        ]);
        DB::table('categories')->insert([
            'title' => 'Social Media Advertising',
            'parent' => 9
        ]);
        DB::table('categories')->insert([
            'title' => 'Content Marketing',
            'parent' => 9
        ]);
        DB::table('categories')->insert([
            'title' => 'Podcast Marketing',
            'parent' => 9
        ]);
        DB::table('categories')->insert([
            'title' => 'Video Marketing',
            'parent' => 9
        ]);
        //--------------------------------//

        //--------------------------------//
        DB::table('categories')->insert([
            'title' => 'Writing & Translation',
        ]);
        DB::table('categories')->insert([
            'title' => 'Articles & Blog Posts',
            'parent' => 16
        ]);
        DB::table('categories')->insert([
            'title' => 'Translation',
            'parent' => 16
        ]);
        DB::table('categories')->insert([
            'title' => 'Proofreading & Editing',
            'parent' => 16
        ]);
        DB::table('categories')->insert([
            'title' => 'Website Content',
            'parent' => 16
        ]);
        DB::table('categories')->insert([
            'title' => 'Book & eBook Writing',
            'parent' => 16
        ]);
        DB::table('categories')->insert([
            'title' => 'Brand Voice & Tone',
            'parent' => 16
        ]);
        DB::table('categories')->insert([
            'title' => 'UX Writing',
            'parent' => 16
        ]);
        DB::table('categories')->insert([
            'title' => 'Resume Writing',
            'parent' => 16
        ]);
        //--------------------------------//

        //--------------------------------//
        DB::table('categories')->insert([
            'title' => 'Music & Audio',
        ]);
        DB::table('categories')->insert([
            'title' => 'Voice Over',
            'parent' => 25
        ]);
        DB::table('categories')->insert([
            'title' => 'Mixing & Mastering',
            'parent' => 25
        ]);
        DB::table('categories')->insert([
            'title' => 'Producers & Composers',
            'parent' => 25
        ]);
        DB::table('categories')->insert([
            'title' => 'Singers & Vocalists',
            'parent' => 25
        ]);
        DB::table('categories')->insert([
            'title' => 'Session Musicians',
            'parent' => 25
        ]);
        DB::table('categories')->insert([
            'title' => 'Online Music Lessons',
            'parent' => 25
        ]);
        DB::table('categories')->insert([
            'title' => 'Songwriters',
            'parent' => 25
        ]);
        DB::table('categories')->insert([
            'title' => 'Beat Making',
            'parent' => 25
        ]);
        //--------------------------------//

        //--------------------------------//
        DB::table('categories')->insert([
            'title' => 'Programming & Tech',
        ]);
        DB::table('categories')->insert([
            'title' => 'WordPress',
            'parent' => 34
        ]);
        DB::table('categories')->insert([
            'title' => 'Website Builders & CMS',
            'parent' => 34
        ]);
        DB::table('categories')->insert([
            'title' => 'E-Commerce Development',
            'parent' => 34
        ]);
        DB::table('categories')->insert([
            'title' => 'Game Development',
            'parent' => 34
        ]);
        DB::table('categories')->insert([
            'title' => 'Development for Streamers',
            'parent' => 34
        ]);
        DB::table('categories')->insert([
            'title' => 'Web Programming',
            'parent' => 34
        ]);
        DB::table('categories')->insert([
            'title' => 'Desktop Applications',
            'parent' => 34
        ]);
        //--------------------------------//

        //--------------------------------//
        DB::table('categories')->insert([
            'title' => 'Business',
        ]);
        DB::table('categories')->insert([
            'title' => 'Virtual Assistant',
            'parent' => 42
        ]);
        DB::table('categories')->insert([
            'title' => 'E-Commerce Management',
            'parent' => 42
        ]);
        DB::table('categories')->insert([
            'title' => 'Customer Care',
            'parent' => 42
        ]);
        DB::table('categories')->insert([
            'title' => 'Market Research',
            'parent' => 42
        ]);
        DB::table('categories')->insert([
            'title' => 'Business Plans',
            'parent' => 42
        ]);
        DB::table('categories')->insert([
            'title' => 'HR Consulting',
            'parent' => 42
        ]);
        DB::table('categories')->insert([
            'title' => 'Business Consulting',
            'parent' => 42
        ]);
        DB::table('categories')->insert([
            'title' => 'Flyer Distribution',
            'parent' => 42
        ]);
        //--------------------------------//
    }
}
