<?php declare(strict_types=1);

namespace KboPublicSearch\Establishment\SearchByPhoneme\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Core\Xml\Node;

/** @internal */
final class ReadEstablishmentByPhonemeRequest extends Request
{
    public function __construct(
        private readonly Phonemes $phonemes,
        private readonly ?Zipcode $zipcode,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEstablishmentByPhoneme';
    }

    public function message(): array
    {
        return [
            ...array_map(static fn (string $phoneme) => Node::element('mes:Phoneme', $phoneme), $this->phonemes->toArray()),
            ...($this->zipcode ? [Node::element('mes:Zipcode', $this->zipcode->toString())] : []),
            Node::element('mes:ActiveFilter', $this->onlyActive ? 'true' : 'false'),
        ];
    }
}
