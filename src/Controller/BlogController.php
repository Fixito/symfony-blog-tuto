<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
  #[Route('/', name: 'homepage')]
  public function index(): Response
  {
    return $this->render("blog/index.html.twig");
  }

  #[Route('/add', name: 'article_add')]
  public function add(): Response
  {
    return $this->render("blog/add.html.twig");
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
