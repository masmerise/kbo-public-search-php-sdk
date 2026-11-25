<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\SearchByPhoneme\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Enterprise\Type\EnterpriseTypes;
use KboPublicSearch\Enterprise\Type\JuridicalFormFilter;
use KboPublicSearch\Enterprise\Type\TypeOfEnterprise;

/** @internal */
final class ReadEnterpriseByPhonemeRequest extends Request
{
    public function __construct(
        private readonly Phonemes $phonemes,
        private readonly EnterpriseTypes $types,
        private readonly ?Zipcode $zipcode,
        private readonly ?JuridicalFormFilter $juridicalForm,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEnterpriseByPhoneme';
    }

    public function message(): array
    {
        return [
            ...array_map(static fn (string $phoneme) => Node::element('mes:Phoneme', $phoneme), $this->phonemes->toArray()),
            ...($this->zipcode ? [Node::element('mes:Zipcode', $this->zipcode->toString())] : []),
            ...array_map(static fn (TypeOfEnterprise $type) => Node::element('mes:TypeOfEnterprise', $type->value), $this->types->toArray()),
            ...($this->juridicalForm ? [Node::element('mes:EnterpriseJuridicalForm', $this->juridicalForm->value)] : []),
            Node::element('mes:ActiveFilter', $this->onlyActive ? 'true' : 'false'),
        ];
    }
}
