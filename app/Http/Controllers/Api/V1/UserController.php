<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Models\User;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Policies\V1\UserPolicy;

class UserController extends ApiController
{
    protected $policyClass = UserPolicy::class;
    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(
            User::filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // policy
        if ($this->isAble('store', User::class)) {
            return new UserResource(User::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // policy
        if ($this->isAble('update', $user)) {
            $user->update($request->mappedAttributes());

            // return a pretty JSON with the data 
            return new UserResource($user);
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }

    public function replace(ReplaceUserRequest $request, User $user)
    {
        // policy
        if ($this->isAble('replace', $user)) {
            $user->update($request->mappedAttributes());

            // return a pretty JSON with the data 
            return new UserResource($user);
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // policy
        if ($this->isAble('delete', $user)) {
            $user->delete();
            return response()->json(null, 204);
            // return $this->ok('User successfully deleted');
        }
            
        return $this->notAuthorized('You are not authorized to update that resource');
    }
}
