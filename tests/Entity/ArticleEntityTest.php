<?php

namespace App\Tests\Entity;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\ORMSqliteDatabaseTool;

class ArticleEntityTest extends KernelTestCase
{
    protected ?ORMSqliteDatabaseTool $databaseTool = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testRepositoryCount(): void
    {
        // On charge les fixtures (les articles)
        $articles = $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__) . '/Fixtures/ArticleTestFixtures.yaml',
        ]);

        // On compte le nombre d'entrée dans la table article
        $articles = self::getContainer()->get(ArticleRepository::class)->count([]);

        $this->assertEquals(20, $articles);
    }
}
