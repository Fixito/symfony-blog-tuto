<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
  #[Route('/', name: 'homepage')]
  public function index(): Response
  {
    return $this->render("blog/index.html.twig");
  }

  #[Route('/add', name: 'article_add')]
  public function add(Request $request, ManagerRegistry $doctrine): Response
  {
    $article = new Article();
    $form = $this->createForm(ArticleType::class, $article);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $article->setLastUpdateDate(new \DateTime());

      if ($article->getIsPublished()) {
        $article->setPublicationDate(new \DateTime());
      }

      // on récupère l'entity manager
      $em = $doctrine->getManager();
      $em->persist($article); // On confie notre entité à l'entity manager (on persiste l'entité)
      $em->flush(); // On exécute la requête

      return new Response('L\'article a bien été enregistrer.');
    }
  }

  #[Route('/show/{url}', name: 'article_show')]
  public function show($url): Response
  {
    return $this->render("blog/show.html.twig", [
      "slug" => $url
    ]);
  }

  #[Route('/edit/{id}', name: 'article_edit')]
  public function edit($id): Response
  {
    return $this->render("blog/edit.html.twig", [
      "id" => $id
    ]);
  }

  #[Route('/remove/{id}', name: 'article_remove')]
  public function remove($id): Response
  {
    return new Response("<h1>Supprimer l'Article $id</h1>");
  }
}
