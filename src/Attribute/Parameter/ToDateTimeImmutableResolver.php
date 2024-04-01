<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Attribute\Parameter;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\Result;

/**
 * @psalm-import-type IntlDateFormatterFormat from ToDateTimeImmutable
 */
final class ToDateTimeImmutableResolver implements ParameterAttributeResolverInterface
{
    /**
     * @psalm-param IntlDateFormatterFormat $dateType
     * @psalm-param IntlDateFormatterFormat $timeType
     * @psalm-param non-empty-string|null $timeZone
     */
    public function __construct(
        private readonly ?string $format = null,
        private readonly int $dateType = IntlDateFormatter::SHORT,
        private readonly int $timeType = IntlDateFormatter::SHORT,
        private readonly ?string $timeZone = null,
        private readonly ?string $locale = null,
    ) {
    }

    public function getParameterValue(
        ParameterAttributeInterface $attribute,
        ParameterAttributeResolveContext $context
    ): Result {
        if (!$attribute instanceof ToDateTimeImmutable) {
            throw new UnexpectedAttributeException(ToDateTimeImmutable::class, $attribute);
        }

        if (!$context->isResolved()) {
            return Result::fail();
        }

        $resolvedValue = $context->getResolvedValue();

        if ($resolvedValue instanceof DateTimeImmutable) {
            return Result::success($resolvedValue);
        }

        if ($resolvedValue instanceof DateTimeInterface) {
            return Result::success(DateTimeImmutable::createFromInterface($resolvedValue));
        }

        $timeZone = $attribute->timeZone ?? $this->timeZone;
        if ($timeZone !== null) {
            $timeZone = new DateTimeZone($timeZone);
        }

        if (is_int($resolvedValue)) {
            return Result::success(
                $this->makeDateTimeFromTimestamp($resolvedValue, $timeZone)
            );
        }

        if (is_string($resolvedValue) && !empty($resolvedValue)) {
            $format = $attribute->format ?? $this->format;
            return (is_string($format) && str_starts_with($format, 'php:'))
                ? $this->parseWithPhpFormat($resolvedValue, substr($format, 4), $timeZone)
                : $this->parseWithIntlFormat(
                    $resolvedValue,
                    $format,
                    $attribute->dateType ?? $this->dateType,
                    $attribute->timeType ?? $this->timeType,
                    $timeZone,
                    $attribute->locale ?? $this->locale,
                );
        }

        return Result::fail();
    }

    /**
     * @psalm-param non-empty-string $resolvedValue
     */
    private function parseWithPhpFormat(string $resolvedValue, string $format, ?DateTimeZone $timeZone): Result
    {
        $date = DateTimeImmutable::createFromFormat($format, $resolvedValue, $timeZone);
        if ($date === false) {
            return Result::fail();
        }

        $errors = DateTimeImmutable::getLastErrors();
        if ($errors !== false && !empty($errors['warning_count'])) {
            return Result::fail();
        }

        // If no time was provided in the format string set time to 0
        if (!strpbrk($format, 'aAghGHisvuU')) {
            $date = $date->setTime(0, 0);
        }

        return Result::success($date);
    }

    /**
     * @psalm-param non-empty-string $resolvedValue
     * @psalm-param IntlDateFormatterFormat $dateType
     * @psalm-param IntlDateFormatterFormat $timeType
     */
    private function parseWithIntlFormat(
        string $resolvedValue,
        ?string $format,
        int $dateType,
        int $timeType,
        ?DateTimeZone $timeZone,
        ?string $locale,
    ): Result {
        $formatter = $format === null
            ? new IntlDateFormatter($locale, $dateType, $timeType, $timeZone)
            : new IntlDateFormatter(
                $locale,
                IntlDateFormatter::NONE,
                IntlDateFormatter::NONE,
                $timeZone,
                pattern: $format
            );
        $formatter->setLenient(false);
        $timestamp = $formatter->parse($resolvedValue);
        return is_int($timestamp)
            ? Result::success($this->makeDateTimeFromTimestamp($timestamp, $timeZone))
            : Result::fail();
    }

    private function makeDateTimeFromTimestamp(int $timestamp, ?DateTimeZone $timeZone): DateTimeImmutable
    {
        return (new DateTimeImmutable(timezone: $timeZone))->setTimestamp($timestamp);
    }
}
