<?php

namespace App\DataFixtures;

use App\Entity\Document;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i=1; $i<50; $i++)
        {
            $document = new Document();
            $document->setName("Some new document " . $i);
            $document->setDescription("Some description " . $i);
            $manager->persist($document);
        }
        $manager->flush();
    }
}
