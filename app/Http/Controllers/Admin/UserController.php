<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use Gate;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all(); //eloquent orm
        return view('admin.users.index')->with('users',$users);
    }
    public function edit(User $user)
    {
        if(Gate::denies('edit-users'))
        {
            return redirect(route('admin.users.index'));
        }
        // dd($user);//die to dump method//grab the user info from db.
        $roles = Role::all(); //that also grab the roll from db.

        //Now
        return view('admin.users.edit')->with([
            'user' => $user,
            'roles' =>$roles
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
      $user->roles()->sync($request->roles);
      
     
      $user->name = $request->name;
      $user->email = $request->email;
      
      if($user->save())
      {
        $request->session()->flash('success',$user->name.'has been saved');
      } else
      {
        $request->session()->flash('danger','An Error Has Been Occured');
      }


      return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if(Gate::denies('delete-users'))
        {
            return redirect(route(admin.users.index));
        }
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index');
    }
}
