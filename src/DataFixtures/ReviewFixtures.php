<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture
{
    private const N = 10;

    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < self::N; $i++) {
            $review = new Review();
            $review->setAuthor("Author $i");
            $review->setMessage("Message $i");
            $manager->persist($review);
        }

        $manager->flush();
    }
}
