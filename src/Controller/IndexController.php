<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function home()
  {
    if ($this->IsGranted("ROLE_ADMIN")) {
      return $this->redirectToRoute('admin');
    }
    return $this->redirectToRoute('app_login');
  }
}