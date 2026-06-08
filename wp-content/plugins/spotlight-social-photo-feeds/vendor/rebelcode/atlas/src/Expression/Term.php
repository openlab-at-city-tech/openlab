<?php

namespace RebelCode\Atlas\Expression;

use InvalidArgumentException;

/** @psalm-immutable */
class Term extends BaseExpr
{
    public const NUMBER = 0;
    public const STRING = 1;
    public const BOOLEAN = 2;
    public const COLUMN = 3;
    public const LIST = 4;
    public const NULL = 5;

    /** @var mixed */
    protected $value;

    /**
     * @var int
     * @psalm-var Term::*
     */
    protected $type;

    /** @var bool */
    protected $distinct;

    /**
     * Constructor.
     *
     * @param int $type The term's type. See the constants in this class.
     * @param mixed $value The value.
     * @param bool $distinct Whether the column is distinct or not. Only for terms of type {@link Term::COLUMN}.
     *
     * @psalm-param Term::* $type
     */
    public function __construct(int $type, $value, bool $distinct = false)
    {
        $this->value = $value;
        $this->type = $type;
        $this->distinct = $distinct;
    }

    /** @psalm-return Term::* */
    public function getType(): int
    {
        return $this->type;
    }

    /** @return mixed */
    public function getValue()
    {
        return $this->value;
    }

    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    public function distinct(bool $distinct = true): self
    {
        return $distinct === $this->distinct
            ? $this
            : new self($this->type, $this->value, $distinct);
    }

    /** @psalm-suppress PossiblyInvalidCast */
    public function toString(): string
    {
        switch ($this->type) {
            case self::NULL:
                return 'NULL';
            case self::NUMBER:
                return (string) $this->value;
            case self::STRING:
                return "'$this->value'";
            case self::BOOLEAN:
                return $this->value ? 'TRUE' : 'FALSE';
            case self::COLUMN:
                $colParts = [];
                foreach ((array) $this->value as $part) {
                    /** @var string $part */
                    $colParts = array_merge($colParts, explode('.', $part));
                }

                $col = implode('`.`', $colParts);
                return "`$col`";
            case self::LIST:
                /** @psalm-var ExprInterface[] $elements */
                $elements = $this->value;

                $elementsStr = array_map(function (ExprInterface $element) {
                    return $element->toString();
                }, $elements);

                return '(' . implode(', ', $elementsStr) . ')';
            default:
                throw new InvalidArgumentException("Term has unknown type: \"$this->type\"");
        }
    }

    /**
     * Creates a term. This is the preferred way to create terms.
     *
     * Note that terms of type {@link Term::COLUMN} cannot be created with this method, since all strings are
     * interpreted as terms of type {@link Term::STRING}.
     *
     * @psalm-mutation-free
     *
     * @param mixed $value
     * @return ExprInterface
     */
    public static function create($value): ExprInterface
    {
        if ($value instanceof ExprInterface) {
            return $value;
        }

        $type = self::detectType($value);

        if ($type === self::LIST) {
            foreach ($value as $i => $elem) {
                if (!$elem instanceof self) {
                    $value[$i] = self::create($elem);
                }
            }
        }

        return new self($type, $value);
    }

    /**
     * Creates a term of type {@link Term::COLUMN}.
     *
     * @psalm-mutation-free
     *
     * @param string|string[] $name The column name or an array of column qualifier segments.
     * @return self
     */
    public static function column($name): self
    {
        return new self(self::COLUMN, $name);
    }

    /**
     * @psalm-mutation-free
     * @psalm-return Term::*
     */
    public static function detectType($value): int
    {
        $type = gettype($value);

        switch ($type) {
            case 'integer':
            case 'double':
                return self::NUMBER;
            case 'string':
                return self::STRING;
            case 'boolean':
                return self::BOOLEAN;
            case "array":
                return self::LIST;
            case "NULL":
                return self::NULL;
            default:
                throw new InvalidArgumentException('Unsupported type for term value: ' . gettype($value));
        }
    }
}
