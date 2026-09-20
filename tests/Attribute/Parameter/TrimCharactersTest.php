<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Parameter\TrimCharacters;
use Yiisoft\Hydrator\Tests\Support\TestHelper;

use const E_USER_DEPRECATED;
use const E_USER_WARNING;

final class TrimCharactersTest extends TestCase
{
    #[TestWith(['a..z'])]
    #[TestWith(['a..'])]
    public function testCheckDeprecatedRangesTriggersNotice(string $characters): void
    {
        $errors = TestHelper::captureErrors(static function () use ($characters): void {
            TrimCharacters::checkDeprecatedRanges($characters);
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_DEPRECATED, $errors[0][0]);
        $this->assertSame(
            'The ".." range syntax of the "characters" parameter is deprecated and will be removed in the next major version.',
            $errors[0][1],
        );
    }

    #[TestWith([null])]
    #[TestWith(['abc'])]
    #[TestWith(['.'])]
    #[TestWith(['a.b'])]
    public function testCheckDeprecatedRangesIsSilentForNullOrDotFreeOrSingleDotCharacters(?string $characters): void
    {
        $errors = TestHelper::captureErrors(static function () use ($characters): void {
            TrimCharacters::checkDeprecatedRanges($characters);
        });

        $this->assertSame([], $errors);
    }

    #[TestWith(['a..z', 'abcdefghijklmnopqrstuvwxyz'])]
    #[TestWith(['a..a', 'a'])]
    #[TestWith(["\u{430}..\u{433}", "\u{430}\u{431}\u{432}\u{433}"])]
    public function testExpandRangesExpandsRange(string $characters, string $expected): void
    {
        $this->assertSame($expected, TrimCharacters::expandRanges($characters, null));
    }

    public function testExpandRangesTreatsSingleDotAsOrdinaryCharacter(): void
    {
        $errors = TestHelper::captureErrors(static function () use (&$dot, &$twoChars): void {
            $dot = TrimCharacters::expandRanges('.', null);
            $twoChars = TrimCharacters::expandRanges('a.b', null);
        });

        $this->assertSame([], $errors);
        $this->assertSame('.', $dot);
        $this->assertSame('a.b', $twoChars);
    }

    public function testExpandRangesSkipsCodePointsNotRepresentableInTargetEncoding(): void
    {
        $start = mb_chr(0x40C, 'Windows-1251');
        $end = mb_chr(0x44F, 'Windows-1251');

        $result = TrimCharacters::expandRanges($start . '..' . $end, 'Windows-1251');

        $this->assertSame(67, mb_strlen($result, 'Windows-1251'));
    }

    #[TestWith(["\xFF..z", 'UTF-8', "Invalid '..'-range", "\xFF.z"])]
    #[TestWith(['..z', null, "Invalid '..'-range, no character to the left of '..'", '.z'])]
    #[TestWith(['a..', null, "Invalid '..'-range, no character to the right of '..'", 'a.'])]
    #[TestWith(['c..a', null, "Invalid '..'-range, '..'-range needs to be incrementing", 'c.a'])]
    #[TestWith(['a..b..c', null, "Invalid '..'-range", 'ab.c'])]
    public function testExpandRangesInvalidRange(
        string $characters,
        ?string $encoding,
        string $expectedWarning,
        string $expectedResult,
    ): void {
        $errors = TestHelper::captureErrors(static function () use ($characters, $encoding, &$result): void {
            $result = TrimCharacters::expandRanges($characters, $encoding);
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_WARNING, $errors[0][0]);
        $this->assertSame($expectedWarning, $errors[0][1]);
        $this->assertSame($expectedResult, $result);
    }
}
