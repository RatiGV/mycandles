<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Http\Request;
use Session;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = Session::get('admin')->role;

        if ($role == 1) {
            return $next($request);
        }

        $action = $request->route()->getActionMethod();

        if ($role == 2) {
            $available_actions = json_decode(DB::table('configurations')->first()->standard_admin_actions);
        } elseif ($role == 3) {
            $available_actions = json_decode(DB::table('configurations')->first()->moderator_admin_actions);
        }

        array_push($available_actions, 'index');

        if (in_array('edit', $available_actions)) {
            array_push($available_actions, 'update');
        }

        if (! in_array($action, $available_actions)) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 5,
                    'type' => 'error',
                    'text' => trans('admin.no_permission'),
                ]);
            }

            $request->session()->flash('no_permission', true);

            return redirect()->back();
        }

        return $next($request);
    }
}
