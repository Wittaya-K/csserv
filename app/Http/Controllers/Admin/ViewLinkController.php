<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManageLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ViewLinkController extends Controller
{

    public function index(Request $request)
    {
        abort_unless(Gate::allows('view_link_access'), 403);

        $data = ManageLink::orderBy('department_name')->get()
                    ->groupBy('department_name');

        return view('admin.view_links.index', compact('data'));
    }
}