<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\sale_link;
use App\Models\Webinar;
use Illuminate\Http\Request;

class SaleLinkController extends Controller
{

    public function index()
    {
        $user_id = auth()->user()->id;
        $sale_link = sale_link::where('seller_id', $user_id)->paginate(10);
        $data = [
            'pageTitle' => 'لینک فروش',
            'sale_link' => $sale_link
        ];
        return view('admin.sale_link.list', $data);
    }

    public function create()
    {
        $webinars =  Webinar::get();
        $data = [
            'pageTitle' => 'ایجاد لینک فروش',
            'webinars' => $webinars,
            'action' => ''
        ];
        return view('admin.sale_link.create', $data);
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
            'products' => 'required',
        ]);
        sale_link::create([
            'name' => $request->name,
            'status' => $request->status,
            'products' => json_encode($request->products),
            'price' => $request->price,
            'seller_id' => auth()->user()->id
        ]);

        return redirect(getAdminPanelUrl() . '/link/list');
    }


    public function edit($id)
    {
        $sale_link = sale_link::where('id', $id)->first();
        $webinars =  Webinar::get();
        $data = [
            'pageTitle' => 'ایجاد لینک فروش',
            'webinars' => $webinars,
            'sale_link' =>  $sale_link,
            'action' => 'update/' . $sale_link->id
        ];
        return view('admin.sale_link.create', $data);
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
            'products' => 'required',
        ]);
        sale_link::where('id', $id)->update([
            'name' => $request->name,
            'status' => $request->status,
            'products' => json_encode($request->products),
            'price' => $request->price
        ]);
        return redirect(getAdminPanelUrl() . '/link/list');
    }

    public function delete($id)
    {
        sale_link::where('id', $id)->delete();
        return redirect(getAdminPanelUrl() . '/link/list');
    }
}
