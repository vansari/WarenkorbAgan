<?php

namespace App\Tests\api;

use App\Entity\Product;
use App\Tests\ApiTester;
use Symfony\Component\HttpFoundation\Response;

class ProductsCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveInRepository(
            Product::class,
            ['name' => 'Adidas Schuhe', 'price' => 199.99]
        );
        $I->haveInRepository(
            Product::class,
            ['name' => 'Nike Schuhe', 'price' => 299.99]
        );
    }

    // tests
    public function getProducts(ApiTester $I): void
    {
        $I->sendGet('/products');
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
        $I->canSeeResponseMatchesJsonType(
            [
                'id' => 'integer',
                'name' => 'string',
                'price' => 'float'
            ]
        );
    }

    public function getProduct(ApiTester $I): void
    {
        $productId = $I->haveInRepository(Product::class, ['name' => 'Neue Schuhe', 'price' => 19.99]);
        $I->sendGet('/products/' . $productId);
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
        $I->canSeeResponseMatchesJsonType(
            [
                'id' => 'integer',
                'name' => 'string',
                'price' => 'float'
            ]
        );
    }

    public function getNotFoundResponse(ApiTester $I): void
    {
        $I->sendGet('/products/1111');
        $I->canSeeResponseCodeIs(Response::HTTP_NOT_FOUND);
    }
}
