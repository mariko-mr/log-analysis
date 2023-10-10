<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/../lib/sql.php');

final class SqlTest extends TestCase
{
    public function testDbConnect(): void
    {
        $dbh = dbConnect();

        // PDOオブジェクトが返されたことを確認
        $this->assertInstanceOf(PDO::class, $dbh);
    }
}
