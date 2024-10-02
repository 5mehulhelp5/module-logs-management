<?php

declare(strict_types=1);

namespace NetBytes\LogsExplorer\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class LinesNumber extends Value
{
    /**
     * Validate lines number
     *
     * @return void
     * @throws LocalizedException
     */
    public function beforeSave(): void
    {
        $value = $this->getValue();

        if ($value <= 0) {
            $field = $this->getFieldConfig();
            $label = $field && is_array($field) ? $field['label'] : 'value';
            $msg = __('Invalid %1. The value must be greater than 0.', $label);
            throw new LocalizedException($msg);
        }
    }
}
