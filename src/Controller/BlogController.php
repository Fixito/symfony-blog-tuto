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
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class BlogController extends AbstractController
{
  #[Route('/', name: 'homepage')]
  public function index(ManagerRegistry $doctrine): Response
  {
    $articles = $doctrine->getRepository(Article::class)->findBy(
      ["isPublished" => true],
      ["publicationDate" => "desc"],
    );

    return $this->render("blog/index.html.twig", [
      "articles" => $articles
    ]);
  }

  #[Route('/add', name: 'article_add')]
  public function add(Request $request, ManagerRegistry $doctrine): Response
  {
    $article = new Article();
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $article->setLastUpdateDate(new \DateTime());

      if ($article->getPicture() !== null) {
        $file = $form->get('picture')->getData();
        $fileName = uniqid() . '.' . $file->guessExtension();

        try {
          $file->move(
            // Le dossier dans lequel le fichier va être charger (image_directory est un paramètre cf.config/services.yaml)
            $this->getParameter('images_directory'),
            $fileName
          );
        } catch (FileException $e) {
          return new Response($e->getMessage());
        }

        $article->setPicture($fileName);
      }

      if ($article->getIsPublished()) {
        $article->setPublicationDate(new \DateTime());
      }

      // on récupère l'entity manager
      $em = $doctrine->getManager();
      // On confie notre entité à l'entity manager (on persiste l'entité)
      $em->persist($article);
      // On exécute la requête
      $em->flush();

      return new Response('L\'article a bien été enregistrer.');
    }

    return $this->render('blog/add.html.twig', [
      'form' => $form->createView()
    ]);
  }

  #[Route('/show/{id}', name: 'article_show')]
  public function show(Article $article): Response
  {
    return $this->render("blog/show.html.twig", [
      "article" => $article
    ]);
  }

  #[Route('/edit/{id}', name: 'article_edit')]
  public function edit($id, ManagerRegistry $doctrine, Article $article, Request $request): Response
  {
    $oldPicture = $article->getPicture();

    // récupère l'entité Article mais on peut le faire plus simple avec le ParamConverter
    // $article = $doctrine->getRepository(Article::class)->find($id);
    $form = $this->createForm(ArticleType::class, $article);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $article->setLastUpdateDate(new DateTime());

      if ($article->getIsPublished()) {
        $article->setPublicationDate(new DateTime());
      }

      if ($article->getPicture() !== null && $article->getPicture() !== $oldPicture) {
        $file = $form->get('picture')->getData();
        $fileName = uniqid() . '.' . $file->guessExtension();

        try {
          $file->move(
            $this->getParameter('images_directory'),
            $fileName
          );
        } catch (FileException $e) {
          return new Response($e->getMessage());
        }

        $article->setPicture($fileName);
      } else {
        $article->setPicture($oldPicture);
      }

      $em = $doctrine->getManager();
      $em->persist($article);
      $em->flush();

      return new Response("L'article a bien été modifieé.");
    }

    return $this->render("blog/edit.html.twig", [
      "article" => $article,
      "form" => $form->createView()
    ]);
  }

  #[Route('/remove/{id}', name: 'article_remove')]
  public function remove($id): Response
  {
    return new Response("<h1>Supprimer l'Article $id</h1>");
  }
}
