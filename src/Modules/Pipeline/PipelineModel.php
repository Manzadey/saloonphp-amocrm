<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Pipeline;

use Manzadey\SaloonAmoCrm\Collections\ModelCollection;
use Manzadey\SaloonAmoCrm\Modules\Model;
use Manzadey\SaloonAmoCrm\Modules\Pipeline\Status\PipelineStatusModel;

class PipelineModel extends Model
{
    public function id(): ?int
    {
        return $this->get('id');
    }

    public function name(): ?string
    {
        return $this->get('name');
    }

    public function sort(): ?int
    {
        return $this->get('sort');
    }

    public function isMain(): ?bool
    {
        return $this->get('is_main');
    }

    public function isUnsortedOn(): ?bool
    {
        return $this->get('is_unsorted_on');
    }

    public function isArchive(): ?bool
    {
        return $this->get('is_archive');
    }

    public function accountId(): ?int
    {
        return $this->get('account_id');
    }

    /**
     * @return list<PipelineStatusModel>
     */
    public function statuses(): array
    {
        return ModelCollection::of(PipelineStatusModel::class, $this->get('_embedded.statuses'))->all();
    }
}
