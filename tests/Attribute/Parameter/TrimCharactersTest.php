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
    #[TestWith(['a..z', false])]
    #[TestWith(['a..', false])]
    #[TestWith(['a..z', true])]
    #[TestWith(["a\0.\0.\0z\0", true, 'UTF-16LE'])]
    #[TestWith(["\0a\0.\0.\0z", true, 'UTF-16BE'])]
    #[TestWith(["a\0\0\0.\0\0\0.\0\0\0z\0\0\0", true, 'UTF-32LE'])]
    #[TestWith(["\0\0\0a\0\0\0.\0\0\0.\0\0\0z", true, 'UTF-32BE'])]
    public function testCheckDeprecatedRangesTriggersNotice(
        string $characters,
        bool $multibyte,
        ?string $encoding = null,
    ): void {
        $errors = TestHelper::captureErrors(static function () use ($characters, $multibyte, $encoding): void {
            TrimCharacters::checkDeprecatedRanges($characters, $multibyte, $encoding);
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_DEPRECATED, $errors[0][0]);
        $this->assertSame(
            'The ".." range syntax of the "characters" parameter is deprecated and will be removed in the next major version.',
            $errors[0][1],
        );
    }

    #[TestWith([null, false])]
    #[TestWith(['abc', false])]
    #[TestWith(['.', false])]
    #[TestWith(['a.b', false])]
    #[TestWith([null, true])]
    #[TestWith(['a.b', true])]
    #[TestWith(["a\0.\0b\0", true, 'UTF-16LE'])]
    #[TestWith(["\x41\x2E\x2E\x41", true, 'UTF-16LE'])]
    public function testCheckDeprecatedRangesIsSilent(
        ?string $characters,
        bool $multibyte,
        ?string $encoding = null,
    ): void {
        $errors = TestHelper::captureErrors(static function () use ($characters, $multibyte, $encoding): void {
            TrimCharacters::checkDeprecatedRanges($characters, $multibyte, $encoding);
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

    #[TestWith(['UTF-16LE'])]
    #[TestWith(['UTF-16BE'])]
    #[TestWith(['UTF-32LE'])]
    #[TestWith(['UTF-32BE'])]
    public function testExpandRangesWithEncoding(string $encoding): void
    {
        $characters = mb_convert_encoding('a..z', $encoding, 'UTF-8');
        $expected = mb_convert_encoding('abcdefghijklmnopqrstuvwxyz', $encoding, 'UTF-8');

        $this->assertSame($expected, TrimCharacters::expandRanges($characters, $encoding));
    }

    #[TestWith(['UTF-7'])]
    #[TestWith(['UTF7-IMAP'])]
    #[TestWith(['JIS'])]
    #[TestWith(['ISO-2022-JP'])]
    #[TestWith(['ISO-2022-JP-MS'])]
    #[TestWith(['CP50220'])]
    #[TestWith(['CP50221'])]
    #[TestWith(['CP50222'])]
    public function testExpandRangesWithStatefulEncoding(string $encoding): void
    {
        $characters = mb_convert_encoding("x\u{430}..\u{433}y", $encoding, 'UTF-8');

        $result = TrimCharacters::expandRanges($characters, $encoding);

        $this->assertSame("x\u{430}\u{431}\u{432}\u{433}y", mb_convert_encoding($result, 'UTF-8', $encoding));
    }

    public function testExpandRangesSkipsCodePointsNotRepresentableInStatefulEncoding(): void
    {
        $characters = mb_convert_encoding("\u{410}..\u{451}", 'ISO-2022-JP', 'UTF-8');

        $result = TrimCharacters::expandRanges($characters, 'ISO-2022-JP');

        $this->assertSame(65, mb_strlen($result, 'ISO-2022-JP'));
        $this->assertStringNotContainsString('?', mb_convert_encoding($result, 'UTF-8', 'ISO-2022-JP'));
    }

    public function testExpandRangesSkipsSurrogatesInStatefulEncoding(): void
    {
        $characters = mb_convert_encoding("\u{D7FF}..\u{E000}", 'UTF-7', 'UTF-8');

        $result = TrimCharacters::expandRanges($characters, 'UTF-7');

        $this->assertSame("\u{D7FF}\u{E000}", mb_convert_encoding($result, 'UTF-8', 'UTF-7'));
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
    #[TestWith(["a\0.\0.\0", 'UTF-16LE', "Invalid '..'-range, no character to the right of '..'", "a\0.\0"])]
    #[TestWith(['+BDI-..+BDA-', 'UTF-7', "Invalid '..'-range, '..'-range needs to be incrementing", '+BDI-.+BDA-'])]
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
