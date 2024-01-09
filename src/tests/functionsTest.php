<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class functionsTest extends TestCase
{
    public function testConnectToDatabase(): void
    {
        $this->assertIsObject(connectToDatabase());
    }

    public function testGetBooks(): void
    {
        $this->assertIsArray(getBooks());
    }
}
