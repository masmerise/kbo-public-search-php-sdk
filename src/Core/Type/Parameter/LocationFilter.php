<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Parameter;

use KboPublicSearch\Core\Type\Primitive\MunicipalityCode;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Core\Xml\Node;
use Webmozart\Assert\Assert;

/** Restricts a search to 1-4 municipalities, 1 municipality and its neighbours, or 1-4 zipcodes. */
final readonly class LocationFilter
{
    /** @param list<string> $codes */
    private function __construct(
        private string $filter,
        private string $element,
        private array $codes,
    ) {}

    public static function municipalities(string ...$codes): self
    {
        Assert::countBetween($codes, 1, 4, 'Between 1 and 4 municipality codes must be given.');

        return new self(
            'MunicipalityLocationFilter',
            'MunicipalityCode',
            array_map(static fn (string $code) => MunicipalityCode::fromString($code)->toString(), array_values($codes)),
        );
    }

    public static function neighbouringMunicipality(string $code): self
    {
        return new self('NeighboringMunicipalityLocationFilter', 'MunicipalityCode', [MunicipalityCode::fromString($code)->toString()]);
    }

    public static function zipcodes(string ...$codes): self
    {
        Assert::countBetween($codes, 1, 4, 'Between 1 and 4 zipcodes must be given.');

        return new self(
            'ZipcodeLocationFilter',
            'Zipcode',
            array_map(static fn (string $code) => Zipcode::fromString($code)->toString(), array_values($codes)),
        );
    }

    /** @internal */
    public function toNode(): Node
    {
        return Node::element('mes:LocationFilter', [
            Node::element("mes:{$this->filter}", array_map(
                fn (string $code) => Node::element("mes:{$this->element}", $code),
                $this->codes,
            )),
        ]);
    }
}
