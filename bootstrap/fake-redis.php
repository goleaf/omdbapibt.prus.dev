<?php

if (! class_exists('Redis')) {
    class Redis
    {
        public const OPT_MAX_RETRIES = 1;

        public const OPT_BACKOFF_ALGORITHM = 2;

        public const OPT_BACKOFF_BASE = 3;

        public const OPT_BACKOFF_CAP = 4;

        public const OPT_PREFIX = 5;

        public const OPT_READ_TIMEOUT = 6;

        public const OPT_SCAN = 7;

        public const OPT_SERIALIZER = 8;

        public const OPT_COMPRESSION = 9;

        public const OPT_COMPRESSION_LEVEL = 10;

        public const BACKOFF_ALGORITHM_DEFAULT = 0;

        public const BACKOFF_ALGORITHM_DECORRELATED_JITTER = 1;

        public const BACKOFF_ALGORITHM_EQUAL_JITTER = 2;

        public const BACKOFF_ALGORITHM_EXPONENTIAL = 3;

        public const BACKOFF_ALGORITHM_UNIFORM = 4;

        public const BACKOFF_ALGORITHM_CONSTANT = 5;

        public const SERIALIZER_NONE = 0;

        protected array $data = [];

        protected string $prefix = '';

        protected bool $inTransaction = false;

        protected array $options = [];

        public function connect(...$parameters): bool
        {
            return true;
        }

        public function pconnect(...$parameters): bool
        {
            return $this->connect(...$parameters);
        }

        public function setOption(int $option, mixed $value): bool
        {
            $this->options[$option] = $value;

            if ($option === self::OPT_PREFIX) {
                $this->prefix = (string) $value;
            }

            return true;
        }

        public function getOption(int $option): mixed
        {
            return $this->options[$option] ?? ($option === self::OPT_SERIALIZER ? self::SERIALIZER_NONE : null);
        }

        public function auth(mixed $credentials): bool
        {
            return true;
        }

        public function select(int $database): bool
        {
            return true;
        }

        public function client(string $command, mixed $value): bool
        {
            return true;
        }

        public function set(string $key, mixed $value, mixed $options = null): bool
        {
            $this->data[$this->applyPrefix($key)] = $value;

            return true;
        }

        public function setex(string $key, int $seconds, mixed $value): bool
        {
            $this->data[$this->applyPrefix($key)] = $value;

            return true;
        }

        public function get(string $key): mixed
        {
            return $this->data[$this->applyPrefix($key)] ?? false;
        }

        public function mget(array $keys): array
        {
            return array_map(fn (string $key) => $this->get($key), $keys);
        }

        public function del(string|array ...$keys): int
        {
            $deleted = 0;
            $keys = count($keys) === 1 && is_array($keys[0]) ? $keys[0] : $keys;

            foreach ($keys as $key) {
                $prefixed = $this->applyPrefix((string) $key);

                if (array_key_exists($prefixed, $this->data)) {
                    unset($this->data[$prefixed]);
                    $deleted++;
                }
            }

            return $deleted;
        }

        public function exists(string $key): bool
        {
            return array_key_exists($this->applyPrefix($key), $this->data);
        }

        public function incrby(string $key, int $value): int
        {
            $prefixed = $this->applyPrefix($key);
            $current = (int) ($this->data[$prefixed] ?? 0);
            $current += $value;
            $this->data[$prefixed] = $current;

            return $current;
        }

        public function eval(string $script, int $numberOfKeys, mixed ...$arguments): int
        {
            return 1;
        }

        public function multi(): bool
        {
            $this->inTransaction = true;

            return true;
        }

        public function exec(): bool
        {
            $this->inTransaction = false;

            return true;
        }

        public function flushdb(): bool
        {
            $this->data = [];

            return true;
        }

        public function expire(string $key, int $seconds): bool
        {
            return true;
        }

        public function _prefix(string $value): string
        {
            return $this->prefix.$value;
        }

        protected function applyPrefix(string $key): string
        {
            return $this->prefix.$key;
        }
    }
}
