<?php


namespace App\Tests\Api;

use App\Tests\ApiTester;

class ProductsCest
{
    public function _before(ApiTester $I)
    {

    }

    // tests
    public function getProducts(ApiTester $I)
    {
        $I->sendGet('/products');
        $I->canSeeResponseCodeIsSuccessful();
        $I->canSeeResponseIsJson();
    }
}
