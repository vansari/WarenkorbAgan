<?php


namespace App\Tests\unit\Entity;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;

class CartItemTest extends Unit
{
    public UnitTester $tester;
    private EntityManagerInterface $em;
    private array $testProducts = [];

    public function _before()
    {
        parent::_before(); // TODO: Change the autogenerated stub
        $this->em = $this->getModule('Doctrine2')->em;
        $this->testProducts = [];
        $this->testProducts['adidas'] = $this->tester->haveInRepository(Product::class, ['name' => 'Adidas Schuhe', 'price' => 199.99]);
        $this->testProducts['nike'] = $this->tester->haveInRepository(Product::class, ['name' => 'Nike Schuhe', 'price' => 299.99]);
        $this->testProducts['puma'] = $this->tester->haveInRepository(Product::class, ['name' => 'Puma Schuhe', 'price' => 99.99]);
        $this->testProducts['deich'] = $this->tester->haveInRepository(Product::class, ['name' => 'Deich Schuhe', 'price' => 9.99]);
    }

    // tests
    public function testCreateCartItem(): void
    {
        /** @var Product $product */
        $product = $this->em->find(Product::class, $this->testProducts['adidas']);
        $item = new CartItem();
        $item->setProduct($product);

        $this->em->persist($item);
        $this->em->flush();

        $this->assertNotEmpty($item->getId());
        $this->tester->seeInRepository(CartItem::class, ['quantity' => 1]);
    }

    public function testFetchCartItem(): void
    {
        $product = $this->em->find(Product::class, $this->testProducts['adidas']);
        $id = $this->tester->haveInRepository(CartItem::class, ['product' => $product, 'quantity' => 1]);
        $item = $this->em->find(CartItem::class, $id);
        $this->tester->assertEquals($id, $item->getId());
    }

    public function testUpdateCartItem(): void
    {
        $productAdidas = $this->em->find(Product::class, $this->testProducts['adidas']);
        $id = $this->tester->haveInRepository(CartItem::class, ['product' => $productAdidas, 'quantity' => 1]);
        /** @var CartItem $item */
        $item = $this->em->find(CartItem::class, $id);
        $this->tester->assertEquals($id, $item->getId());
        /** @var Product $product */
        $product = $this->em->find(Product::class, $this->testProducts['nike']);
        $item->setProduct($product);
        $this->em->persist($item);
        $this->em->flush();
        $this->tester->assertEquals($item->getProduct()->getId(), $product->getId());
        $this->tester->seeInRepository(CartItem::class, ['id' => $item->getId(), 'product' => $product, 'quantity' => 1]);
        $this->tester->dontSeeInRepository(CartItem::class, ['id' => $item->getId(), 'product' => $productAdidas, 'quantity' => 1]);
    }

    public function testDeleteCartItem(): void
    {
        $productAdidas = $this->em->find(Product::class, $this->testProducts['adidas']);
        $id = $this->tester->haveInRepository(CartItem::class, ['product' => $productAdidas, 'quantity' => 1]);
        /** @var CartItem $item */
        $item = $this->em->find(CartItem::class, $id);
        $this->tester->assertEquals($id, $item->getId());
        $this->em->remove($item);
        $this->em->flush();
        $this->tester->dontSeeInRepository(CartItem::class, ['id' => $id]);
    }

    public function testIncrementAndDecrementOfQuantity(): void
    {
        $cartItem = new CartItem();
        $this->assertNotNull($cartItem->getQuantity());
        $this->tester->assertSame(0, $cartItem->getQuantity());

        $cartItem->setProduct((new Product())->setName('Adidas')->setPrice(250));
        $this->tester->assertEquals(1, $cartItem->getQuantity());

        $cartItem->addOne();
        $this->tester->assertEquals(2, $cartItem->getQuantity());

        $cartItem->subtractOne();
        $this->tester->assertEquals(1, $cartItem->getQuantity());
    }

    public function testGetTotalOfProductQuantity(): void
    {
        $cartItem = new CartItem();
        $this->assertEquals(0, $cartItem->getTotal());

        $productAdidas = $this->em->find(Product::class, $this->testProducts['adidas']);
        $id = $this->tester->haveInRepository(CartItem::class, ['product' => $productAdidas, 'quantity' => 1]);
        /** @var CartItem $item */
        $item = $this->em->find(CartItem::class, $id);
        $this->assertEquals(199.99, $item->getTotal());
        $item->addOne();
        $this->assertEquals(399.98, $item->getTotal());
        $item->subtractOne();
        $this->assertEquals(199.99, $item->getTotal());
    }
}
