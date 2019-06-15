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

    private const POSTS = [
        [
            'title' => 'Hello World!',
            'content' => 'To cut the mustard is to meet a required standard, or to meet expectations.',
            'slug' => 'hello-world'
        ],
        [
            'title' => 'If You Can\'t Stand the Heat, Get Out of the Kitchen',
            'content' => 'One should discontinue with a task if they are unable to cope with it due to pressure.',
            'slug' => 'get-out-of-kitchen'
        ],
        [
            'title' => 'Keep Your Eyes Peeled',
            'content' => 'To be watchful; paying careful attention to something.',
            'slug' => 'eyes-peeled'
        ],
        [
            'title' => 'Beating a Dead Horse',
            'content' => 'To bring up an issue that has already been resolved.',
            'slug' => 'dead-horse'
        ],
        [
            'title' => 'Tough It Out',
            'content' => 'To remain resillient even in hard times; enduring.',
            'slug' => 'tough-out'
        ],
        [
            'title' => 'Scot-free',
            'content' => 'Getting away freely from custody, punishment, or any type of risky situation.',
            'slug' => 'scott-free'
        ],
        [
            'title' => 'Birds of a Feather Flock Together',
            'content' => 'People tend to associate with others who share similar interests or values.',
            'slug' => 'birds-feather'
        ],
        [
            'title' => 'When the Rubber Hits the Road',
            'content' => 'When something is about to begin, get serious, or put to the test.',
            'slug' => 'rubber-hits-road'
        ],
        [
            'title' => 'Poke Fun At',
            'content' => 'Making fun of something or someone; ridicule.',
            'slug' => 'poke-fun'
        ],
        [
            'title' => 'What Goes Up Must Come Down',
            'content' => 'Things that go up must eventually return to the earth due to gravity.',
            'slug' => 'up-and-down'
        ]
    ];

    private const COMMENTS = [
        [
            'content' => 'After one look at this planet any visitor from outer space would say “I WANT TO SEE THE MANAGER.”'
        ],
        [
            'content' => 'Life is full of temporary situations, ultimately ending in a permanent solution.'
        ],
        [
            'content' => 'I like to wax my legs and stick the hair on my back. Why? Because it keeps my back warm. There\'s method in my madness.'
        ],
        [
            'content' => 'If I roll once and you roll twice. What does that mean?'
        ],
        [
            'content' => 'Don\'t you find it Funny that after Monday(M) and Tuesday(T), the rest of the week says WTF?'
        ],
        [
            'content' => 'Sorry, I can\'t hangout. My uncle\'s cousin\'s sister in law\'s best friend\'s insurance agent\'s roommate\'s pet goldfish died. Maybe next time.'
        ],
        [
            'content' => 'Why go to college? There\'s Google.'
        ],
        [
            'content' => 'A good lawyer knows the law; a clever one takes the judge to lunch.'
        ],
        [
            'content' => 'Life is full of temporary situations, ultimately ending in a permanent solution.'
        ],
        [
            'content' => 'I like to say things twice, say things twice. It can get annoying though, annoying though.'
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
