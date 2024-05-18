<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\checkout;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CheckoutController extends Controller
{
    public function index()
    {
        // Ambil data keranjang
        $cartItems = session('cart', []);
        $totalPrice = 0;

        // Hitung total harga
        foreach ($cartItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // Kirim data produk dan total harga ke tampilan checkout
        return view('cart.checkout', [
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function checkoutProcess(Request $request)
    {
        // Ambil data keranjang
        $cartItems = session('cart', []);
        $totalPrice = 0;
        
        // Hitung total harga dari keranjang
        foreach ($cartItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }
        
        // Simpan data checkout ke dalam database
        checkout::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'phone' => $request->phone,
            'payment_method' => $request->payment_method,
            'total_price' => $totalPrice,
        ]);
        
        // Kosongkan data cart setelah checkout
        Cart::truncate(); 
        
        // Kosongkan session keranjang belanja setelah checkout
        session()->forget('cart');
        
        // Arahkan pengguna langsung ke halaman paymentSuccess
        return redirect()->route('paymentSuccess');
    }
}