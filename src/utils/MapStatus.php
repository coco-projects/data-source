<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\utils;

class MapStatus
{
    protected int|string $id;
    protected array      $fields = [
        "label"    => "",
        "content"  => "",
        "selected" => false,
        "disabled" => false,
    ];

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function setFileds($key, $value): static
    {
        $this->fields[$key] = $value;

        return $this;
    }

    public function disabled($isDisabled = true): static
    {
        $this->setFileds('disabled', $isDisabled);

        return $this;
    }

    public function selected($isSelected = true): static
    {
        $this->setFileds('selected', $isSelected);

        return $this;
    }

    public function label($label): static
    {
        $this->setFileds('label', $label);

        return $this;
    }

    public function content($label): static
    {
        $this->setFileds('content', $label);

        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->fields['disabled'];
    }

    public function isSelected(): bool
    {
        return $this->fields['selected'];
    }

    public function getLabel(): mixed
    {
        return $this->fields['label'];
    }

    public function getContent(): mixed
    {
        return $this->fields['content'];
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getId(): int|string
    {
        return $this->id;
    }
}
