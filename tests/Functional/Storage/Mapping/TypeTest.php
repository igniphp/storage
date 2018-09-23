<?php declare(strict_types=1);

namespace Igni\Tests\Functional\Storage\Mapping;

use Igni\Storage\Mapping\Strategy\Text;
use Igni\Storage\Mapping\Type;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    public function testRegister(): void
    {
        Type::register('test', 'TestClass');

        self::assertTrue(Type::has('test'));
    }

    public function testGetDefaultType(): void
    {
        self::assertSame(Text::class, Type::get('string'));
        self::assertSame(Text::class, Type::get('text'));
    }
}
