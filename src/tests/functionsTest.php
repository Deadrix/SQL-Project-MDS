<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../src/functions/functions.php";

final class functionsTest extends TestCase
{
    public function testConnectToDatabase(): void
    {
        $this->assertIsObject(connectToDatabase());
    }

    public function testGetBooks(): void
    {
        $this->assertIsArray(getBooks());
        $this->assertIsArray(getBooks("12", "lapo", "flamma", "1"));

        $this->assertCount(20, getBooks());
        $this->assertCount(0, getBooks(titre: "loup", disponible: "0"));
        $this->assertCount(1, getBooks("12", "lapo", "flamma", "1"));
        $this->assertCount(2, getBooks(titre: "loup"));

        $this->assertStringContainsString("12", getBooks()[0]["titre"]);
    }
}
