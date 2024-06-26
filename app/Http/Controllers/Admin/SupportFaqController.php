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
        $faqs = SupportFaq::orderBy('order')->get();
        $data = [
            'pageTitle' => 'ایجاد سوال',
            'faqs' => $faqs
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
            'description' => $request->description,
            'parent_id' => $request->parent
        ]);
        return redirect()->to(url('admin/supports/faq'));
    }

    public function edit($id)
    {
        $faq = SupportFaq::where('id', $id)->first();
        $faqs = SupportFaq::orderBy('order')->get();
        $data = [
            'pageTitle' => 'ایجاد سوال',
            'faq' => $faq,
            'faqs' => $faqs
        ];
        return view('admin.supports.faq.create', $data);
    }


    public function delete($id)
    {
        SupportFaq::where('id', $id)->delete();
        return redirect()->to(url('admin/supports/faq'));
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
            'parent_id' => $request->parent,
            'description' => $request->description
        ]);
        return redirect()->to(url('admin/supports/faq'));
    }
}
