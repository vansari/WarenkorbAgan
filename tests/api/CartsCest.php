<?php

namespace App\Tests\api;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Tests\ApiTester;
use Symfony\Component\HttpFoundation\Response;

class CartsCest
{
    private int $adidasProduct;
    private int $nikeProduct;

    public function _before(ApiTester $I): void
    {
        $this->adidasProduct = $I->haveInRepository(Product::class, ['name' => 'adidas', 'price' => 199.99]);
        $this->nikeProduct = $I->haveInRepository(Product::class, ['name' => 'nike', 'price' => 199.99]);
    }

    // tests
    public function getEmptyCarts(ApiTester $I): void
    {
        $I->sendGet('/carts');
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
        $I->seeResponseMatchesJsonType([]);
    }

    public function createCart(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost(
            '/carts',
            [
                'items' => [
                    [
                        'product' => $this->adidasProduct,
                        'quantity' => 1,
                    ],
                    [
                        'product' => $this->nikeProduct,
                        'quantity' => 1,
                    ]
                ]
            ]
        );
        $I->canSeeResponseCodeIs(Response::HTTP_CREATED);
        $I->canSeeResponseIsJson();
        $result = json_decode($I->grabResponse(), true);
        $I->assertNotNull($result['id']);
        $I->seeInRepository(Cart::class, ['id' => $result['id']]);
        $I->seeInRepository(CartItem::class, ['id' => $result['items'][0]['id']]);
        $I->seeInRepository(CartItem::class, ['id' => $result['items'][1]['id']]);
    }

    public function createCartWillFailWithNoItems(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost(
            '/carts',
            [

            ]
        );
        $I->canSeeResponseCodeIs(Response::HTTP_BAD_REQUEST);
    }

    public function createCartWillFailWithInvalidItems(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost(
            '/carts',
            [
                'items' => [
                    [
                        'product' => $this->adidasProduct,
                    ],
                ],
            ],
        );
        $I->canSeeResponseCodeIs(Response::HTTP_BAD_REQUEST);
    }

    public function addNewItemToCard(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost(
            '/carts',
            [
                'items' => [
                    [
                        'product' => $this->adidasProduct,
                        'quantity' => 1,
                    ],
                ]
            ]
        );
        $I->canSeeResponseCodeIs(Response::HTTP_CREATED);
        $I->canSeeResponseIsJson();
        $result = json_decode($I->grabResponse(), true);
        $I->assertNotNull($result['id']);
        $I->seeInRepository(Cart::class, ['id' => $result['id']]);
        $I->seeInRepository(CartItem::class, ['id' => $result['items'][0]['id']]);

        $I->sendPut(
            '/carts/' . $result['id'] . '/items',
            [
                'product' => $this->nikeProduct,
                'quantity' => 2,
            ]
        );
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
        $result = json_decode($I->grabResponse(), true);
        $newCart = $I->grabEntityFromRepository(Cart::class, ['id' => $result['id']]);
        $I->assertSame(599.97, $newCart->getTotal());
    }

    public function removeItemFromCard(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost(
            '/carts',
            [
                'items' => [
                    [
                        'product' => $this->adidasProduct,
                        'quantity' => 1,
                    ],
                ]
            ]
        );
        $I->canSeeResponseCodeIs(Response::HTTP_CREATED);
        $I->canSeeResponseIsJson();
        $result = json_decode($I->grabResponse(), true);
        $I->assertNotNull($result['id']);
        $I->seeInRepository(Cart::class, ['id' => $result['id']]);
        $I->seeInRepository(CartItem::class, ['id' => $result['items'][0]['id']]);

        $I->sendDelete(
            '/carts/' . $result['id'] . '/items/' . $result['items'][0]['id']
        );
        $I->canSeeResponseCodeIsSuccessful();
        $I->dontSeeInRepository(CartItem::class, ['id' => $result['items'][0]['id']]);
    }
}
