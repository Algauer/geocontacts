<?php

namespace Tests\Unit;

use App\Rules\ValidCpf;
use PHPUnit\Framework\TestCase;

class ValidCpfTest extends TestCase
{
    private ValidCpf $rule;
    private bool $failed;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new ValidCpf();
        $this->failed = false;
    }

    private function validate(string $value): bool
    {
        $this->failed = false;
        $this->rule->validate('cpf', $value, function () {
            $this->failed = true;
        });

        return ! $this->failed;
    }

    public function test_valid_cpf_passes(): void
    {
        $this->assertTrue($this->validate('52998224725'));
    }

    public function test_invalid_cpf_fails(): void
    {
        $this->assertFalse($this->validate('12345678901'));
    }

    public function test_all_same_digits_fails(): void
    {
        $this->assertFalse($this->validate('11111111111'));
        $this->assertFalse($this->validate('00000000000'));
        $this->assertFalse($this->validate('99999999999'));
    }

    public function test_cpf_with_mask_is_accepted(): void
    {
        $this->assertTrue($this->validate('529.982.247-25'));
    }

    public function test_cpf_with_wrong_length_fails(): void
    {
        $this->assertFalse($this->validate('1234'));
        $this->assertFalse($this->validate('123456789012'));
    }
}
