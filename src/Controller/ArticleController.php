<?php

namespace App\Controller;

use App\Service\ArticleAggregator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private $articleAggregator;

    public function __construct(ArticleAggregator $articleAggregator)
    {
        $this->articleAggregator = $articleAggregator;
    }

    #[Route('/articles', name: 'homepage')]
    public function homepage(): Response
    {
        return $this->render('article/index.html.twig');
    }

    #[Route('/articles/db', name: 'article_db')]
    public function dbArticles(Request $request, PaginatorInterface $paginator): Response
    {
        // Réinitialiser les articles et récupérer ceux de la base de données
        $this->articleAggregator->resetArticles();
        $this->articleAggregator->appendDatabase();
        $dbArticles = $this->articleAggregator->getArticles();

        $pagination = $paginator->paginate($dbArticles, $request->query->getInt('page', 1), 9);

        return $this->render('article/db_articles.html.twig', [
            'articles' => $pagination,
        ]);
    }

    #[Route('/articles/rss', name: 'article_rss')]
    public function rssArticles(Request $request, PaginatorInterface $paginator): Response
    {
        // Réinitialiser les articles et récupérer ceux du flux RSS
        $this->articleAggregator->resetArticles();
        $this->articleAggregator->appendRss('Le Monde', 'http://www.lemonde.fr/rss/une.xml');
        $rssArticles = $this->articleAggregator->getArticles();

        $pagination = $paginator->paginate($rssArticles, $request->query->getInt('page', 1), 9);

        return $this->render('article/rss_articles.html.twig', [
            'articles' =>  $pagination,
        ]);
    }
}
