<?php

declare(strict_types=1);

namespace App\Tests\api;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use Codeception\Attribute\Depends;
use Symfony\Component\HttpFoundation\Response;

class ItemsCest
{
    /**
     * @var int|null
     */
    private ?int $adidasProduct = null;
    private ?int $nikeProduct = null;

    public function _before(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $this->adidasProduct = $I
            ->haveInRepository(
                Product::class,
                ['name' => 'adidas', 'price' => 199.99]
            );
        $this->nikeProduct = $I
            ->haveInRepository(
                Product::class,
                ['name' => 'nike', 'price' => 199.99]
            );
    }

    public function createItem(ApiTester $I): void
    {
        $I->sendPost(
            '/items',
            [
                'product' => $this->adidasProduct,
                'quantity' => 1
            ]
        );
        $I->canSeeResponseCodeIs(Response::HTTP_CREATED);
    }

    public function removeItem(ApiTester $I): void
    {
        $I->sendPost(
            '/items',
            [
                'product' => $this->adidasProduct,
                'quantity' => 1
            ]
        );
        $I->canSeeResponseCodeIs(Response::HTTP_CREATED);
        $data = json_decode($I->grabResponse());

        $I->sendDelete('/items/' . $data->id);
        $I->dontSeeInRepository(CartItem::class, ['id' => $data->id]);
    }

    public function getItems(ApiTester $I): void
    {
        $adidasProduct = $I->grabEntityFromRepository(
            Product::class,
            [
                'id' => $this->adidasProduct,
            ]
        );
        $nikeProduct = $I->grabEntityFromRepository(
            Product::class,
            [
                'id' => $this->nikeProduct,
            ]
        );
        $I->haveInRepository(
            CartItem::class,
            [
                'product' => $adidasProduct,
                'quantity' => 1,
            ]
        );
        $I->haveInRepository(
            CartItem::class,
            [
                'product' => $nikeProduct,
                'quantity' => 1,
            ]
        );

        $I->sendGet('/items');
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
        $I->canSeeResponseMatchesJsonType(
            [
                'id' => 'integer',
                'product' => 'array',
                'quantity' => 'integer'
            ]
        );
        $data = json_decode($I->grabResponse());
        $I->assertCount(2, $data);
    }

    public function getItem(ApiTester $I): void
    {
        $I->sendPost(
            '/items',
            [
                'product' => $this->adidasProduct,
                'quantity' => 1
            ]
        );
        $I->canSeeResponseCodeIs(Response::HTTP_CREATED);
        $data = json_decode($I->grabResponse());

        $I->sendGet('/items/' . $data->id);
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
        $I->canSeeResponseMatchesJsonType(
            [
                'id' => 'integer',
                'product' => 'array',
                'quantity' => 'integer'
            ]
        );
    }

    public function patchItemAddQuantitySubtractQuantity(ApiTester $I): void
    {
        $content = $I->sendPost(
            '/items',
            [
                'product' => $this->adidasProduct,
                'quantity' => 1
            ]
        );
        $data = json_decode($content);
        $oldQuantity = $data->quantity;
        $content = $I->sendPatch('/items/' . $data->id . '/add');
        $data = json_decode($content);
        $I->assertSame($oldQuantity + 1, $data->quantity);
        $oldQuantity = $data->quantity;
        $content = $I->sendPatch('/items/' . $data->id . '/subtract');
        $data = json_decode($content);
        $I->assertSame($oldQuantity - 1, $data->quantity);
    }
}
