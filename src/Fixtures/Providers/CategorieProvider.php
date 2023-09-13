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
            'Framework',
            'Backend',
            'Frontend',
            'Fullstack',
            'API',
            'REST',
            'GraphQL',
            'SQL',
            'Mysql'
        ];

        return $tags[array_rand($tags)];
    }
}
