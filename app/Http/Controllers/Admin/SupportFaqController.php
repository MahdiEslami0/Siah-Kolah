<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportFaq;
use Illuminate\Http\Request;

class SupportFaqController extends Controller
{


    public function index()
    {
        $faqs = SupportFaq::orderBy('order')->paginate(10);
        $data = [
            'pageTitle' => 'سوالات متداول',
            'faqs' => $faqs
        ];
        return view('admin.supports.faq.list', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'ایجاد سوال',
        ];
        return view('admin.supports.faq.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'order' => 'required',
        ]);
        SupportFaq::create([
            'title' => $request->title,
            'order' => $request->order,
            'description' => $request->description
        ]);
        return redirect()->to(url('admin/supports/faq'));
    }

    public function edit($id)
    {
        $faq = SupportFaq::where('id', $id)->first();
        $data = [
            'pageTitle' => 'ایجاد سوال',
            'faq' => $faq
        ];
        return view('admin.supports.faq.create', $data);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'order' => 'required',
        ]);
        SupportFaq::where('id', $id)->update([
            'title' => $request->title,
            'order' => $request->order,
            'description' => $request->description
        ]);
        return redirect()->to(url('admin/supports/faq'));
    }
}