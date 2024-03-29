<?php

declare(strict_types=1);

namespace App\Tests\unit\Entity;

use App\Entity\Product;
use App\Tests\UnitTester;
use Codeception\Module\Doctrine2;
use Codeception\Test\Unit;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

class ProductTest extends Unit
{
    public UnitTester $tester;
    private EntityManagerInterface $em;

    public function _before(): void
    {
        /** @var Doctrine2 $module */
        $module = $this->getModule('Doctrine2');
        $this->em = $module->em;
        $this->tester->haveInRepository(
            Product::class,
            ['name' => 'Adidas Schuhe', 'price' => 199.99]
        );
        $this->tester->haveInRepository(
            Product::class,
            ['name' => 'Nike Schuhe', 'price' => 299.99]
        );
        parent::_before(); // TODO: Change the autogenerated stub
    }

    // tests
    public function testCeateProduct(): void
    {
        $product = new Product();
        $product->setName('Apple AirPods');
        $product->setPrice(249.99);

        $this->em->persist($product);
        $this->em->flush();

        $this->assertNotEmpty($product->getId());
        $this->tester->assertEquals(
            'Apple AirPods',
            $product->getName()
        );
        $this->tester->seeInRepository(Product::class, ['name' => 'Apple AirPods']);
    }

    public function testFetchProduct(): void
    {
        $id = $this->tester->haveInRepository(Product::class, ['name' => 'Macintosh', 'price' => 1500]);
        $product = $this->em->find(Product::class, $id);
        $this->tester->assertEquals($id, $product->getId());
    }

    public function testUpdateProduct(): void
    {
        $id = $this->tester->haveInRepository(Product::class, ['name' => 'Macintosh', 'price' => 1500]);
        $product = $this->em->find(Product::class, $id);
        $this->tester->assertEquals($id, $product->getId());
        $product->setName('Apple Macintosh');
        $this->em->persist($product);
        $this->em->flush();
        $this->tester->assertEquals('Apple Macintosh', $product->getName());
        $this->tester->seeInRepository(Product::class, ['name' => 'Apple Macintosh']);
        $this->tester->dontSeeInRepository(Product::class, ['name' => 'Macintosh']);
    }

    public function testDeleteProduct(): void
    {
        $id = $this->tester->haveInRepository(Product::class, ['name' => 'Macintosh', 'price' => 1500]);
        $product = $this->em->find(Product::class, $id);
        $this->tester->assertEquals($id, $product->getId());
        $this->em->remove($product);
        $this->em->flush();
        $this->tester->dontSeeInRepository(Product::class, ['id' => $id, 'name' => 'Macintosh']);
    }

    public function testInsertProductWillFailIfNameExists(): void
    {
        $this->tester->haveInRepository(Product::class, ['name' => 'Macintosh', 'price' => 1500]);
        $this->em->persist((new Product())->setName('Macintosh')->setPrice(1500));
        $this->tester->expectThrowable(
            UniqueConstraintViolationException::class,
            fn () => $this->em->flush()
        );
    }
}
