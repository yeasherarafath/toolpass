<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function test_initials_are_derived_from_name(): void
    {
        $this->assertSame('JD', initials('Jane Doe'));
        $this->assertSame('A', initials('Alice'));
    }

    public function test_mask_keeps_prefix_and_masks_rest(): void
    {
        $this->assertSame('sec******', mask('secret123', 3));
        $this->assertSame('***', mask('abc', 3));
    }

    public function test_slugify_produces_slug(): void
    {
        $this->assertSame('hello-world', slugify('Hello World!'));
    }

    public function test_random_color_is_hex(): void
    {
        $this->assertMatchesRegularExpression('/^#[0-9A-F]{6}$/', random_color());
    }
}
