<?php

declare(strict_types=1);

namespace Yiisoft\Hydrator\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Attribute\Parameter\TrimCharacters;
use Yiisoft\Hydrator\Tests\Support\TestHelper;

use const E_USER_DEPRECATED;
use const E_USER_WARNING;

final class TrimCharactersTest extends TestCase
{
    public function testCheckDeprecatedRangesTriggersNoticeWhenCharactersContainRange(): void
    {
        $errors = TestHelper::captureErrors(static function (): void {
            TrimCharacters::checkDeprecatedRanges('a..z');
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_DEPRECATED, $errors[0][0]);
        $this->assertSame(
            'The ".." range syntax of the "characters" parameter is deprecated and will be removed in the next major version.',
            $errors[0][1],
        );
    }

    public function testCheckDeprecatedRangesTriggersNoticeForMalformedRange(): void
    {
        $errors = TestHelper::captureErrors(static function (): void {
            TrimCharacters::checkDeprecatedRanges('a..');
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_DEPRECATED, $errors[0][0]);
    }

    public function testCheckDeprecatedRangesIsSilentForNullOrDotFreeOrSingleDotCharacters(): void
    {
        $errors = TestHelper::captureErrors(static function (): void {
            TrimCharacters::checkDeprecatedRanges(null);
            TrimCharacters::checkDeprecatedRanges('abc');
            TrimCharacters::checkDeprecatedRanges('.');
            TrimCharacters::checkDeprecatedRanges('a.b');
        });

        $this->assertSame([], $errors);
    }

    public function testExpandRangesExpandsBasicRange(): void
    {
        $this->assertSame('abcdefghijklmnopqrstuvwxyz', TrimCharacters::expandRanges('a..z', null));
    }

    public function testExpandRangesExpandsSingleCharacterRange(): void
    {
        $this->assertSame('a', TrimCharacters::expandRanges('a..a', null));
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

    public function testExpandRangesExpandsMultibyteRange(): void
    {
        $this->assertSame(
            "\u{430}\u{431}\u{432}\u{433}",
            TrimCharacters::expandRanges("\u{430}..\u{433}", null),
        );
    }

    public function testExpandRangesSkipsCodePointsNotRepresentableInTargetEncoding(): void
    {
        $start = mb_chr(0x40C, 'Windows-1251');
        $end = mb_chr(0x44F, 'Windows-1251');

        $result = TrimCharacters::expandRanges($start . '..' . $end, 'Windows-1251');

        $this->assertSame(67, mb_strlen($result, 'Windows-1251'));
    }

    public function testExpandRangesTreatsCharacterWithInvalidByteSequenceLiterally(): void
    {
        $errors = TestHelper::captureErrors(static function () use (&$result): void {
            $result = TrimCharacters::expandRanges("\xFF..z", 'UTF-8');
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_WARNING, $errors[0][0]);
        $this->assertSame("Invalid '..'-range", $errors[0][1]);
        $this->assertSame("\xFF.z", $result);
    }

    public function testExpandRangesInvalidRangeNoLeftCharacter(): void
    {
        $errors = TestHelper::captureErrors(static function () use (&$result): void {
            $result = TrimCharacters::expandRanges('..z', null);
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_WARNING, $errors[0][0]);
        $this->assertSame("Invalid '..'-range, no character to the left of '..'", $errors[0][1]);
        $this->assertSame('.z', $result);
    }

    public function testExpandRangesInvalidRangeNoRightCharacter(): void
    {
        $errors = TestHelper::captureErrors(static function () use (&$result): void {
            $result = TrimCharacters::expandRanges('a..', null);
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_WARNING, $errors[0][0]);
        $this->assertSame("Invalid '..'-range, no character to the right of '..'", $errors[0][1]);
        $this->assertSame('a.', $result);
    }

    public function testExpandRangesInvalidRangeNotIncrementing(): void
    {
        $errors = TestHelper::captureErrors(static function () use (&$result): void {
            $result = TrimCharacters::expandRanges('c..a', null);
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_WARNING, $errors[0][0]);
        $this->assertSame("Invalid '..'-range, '..'-range needs to be incrementing", $errors[0][1]);
        $this->assertSame('c.a', $result);
    }

    public function testExpandRangesInvalidRangeAmbiguous(): void
    {
        $errors = TestHelper::captureErrors(static function () use (&$result): void {
            $result = TrimCharacters::expandRanges('a..b..c', null);
        });

        $this->assertCount(1, $errors);
        $this->assertSame(E_USER_WARNING, $errors[0][0]);
        $this->assertSame("Invalid '..'-range", $errors[0][1]);
        $this->assertSame('ab.c', $result);
    }
}
