<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle('Post Title - Hello World!');
        $blogPost->setContent('Post content...');
        $blogPost->setAuthor('Rashid MALLAEV');
        $blogPost->setPublished(new \DateTime('2019-06-09 23:25:04'));
        $blogPost->setSlug('hello-world');

        $manager->persist($blogPost);
        $manager->flush();
    }
}
