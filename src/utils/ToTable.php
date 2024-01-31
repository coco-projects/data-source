<?php

    declare(strict_types = 1);

    namespace Coco\dataSource\utils;

class ToTable
{
    protected $splitCallback;
    protected ?array $header = null;
    protected ?int   $seek   = null;

    public function __construct($splitCallback, $header)
    {
        $this->splitCallback = $splitCallback;
        $this->header        = $header;
    }

    public function setSeek(?int $seek): static
    {
        $this->seek = $seek;

        return $this;
    }

    public function __invoke($data): array
    {
        $result = [];

        foreach ($data as $k => $v) {
            if ($k < $this->seek) {
                continue;
            }

            $t = call_user_func_array($this->splitCallback, [$v]);

            if (is_array($this->header) && is_array($t)) {
                $t = array_combine($this->header, $t);
            }

            $result[] = $t;
        }

        return $result;
    }
}
