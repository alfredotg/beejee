<?php

namespace BeeJee\Model;

class Task extends \Illuminate\Database\Eloquent\Model
{
    const STATUS_NEW = 0;
    const STATUS_COMPLETED = 1;

    public $timestamps = false;

    public function getStatusText(): string
    {
        switch (intval($this->status)) {
        case self::STATUS_NEW:
            return 'Новая';
        case self::STATUS_COMPLETED:
            return 'Выполнена';
        }
        return "";
    }

    public function isCompleted(): bool
    {
        return $this->status == self::STATUS_COMPLETED;
    }
}
