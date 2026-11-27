<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Soap\Security;

use DateTimeImmutable;
use KboPublicSearch\Core\Xml\Node;

/** @internal WS-Security Timestamp; KBO validates it against its own clock. */
final readonly class Timestamp
{
    private function __construct(
        private DateTimeImmutable $created,
        private DateTimeImmutable $expires,
    ) {}

    public static function create(DateTimeImmutable $created, int $ttlSeconds = 300): self
    {
        return new self($created, $created->modify("+{$ttlSeconds} seconds"));
    }

    public function toNode(): Node
    {
        return Node::element('wsu:Timestamp', [
            Node::element('wsu:Created', UsernameToken::format($this->created)),
            Node::element('wsu:Expires', UsernameToken::format($this->expires)),
        ]);
    }
}
