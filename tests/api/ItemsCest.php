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

    public function _before(ApiTester $tester): void
    {
        $this->adidasProduct = $tester
            ->haveInRepository(
                Product::class,
                ['name' => 'adidas', 'price' => 199.99]
            );
        $this->nikeProduct = $tester
            ->haveInRepository(
                Product::class,
                ['name' => 'nike', 'price' => 199.99]
            );
    }

    public function createItem(ApiTester $tester): void
    {
        $tester->sendPost(
            '/items',
            json_encode([
                'product' => $this->adidasProduct,
                'quantity' => 1
            ])
        );
        $tester->canSeeResponseCodeIs(Response::HTTP_CREATED);
    }

    public function removeItem(ApiTester $tester): void
    {
        $tester->sendPost(
            '/items',
            json_encode([
                'product' => $this->adidasProduct,
                'quantity' => 1
            ])
        );
        $tester->canSeeResponseCodeIs(Response::HTTP_CREATED);
        $data = json_decode($tester->grabResponse());

        $tester->sendDelete('/items/' . $data->id);
        $tester->dontSeeInRepository(CartItem::class, ['id' => $data->id]);
    }

    public function getItems(ApiTester $tester): void
    {
        $adidasProduct = $tester->grabEntityFromRepository(
            Product::class,
            [
                'id' => $this->adidasProduct,
            ]
        );
        $nikeProduct = $tester->grabEntityFromRepository(
            Product::class,
            [
                'id' => $this->nikeProduct,
            ]
        );
        $tester->haveInRepository(
            CartItem::class,
            [
                'product' => $adidasProduct,
                'quantity' => 1,
            ]
        );
        $tester->haveInRepository(
            CartItem::class,
            [
                'product' => $nikeProduct,
                'quantity' => 1,
            ]
        );

        $tester->sendGet('/items');
        $tester->canSeeResponseCodeIsSuccessful();
        $tester->canSeeResponseIsJson();
        $tester->canSeeResponseMatchesJsonType(
            [
                'id' => 'integer',
                'product' => 'array',
                'quantity' => 'integer'
            ]
        );
        $data = json_decode($tester->grabResponse());
        $tester->assertCount(2, $data);
    }

    public function getItem(ApiTester $tester): void
    {
        $tester->sendPost(
            '/items',
            json_encode([
                'product' => $this->adidasProduct,
                'quantity' => 1
            ])
        );
        $tester->canSeeResponseCodeIs(Response::HTTP_CREATED);
        $data = json_decode($tester->grabResponse());

        $tester->sendGet('/items/' . $data->id);
        $tester->canSeeResponseCodeIsSuccessful();
        $tester->canSeeResponseIsJson();
        $tester->canSeeResponseMatchesJsonType(
            [
                'id' => 'integer',
                'product' => 'array',
                'quantity' => 'integer'
            ]
        );
    }

    public function patchItemAddQuantitySubtractQuantity(ApiTester $tester): void
    {
        $content = $tester->sendPost(
            '/items',
            json_encode([
                'product' => $this->adidasProduct,
                'quantity' => 1
            ])
        );
        $data = json_decode($content);
        $oldQuantity = $data->quantity;
        $content = $tester->sendPatch('/items/' . $data->id . '/add');
        $data = json_decode($content);
        $tester->assertSame($oldQuantity + 1, $data->quantity);
        $oldQuantity = $data->quantity;
        $content = $tester->sendPatch('/items/' . $data->id . '/subtract');
        $data = json_decode($content);
        $tester->assertSame($oldQuantity - 1, $data->quantity);
    }
}
