<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Pipeline;

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
     * @return array<PipelineStatusModel>
     */
    public function statuses(): array
    {
        return array_map(
            static fn (array $status): PipelineStatusModel => new PipelineStatusModel($status),
            $this->get('_embedded.statuses', [])
        );
    }
}
