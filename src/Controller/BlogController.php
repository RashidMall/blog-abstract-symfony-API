<?php


namespace App\Controller;


use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route(
     *     "/{page}",
     *     name="blog_list",
     *     defaults={"page"=1},
     *     requirements={"page"="\d+"},
     *     methods={"GET"}
     *     )
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
     * @Route(
     *     "/post/{id}",
     *     name="blog_post_by_id",
     *     requirements={"id"="\d+"},
     *     methods={"GET"}
     *     )
     */
    public function post(BlogPost $blogPost){
        return $this->json(
            $blogPost
        );
    }

    /**
     * @Route(
     *     "/post/{slug}",
     *     name="blog_post_by_slug",
     *     methods={"GET"}
     *     )
     */
    public function postBySlug(BlogPost $blogPost){
        return $this->json(
            $blogPost
        );
    }

    /**
     * @Route(
     *     "/add",
     *     name="blog_add",
     *     methods={"POST"}
     *     )
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

    /**
     * @Route(
     *     "/delete/{id}",
     *     name="blog_delete",
     *     methods={"DELETE"}
     *     )
     */
    public function delete(BlogPost $blogPost){
        $em = $this->getDoctrine()->getManager();
        $em->remove($blogPost);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}