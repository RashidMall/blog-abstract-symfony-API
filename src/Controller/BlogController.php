<?php


namespace App\Controller;


use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_list", defaults={"page"=1}, requirements={"page"="\d+"})
     */
    public function list($page, Request $request){
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();

        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_post_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_post_by_id", requirements={"id"="\d+"})
     */
    public function post($id){
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $blogPost = $repository->find($id);

        return $this->json(
            $blogPost
        );
    }

    /**
     * @Route("/post/{slug}", name="blog_post_by_slug")
     */
    public function postBySlug($slug){
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $blogPost = $repository->findOneBy(['slug' => $slug]);

        return $this->json(
            $blogPost
        );
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     */
    public function add(Request $request){
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize(
            $request->getContent(), BlogPost::class, 'json'
        );

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }
}