<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Companies\CreateCompanyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Resources\Core\AuthResource;

class RegisterController extends Controller
{
    public function __construct(
        protected CreateCompanyAction $createCompanyAction
    ) {}

    public function __invoke(RegisterRequest $request)
    {
        $result = $this->createCompanyAction->execute($request->validated());

        return $this->created(
            AuthResource::make($result),
            'Registration successful'
        );
    }
}
