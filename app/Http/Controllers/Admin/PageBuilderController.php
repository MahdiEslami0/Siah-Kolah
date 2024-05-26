<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageBuilder;
use Illuminate\Http\Request;

class PageBuilderController extends Controller
{
    public function list()
    {
        $page = PageBuilder::orderBy('order')->paginate('15');
        $data = [
            'pageTitle' => 'ایجاد گزینه برای صفحه',
            'page' => $page
        ];
        return view('admin.page_builder.list', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'ایجاد گزینه برای صفحه',
        ];
        return view('admin.page_builder.create', $data);
    }

    public function store(request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'type' => 'required',
            'order' => 'required',
        ]);
        PageBuilder::create([
            'title' => $request->title,
            'type' => $request->type,
            'order' => $request->order,
            'class' => $request->class,
            'description' => $request->description,
            'url' => $request->url
        ]);
        return redirect()->to(url('/admin/page-builder/list'));
    }

    public function edit($id)
    {
        $page = PageBuilder::where('id', $id)->first();
        $data = [
            'pageTitle' => 'ایجاد گزینه برای صفحه',
            'page' => $page
        ];
        return view('admin.page_builder.create', $data);
    }

    public function update(request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'type' => 'required',
            'order' => 'required',
        ]);
        PageBuilder::where('id', $id)->update([
            'title' => $request->title,
            'type' => $request->type,
            'order' => $request->order,
            'class' => $request->class,
            'description' => $request->description,
            'url' => $request->url
        ]);
        return redirect()->to(url('/admin/page-builder/list'));
    }
}
