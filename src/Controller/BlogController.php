<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
  #[Route('/blog', name: 'app_blog')]
  public function index(): Response
  {
    return new Response("<h1>Hello World!</h1>");
  }
}
