<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    private const POSTS = [
        [
            'id' => 1,
            'slug' => 'hello-world',
            'title' => 'Hello world!'
        ],
        [
            'id' => 2,
            'slug' => 'another-post',
            'title' => 'This is another post!',
        ],
        [
            'id' => 3,
            'slug' => 'last-example',
            'title' => 'This is the last example',
        ],
    ];

    /**
     * @Route("/{page}", name="blog_list", defaults={"page"=1})
     */
    public function list($page){
        return new JsonResponse(
            [
                'page' => $page,
                'data' => self::POSTS
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_post", requirements={"id"="\d+"})
     */
    public function post($id){
        return new JsonResponse(self::POSTS[
            array_search($id, array_column(self::POSTS, 'id'))
        ]);
    }

    /**
     * @Route("/post/{slug}", name="blog_post_by_slug")
     */
    public function postBySlug($slug){
        return new JsonResponse(self::POSTS[
            array_search($slug, array_column(self::POSTS, 'slug'))
        ]);
    }
}