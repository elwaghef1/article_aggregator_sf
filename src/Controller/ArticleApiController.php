<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ArticleApiController extends AbstractController
{
    private $entityManager;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/api/articles', name: 'get_articles', methods: ['GET'])]
    public function getArticles(): JsonResponse
    {
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        $data = $this->serializer->serialize($articles, 'json', ['groups' => 'article:read']);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/api/articles/{id}', name: 'get_article', methods: ['GET'])]
    public function getArticle(Article $article): JsonResponse
    {
        $data = $this->serializer->serialize($article, 'json', ['groups' => 'article:read']);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/api/articles', name: 'create_article', methods: ['POST'])]
    public function createArticle(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $article = new Article();
        $article->setSourceName($data['sourceName']);
        $article->setName($data['name']);
        $article->setContent($data['content']);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return new JsonResponse('Article created', 201);
    }

    #[Route('/api/articles/{id}', name: 'update_article', methods: ['PUT'])]
    public function updateArticle(Request $request, Article $article): JsonResponse
    {
        $this->serializer->deserialize($request->getContent(), Article::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $article]);
        $this->entityManager->flush();

        return new JsonResponse('Article updated', 200);
    }

    #[Route('/api/articles/{id}', name: 'delete_article', methods: ['DELETE'])]
    public function deleteArticle(Article $article): JsonResponse
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        return new JsonResponse('Article deleted', 204);
    }
}
