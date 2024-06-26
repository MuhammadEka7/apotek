<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Exports\OrderExport;
use Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('user')->simplePaginate(5);
        return view('order.kasir.index', compact('orders'));
    }

    public function data()
    {
        $orders = Order::with('user')->simplePaginate(5);
        return view('order.admin.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $medicines = Medicine::all();
        return view('order.kasir.create', compact('medicines'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_customer' => 'required',
            'medicines' => 'required',
        ]);

        // hasilnya berbentuk : "itemnya" => "jumlah yang sama"
        // menentukan qty
        $medicines = array_count_values($request->medicines);

        // penampung detail berbentuk array2 assoc dari obat2 yang dipilih
        $dataMedicines = [];
        foreach ($medicines as $key => $value) {
            $medicine = Medicine::where('id', $key)->first();
            $arrayAssoc = [
                "id" => $key,
                "name_medicine" => $medicine['name'],
                "price" => $medicine['price'],
                "qty" => $value,
                // (int) => memastikan dan mengubah tipe data menjadi integer
                "price_after_qty" => $value * $medicine['price'],
            ];
            // format assoc dimasukkan ke array penampung sebelumnya
            array_push($dataMedicines, $arrayAssoc);
        }
        // var total price awalnya 0
        $totalPrice = 0;
        // loop dari array penampung yang udah di format
        foreach ($dataMedicines as $formatArray) {
            // dia bakal menjumlahkan totalPrice sblmnya ditambah data harga dari price_after_qty
            $totalPrice += (int)$formatArray['price_after_qty'];
        }

        $prosesTambahData = Order::create([
            'name_customer' => $request->name_customer,
            'medicines' => $dataMedicines,
            'total_price' => $totalPrice,
            // user_id menyimpan data dari id dari orang yang ingin login (kasir penanggung jawab)
            'user_id' => Auth::user()->id,
        ]);
        // redirect ke halaman struk
        return redirect()->route('order.struk', $prosesTambahData['id']);
    }

    public function strukPembelian($id)
    {
        $order = Order::where('id', $id)->first();

        return view('order.kasir.struk', compact('order'));
    }

    public function downloadPDF($id)
    {
        $order = Order::where('id', $id)->first()->toArray();

        view()->share('order', $order);

        $pdf = PDF::loadView('order.kasir.download', $order);

        return $pdf->download('Bukti Pembelian.pdf');
    }

    public function downloadExcel()
    {
        $file_name = 'Data Seluruh Pembelian.xlsx';

        return Excel::download(new OrderExport, $file_name);
    }

    public function search(Request $request)
    {
        $searchDate = $request->input('search');
        $orders = Order::whereDate('created_at', $searchDate)->simplePaginate(5);

        return view('order.kasir.index', compact('orders'));
    }

    public function adminSearch(Request $request)
    {
        $cariData = $request->input('search');
        $orders = Order::whereDate('created_at', $cariData)->simplePaginate(5);
        return view('order.admin.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
