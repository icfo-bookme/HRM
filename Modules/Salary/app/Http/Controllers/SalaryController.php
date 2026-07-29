<?php

namespace Modules\Salary\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        return view('salary::index');
    }

    public function create()
    {
        return view('salary::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('salary::show');
    }

    public function edit($id)
    {
        return view('salary::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
