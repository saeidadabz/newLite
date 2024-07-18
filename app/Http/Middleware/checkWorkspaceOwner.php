<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkWorkspaceOwner
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        //        dd($request->user()->currentAccessToken()->update([]));
        $workspace = $user->workspaces()->find($request->workspace);
        if ($workspace === null) {
            return error('You have no access to this workspace');
        }
        $role = Role::find($workspace->pivot->role_id);
        if ($role && $role->title === 'owner') {
            $workspace->update($request->all());
        }

        return $next($request);
    }
}
