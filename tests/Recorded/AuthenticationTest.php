<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class AuthenticationTest extends RecordedTestCase
{
    #[Test]
    public function wrong_password(): void
    {
        $this->markTestIncomplete('needs enterprises->find (Task 8)');
    }
}
