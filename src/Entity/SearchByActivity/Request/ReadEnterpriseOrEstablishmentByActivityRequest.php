<?php declare(strict_types=1);

namespace KboPublicSearch\Entity\SearchByActivity\Request;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Entity\Type\ActivityFilter;
use KboPublicSearch\Entity\Type\EntityFilter;

/** @internal */
final class ReadEnterpriseOrEstablishmentByActivityRequest extends Request
{
    public function __construct(
        private readonly ActivityFilter $activities,
        private readonly LocationFilter $location,
        private readonly EntityFilter $filter,
        private readonly bool $onlyActive,
    ) {}

    public function operation(): string
    {
        return 'ReadEnterpriseOrEstablishmentByActivity';
    }

    public function message(): array
    {
        $flag = static fn (bool $value) => $value ? 'true' : 'false';

        return [
            Node::element('mes:ActivityFilter', array_map(static fn (string $code) => Node::element('mes:ActivityCode', $code), $this->activities->toArray())),
            $this->location->toNode(),
            Node::element('mes:EnterprisePhysicalPersonFilter', $flag($this->filter->enterprisePhysicalPerson)),
            Node::element('mes:EnterpriseLegalPersonFilter', $flag($this->filter->enterpriseLegalPerson)),
            Node::element('mes:EstablishmentFilter', $flag($this->filter->establishment)),
            Node::element('mes:ActiveFilter', $flag($this->onlyActive)),
        ];
    }
}
