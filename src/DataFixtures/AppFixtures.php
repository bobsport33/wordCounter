<?php

namespace App\DataFixtures;

use App\Entity\UrlEntry;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $url = new UrlEntry();
        $url->setUrl("http://www.amazon.com");
        $url->setNotes("this is a note");
        $url->setCreatedDate(new DateTime());
        $manager->persist($url);

        $manager->flush();
    }
}
