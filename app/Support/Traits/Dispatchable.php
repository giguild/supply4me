<?php

namespace App\Support\Traits;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable as LaravelDispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

trait Dispatchable
{
    use LaravelDispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function dispatchToQueue(array $data = []): static
    {
        return static::dispatch($data);
    }

    public function dispatchSync(array $data = []): static
    {
        return static::dispatchSync($data);
    }

    public function dispatchNow(array $data = []): static
    {
        return static::dispatchSync($data);
    }
}
