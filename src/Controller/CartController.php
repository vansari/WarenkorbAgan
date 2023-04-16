<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Cart;
use App\Handler\CartHandler;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/carts')]
class CartController extends AbstractController
{

    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly CartHandler    $cartHandler
    ) {

    }
    #[Route('', name: 'app_cart_list', methods: ['GET'])]
    public function getList(): JsonResponse
    {
        return $this->json($this->cartRepository->findAll());
    }

    #[Route('', name: 'app_item_create', methods: ['POST'])]
    public function createCart(Request $request, ValidatorInterface $validation): Response|JsonResponse
    {
        $cart = $this->cartHandler->createFromCartJson($request->getContent());
        $violation = $validation->validate($cart, groups: ['cart:create', 'item:create']);
        if ($violation->count()) {
            return new JsonResponse($violation, Response::HTTP_BAD_REQUEST);
        }
        foreach ($cart->getItems() as $item) {
            $violation = $validation->validate($item, groups: ['cart:create', 'item:create']);
            if ($violation->count()) {
                return new JsonResponse($violation, Response::HTTP_BAD_REQUEST);
            }
        }
        $this->cartRepository->save($cart, true);

        return $this->json($cart, Response::HTTP_CREATED);
    }
}
