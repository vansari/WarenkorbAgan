<?php


namespace App\Tests\Api;

use App\Tests\ApiTester;

class CartsCest
{
    public function _before(ApiTester $I): void
    {
        $I->haveInRepository(Product::class, ['name' => 'Adidas Schuhe', 'price' => 199.99]);
    }

    // tests
    public function getCarts(ApiTester $I): void
    {
        $I->sendGet('/carts');
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
        $I->seeResponseMatchesJsonType([]);
    }

    public function createWarenkorb(ApiTester $I): void
    {
        $I->sendPost(
            '/warenkorb',
            [
                'product' => '/products/1',
                'quantity' => 1,
            ]
        );
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
        $result = json_decode($I->grabResponse(), true);
    }
}
