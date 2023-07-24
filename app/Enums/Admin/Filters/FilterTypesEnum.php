<?php declare(strict_types=1);

namespace App\Enums\Admin\Filters;

use BenSampo\Enum\Enum;

/**
 * Class FilterTypesEnum
 * @package App\Enums\Admin\Filters
 *
 * Тип Фильтров проекта
 * В проекте всего 4 типа
 *
 */
final class FilterTypesEnum extends Enum
{
    /**
     * Тип 1 (по умолчанию) Главный фильтр
     */
    const DEFAULT = 0;

    /**
     * Тип 2 Под фильтр
     */
    const SUB_FILTER = 1;

    /**
     * Тип 3 Это фильтр со значениями истинно или лож
     */
    const BOOL = 2;

    /**
     * Тип 4 Это фильтр со значениями
     */
    const WITH_VALUES = 3;
}
