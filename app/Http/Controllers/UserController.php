<?php

namespace App\Http\Controllers;

use App\Events\UserUpdated;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Notifications\User\UserUpdated as UserUserUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, User $user)
    {
        if ($request->user()->cannot('update', $user)) {
            return redirect()->back()->withErrors([
                'auth' => 'Not allowed to update user'
            ]);
        }

        $user->fill($request->validated());

        // Update user password if it exists
        if ($request->exists('password'))
            $user->password = Hash::make($request->input('password'));

        $user->save();

        Notification::send(User::all(), new UserUserUpdated($user));

        return Inertia::render('User/Settings');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }


    public function settings(User $user)
    {
        return Inertia::render('User/Settings');
    }
}
