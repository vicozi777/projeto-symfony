<?php

namespace App\Controller;

use App\Entity\Categoria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoriaController extends AbstractController
{
    #[Route('/app/categoria', name: 'categoria_listar')]
    public function listar(EntityManagerInterface $em): Response
    {
        $categorias = $em->getRepository(Categoria::class)->findAll();

        return $this->render('categoria/index.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    #[Route('/app/categoria/nova', name: 'categoria_nova')]
    public function nova(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $categoria = new Categoria();
            $categoria->setNome($request->request->get('nome'));

            $em->persist($categoria);
            $em->flush();

            return $this->redirectToRoute('categoria_listar');
        }

        return $this->render('categoria/formulario.html.twig', [
            'categoria' => null,
        ]);
    }

    #[Route('/app/categoria/{id}/editar', name: 'categoria_editar')]
    public function editar(Categoria $categoria, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $categoria->setNome($request->request->get('nome'));
            $em->flush();

            return $this->redirectToRoute('categoria_listar');
        }

        return $this->render('categoria/formulario.html.twig', [
            'categoria' => $categoria,
        ]);
    }

    #[Route('/app/categoria/{id}/excluir', name: 'categoria_excluir', methods: ['POST'])]
    public function excluir(Categoria $categoria, EntityManagerInterface $em): Response
    {
        $em->remove($categoria);
        $em->flush();

        return $this->redirectToRoute('categoria_listar');
    }
}