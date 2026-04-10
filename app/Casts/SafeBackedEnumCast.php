<?php

namespace App\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Cast vers un backed enum sans lever d'exception si la valeur SQL est inconnue
 * (données legacy ou imports) : on retombe sur un cas par défaut.
 */
class SafeBackedEnumCast implements CastsAttributes
{
    public function __construct(
        protected string $enumClass,
        protected ?string $fallbackValue = null,
    ) {
        if (! enum_exists($this->enumClass)) {
            throw new InvalidArgumentException("Classe d'enum invalide : {$this->enumClass}");
        }

        if (! is_subclass_of($this->enumClass, BackedEnum::class)) {
            throw new InvalidArgumentException("{$this->enumClass} doit être un backed enum.");
        }
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?BackedEnum
    {
        if ($value === null || $value === '') {
            return null;
        }

        /** @var class-string<BackedEnum> $class */
        $class = $this->enumClass;
        $case = $class::tryFrom((string) $value);

        if ($case !== null) {
            return $case;
        }

        if ($this->fallbackValue !== null) {
            $fallback = $class::tryFrom($this->fallbackValue);
            if ($fallback !== null) {
                return $fallback;
            }
        }

        $cases = $class::cases();

        return $cases[0] ?? null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return [$key => null];
        }

        /** @var class-string<BackedEnum> $class */
        $class = $this->enumClass;

        if ($value instanceof BackedEnum) {
            return [$key => $value->value];
        }

        $case = $class::tryFrom((string) $value);
        if ($case !== null) {
            return [$key => $case->value];
        }

        if ($this->fallbackValue !== null) {
            $fallback = $class::tryFrom($this->fallbackValue);
            if ($fallback !== null) {
                return [$key => $fallback->value];
            }
        }

        $cases = $class::cases();
        $first = $cases[0] ?? null;

        return [$key => $first?->value];
    }
}
