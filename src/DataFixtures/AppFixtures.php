<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const USERS = [
        [
            'username' => 'rashid_mall',
            'email' => 'rashid@mail.com',
            'name' => 'Rashid Mall',
            'password' => 'Pass12345',
            //'roles' => [User::ROLE_ADMIN],
        ],
        [
            'username' => 'bob_sanchez',
            'email' => 'bob@mail.com',
            'name' => 'Bob Sanchez',
            'password' => 'Bob12345',
            //'roles' => [User::ROLE_USER],
        ],
        [
            'username' => 'alice_wonderland',
            'email' => 'alice@mail.com',
            'name' => 'Alice Wonderland',
            'password' => 'Alice12345',
            //'roles' => [User::ROLE_USER],
        ]
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadUsers(ObjectManager $manager){
        foreach (self::USERS as $userData){
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setName($userData['name']);
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $userData['password'])
            );

            $this->setReference($userData['username'], $user);

            $manager->persist($user);
        }
        $manager->flush();
    }

    public function loadBlogPosts(ObjectManager $manager){
        for($i = 0; $i < 100; $i++){
            $post = new BlogPost();
            $post->setTitle($this->faker->realText(30));
            $post->setContent($this->faker->realText());
            $post->setSlug($this->faker->slug);
            $post->setPublished($this->faker->dateTimeThisYear);
            $post->setAuthor(
                $this->getReference(self::USERS[rand(0, count(self::USERS)-1)]['username'])
            );

            $this->setReference("blog_post_$i", $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager){
        for ($i = 0; $i < 100; $i++){
            for($j = 0; $j < rand(1, 10); $j++){
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear());
                $comment->setAuthor(
                    $this->getReference(self::USERS[rand(0, count(self::USERS)-1)]['username'])
                );
                $comment->setBlogPost($this->getReference("blog_post_$i"));

                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
