<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/books', name: 'books_index', methods: 'GET')]
    public function index(BookRepository $bookRepository): JsonResponse
    {
        return $this->json([
            'data' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/books', name: 'books_store', methods: 'POST')]
    public function store(Request $request, BookRepository $bookRepository): JsonResponse
    {
        if ($request->getContentType() !== 'json') {
            throw $this->createNotFoundException();
        }
        $data = json_decode($request->getContent(), true);

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Manaus'))); //
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Manaus'))); //

        $bookRepository->save($book, true);

        return $this->json([
            'message' => 'Book created successfully!',
            'data' => $book,
        ], 201);
    }

    #[Route('/books/{book}', name: 'books_show', methods: 'GET')]
    public function show(int $book, BookRepository $bookRepository): JsonResponse
    {
        $data = $bookRepository->find($book);
        if (!$data) {
            throw $this->createNotFoundException();
        }

        return $this->json([
            'data' => $data,
        ]);
    }

    #[Route('/books/{book}', name: 'books_update', methods: ['PUT', 'PATCH'])]
    public function update(int $book, Request $request, BookRepository $bookRepository, ManagerRegistry $registry): JsonResponse
    {
        $book = $bookRepository->find($book);
        if (!$book) {
            throw $this->createNotFoundException();
        }

        if ($request->getContentType() !== 'json') {
            throw $this->createNotFoundException();
        }
        $data = json_decode($request->getContent(), true);


        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Manaus'))); //

        $registry->getManager()->flush();

        return $this->json([
            'message' => 'Book updated successfully!',
            'data' => $book,
        ]);
    }
}
