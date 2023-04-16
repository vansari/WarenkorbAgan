<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Handler\CartHandler;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
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

    #[Route('', name: 'app_cart_create', methods: ['POST'])]
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

    #[Route('/{id}/items', name: 'app_cart_add_item', methods: ['PUT'])]
    public function addItemToCard(Request $request, ?Cart $cart, ValidatorInterface $validator): Response|JsonResponse
    {
        if (null === $cart) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $item = $this->cartHandler->createFromItemJson($request->getContent());
        if (($violations = $validator->validate($item, groups: ['item:create']))->count()) {
            return new JsonResponse($violations, Response::HTTP_BAD_REQUEST);
        }

        $cart->addItem($item);
        if (($violation = $validator->validate($cart, groups: ['item:create']))->count()) {
            return new JsonResponse($violation, Response::HTTP_BAD_REQUEST);
        }

        $this->cartRepository->save($cart, true);

        return $this->json($cart);
    }

    #[Route('/{id}/items/{itemId}', name: 'app_cart_remove_item', methods: ['DELETE'])]
    public function removeItemFromCard(?Cart $cart, int $itemId): Response|JsonResponse
    {
        if (null === $cart) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $this->cartRepository->save($cart->removeItemById($itemId), true);

        return $this->json($cart);
    }
}
