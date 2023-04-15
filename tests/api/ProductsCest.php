<?php


namespace App\Tests\api;

use App\Tests\ApiTester;
use Symfony\Component\HttpFoundation\Response;

class ProductsCest
{
    public function _before(ApiTester $I)
    {

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
        $I->sendGet('/products/1');
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
        $I->sendGet('/products/11');
        $I->canSeeResponseCodeIs(Response::HTTP_NOT_FOUND);
    }
}
