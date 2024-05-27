<?php

namespace App\Service;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ArticleAggregator
{
    private $em;
    private $httpClient;
    private $cache;
    private $articles;

    public function __construct(EntityManagerInterface $em, HttpClientInterface $httpClient, CacheInterface $cache)
    {
        $this->em = $em;
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->articles = [];
    }

    /**
     * Récupère les articles de la base de données.
     */
    public function appendDatabase()
    {
        $articleRepository = $this->em->getRepository(Article::class);
        $articles = $articleRepository->findAll();
        foreach ($articles as $article) {
            $this->articles[] = $article;
        }
    }

    /**
     * Récupère les articles d'un flux RSS donné sans les persister en base de données.
     *
     * @param string $sourceName
     * @param string $feedUrl
     */
    public function appendRss(string $sourceName, string $feedUrl)
    {
        // Utiliser le cache pour éviter des requêtes répétitives vers le flux RSS
        $rssContent = $this->cache->get('rss_' . md5($feedUrl), function (ItemInterface $item) use ($feedUrl) {
            // Mettre en cache pendant 15 minutes
            $item->expiresAfter(900);
            $response = $this->httpClient->request('GET', $feedUrl);
            return $response->getContent();
        });

        $rss = simplexml_load_string($rssContent);

        foreach ($rss->channel->item as $item) {
            $article = new Article();
            $article->setSourceName($sourceName);
            $article->setName((string) $item->title);
            $article->setContent((string) $item->description);
            $this->articles[] = $article;
        }
    }

    /**
     * Retourne la liste des articles agrégés.
     *
     * @return Article[]
     */
    public function getArticles(): array
    {
        return $this->articles;
    }

    /**
     * Réinitialise la liste des articles.
     */
    public function resetArticles()
    {
        $this->articles = [];
    }
}
