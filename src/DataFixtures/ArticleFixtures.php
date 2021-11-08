<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Repository\TagRepository;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{

    const MAX_ARTICLES = 1000;
    public function __construct(protected TagRepository $tagRepository, protected CategoryRepository $categoryRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('de_CH');
        $categories = $this->categoryRepository->findAll();
        $tags = $this->tagRepository->findAll();
        for ($i = 1; $i <= self::MAX_ARTICLES; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence(3));
            $article->setCaption($faker->realTextBetween(20, 200));
            $article->setText($faker->realTextBetween(300, 2500));
            $article->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisDecade()));
            $article->setCategory($faker->randomElement($categories));

            foreach ($faker->randomElements($tags, $faker->numberBetween(1, 12)) as $tag) {
                $article->addTag($tag);
            }

            $manager->persist($article);
            if ($i % 100 === 0) {
                $manager->flush();
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            TagFixtures::class,
        ];
    }
}
