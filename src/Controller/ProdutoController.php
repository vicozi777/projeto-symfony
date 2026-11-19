<?php

namespace App\Controller;

use App\Entity\Produto;
use App\Repository\CategoriaRepository;
use App\Repository\ProdutoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app/produto')]
class ProdutoController extends AbstractController
{
    #[Route('', name: 'produto_listar')]
    public function listar(ProdutoRepository $repo): Response
    {
        return $this->render('produto/index.html.twig', [
            'produtos' => $repo->findAll(),
        ]);
    }

    #[Route('/novo', name: 'produto_novo')]
    public function novo(Request $request, EntityManagerInterface $em, CategoriaRepository $categorias): Response
    {
        if ($request->isMethod('POST')) {
            $produto = new Produto();
            $produto->setNome($request->request->get('nome'));
            $produto->setQuantidade((int) $request->request->get('quantidade'));
            $produto->setCategoria($categorias->find($request->request->get('categoria')));

            $em->persist($produto);
            $em->flush();

            return $this->redirectToRoute('produto_listar');
        }

        return $this->render('produto/formulario.html.twig', [
            'produto' => null,
            'categorias' => $categorias->findAll(),
        ]);
    }

    #[Route('/{id}/editar', name: 'produto_editar')]
    public function editar(Produto $produto, Request $request, EntityManagerInterface $em, CategoriaRepository $categorias): Response
    {
        if ($request->isMethod('POST')) {
            $produto->setNome($request->request->get('nome'));
            $produto->setQuantidade((int) $request->request->get('quantidade'));
            $produto->setCategoria($categorias->find($request->request->get('categoria')));

            $em->flush();

            return $this->redirectToRoute('produto_listar');
        }

        return $this->render('produto/formulario.html.twig', [
            'produto' => $produto,
            'categorias' => $categorias->findAll(),
        ]);
    }

    #[Route('/{id}/excluir', name: 'produto_excluir', methods: ['POST'])]
    public function excluir(Produto $produto, EntityManagerInterface $em): Response
    {
        $em->remove($produto);
        $em->flush();

        return $this->redirectToRoute('produto_listar');
    }

    #[Route('/{id}/adicionar', name: 'produto_adicionar', methods: ['POST'])]
    public function adicionar(Produto $produto, Request $request, EntityManagerInterface $em): Response
    {
        $qtd = max(0, (int) $request->request->get('quantidade'));
        $produto->setQuantidade($produto->getQuantidade() + $qtd);
        $em->flush();

        return $this->redirectToRoute('produto_listar');
    }

    #[Route('/{id}/remover', name: 'produto_remover', methods: ['POST'])]
    public function remover(Produto $produto, Request $request, EntityManagerInterface $em): Response
    {
        $qtd = max(0, (int) $request->request->get('quantidade'));
        $produto->setQuantidade(max(0, $produto->getQuantidade() - $qtd));
        $em->flush();

        return $this->redirectToRoute('produto_listar');
    }
}