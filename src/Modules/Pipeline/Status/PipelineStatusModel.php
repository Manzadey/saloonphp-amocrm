<?php

declare(strict_types=1);

namespace Manzadey\SaloonAmoCrm\Modules\Pipeline\Status;

use Manzadey\SaloonAmoCrm\Modules\Model;

class PipelineStatusModel extends Model
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

    public function isEditable(): ?bool
    {
        return $this->get('is_editable');
    }

    public function pipelineId(): ?int
    {
        return $this->get('pipeline_id');
    }

    public function color(): ?string
    {
        return $this->get('color');
    }

    public function type(): ?int
    {
        return $this->get('type');
    }

    public function accountId(): ?int
    {
        return $this->get('account_id');
    }
}
