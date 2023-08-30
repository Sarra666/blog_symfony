<?php

namespace App\Fixtures\Providers;

class CategorieProvider
{
    public function randomTag(): string
    {
        $tags = [
            'PHP',
            'Symfony',
            'Javascript',
            'React',
            'VueJS',
            'Angular',
            'Frontend',
            'Backend',
            'Fullstack',
            'API',
            'REST',
            'GraphQL',
            'SQL',
            'MySQL'
        ];


        return $tags[array_rand($tags)];
    }

   
}