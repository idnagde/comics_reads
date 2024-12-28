<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Action',
            'Adaptation',
            'Adult',
            'Adventure',
            'Another chance',
            'Apocalypse',
            'Comedy',
            'Coming Soon',
            'Crazy MC',
            'Cultivation',
            'Cute',
            'Demon',
            'Drama',
            'Dungeons',
            'Ecchi',
            'Fantasy',
            'Fight',
            'Game',
            'Genius',
            'Genius MC',
            'Harem',
            'Hero',
            'Historical',
            'Isekai',
            'Josei',
            'Kool Kids',
            'Loli',
            'Magic',
            'Martial Arts',
            'Mature',
            'Mecha',
            'Modern Setting',
            'Monsters',
            'Murim',
            'Mystery',
            'Necromancer',
            'Noble',
            'Overpowered',
            'Pets',
            'Post-Apocalyptic',
            'Psychological',
            'Rebirth',
            'Regression',
            'Reincarnation',
            'Return',
            'Returned',
            'Returner',
            'Revenge',
            'Romance',
            'School',
            'School Life',
            'Sci-fi',
            'Seinen',
            'Shoujo',
            'Shounen',
            'Slice of Life',
            'Sports',
            'Super Hero',
            'Superhero',
            'Supernatural',
            'Survival',
            'Suspense',
            'System',
            'Thriller',
            'Time Travel',
            'Time Travel (Future)',
            'Tower',
            'Tragedy',
            'Transmigrating',
            'Video Game',
            'Video Games',
            'Villain',
            'Violence',
            'Virtual Game',
            'Virtual Reality',
            'Virtual World',
            'Webtoon',
            'Wuxia'
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate(['name' => $genre]);
        }
    }
}
