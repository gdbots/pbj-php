<?php
declare(strict_types=1);

namespace Gdbots\Pbj;

use Gdbots\Pbj\Enum\FieldRule;
use Gdbots\Pbj\Enum\Format;
use Gdbots\Pbj\Type\Type;

final class FieldBuilder
{
    private string $name;
    private Type $type;
    private ?FieldRule $rule = null;
    private bool $required = false;
    private ?int $minLength = null;
    private ?int $maxLength = null;
    private ?string $pattern = null;
    private ?Format $format = null;
    private ?int $min = null;
    private ?int $max = null;
    private int $precision = 10;
    private int $scale = 2;

    /** @var mixed */
    private $default;

    private bool $useTypeDefault = true;
    private ?string $className = null;
    private ?array $anyOfCuries = null;
    private ?\Closure $assertion = null;
    private bool $overridable = false;

    final private function __construct(string $name, Type $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public static function create(string $name, Type $type): self
    {
        return new static($name, $type);
    }

    public function required(): self
    {
        $this->required = true;
        return $this;
    }

    public function optional(): self
    {
        $this->required = false;
        return $this;
    }

    public function asASingleValue(): self
    {
        $this->rule = FieldRule::A_SINGLE_VALUE();
        return $this;
    }

    public function asASet(): self
    {
        $this->rule = FieldRule::A_SET();
        return $this;
    }

    public function asAList(): self
    {
        $this->rule = FieldRule::A_LIST();
        return $this;
    }

    public function asAMap(): self
    {
        $this->rule = FieldRule::A_MAP();
        return $this;
    }

    public function minLength(int $minLength): self
    {
        $this->minLength = $minLength;
        return $this;
    }

    public function maxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function pattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function format(Format $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function min(int $min): self
    {
        $this->min = $min;
        return $this;
    }

    public function max(int $max): self
    {
        $this->max = $max;
        return $this;
    }

    public function precision(int $precision): self
    {
        $this->precision = $precision;
        return $this;
    }

    public function scale(int $scale): self
    {
        $this->scale = $scale;
        return $this;
    }

    public function withDefault($default): self
    {
        $this->default = $default;
        return $this;
    }

    public function useTypeDefault(bool $useTypeDefault): self
    {
        $this->useTypeDefault = $useTypeDefault;
        return $this;
    }

    public function className(string $className): self
    {
        $this->className = $className;
        $this->anyOfCuries = null;
        return $this;
    }

    public function anyOfCuries(array $anyOfCuries): self
    {
        $this->anyOfCuries = $anyOfCuries;
        $this->className = null;
        return $this;
    }

    public function assertion(\Closure $assertion): self
    {
        $this->assertion = $assertion;
        return $this;
    }

    public function overridable(bool $overridable): self
    {
        $this->overridable = $overridable;
        return $this;
    }

    public function build(): Field
    {
        if (null === $this->rule) {
            $this->rule = FieldRule::A_SINGLE_VALUE();
        }

        return new Field(
            $this->name,
            $this->type,
            $this->rule,
            $this->required,
            $this->minLength,
            $this->maxLength,
            $this->pattern,
            $this->format,
            $this->min,
            $this->max,
            $this->precision,
            $this->scale,
            $this->default,
            $this->useTypeDefault,
            $this->className,
            $this->anyOfCuries,
            $this->assertion,
            $this->overridable
        );
    }
}
