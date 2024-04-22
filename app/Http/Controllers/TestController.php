<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
  
    public function __construct()
    {
        # By default we are using here auth:api middleware 
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function create(Request $request){

        // dd($request['payment']);
       
        // $requestData = json_decode($request, true);
        // dd($request['payment']['name']);

        $user = User::where('token',$request['token'])->first();
        // dd($user);
        
        $randomString = uniqid();
        $md5Hast = md5(uniqid($randomString, true));
        $payment = Payment::create([
            'name' => $request['payment']['name'] ?? null,
            'description' => $request['payment']['description'] ?? null,
            'company_id' =>  $user->id,
            'amount' => 0,
            'token' => $md5Hast,
            'exipered_date' =>  $request['payment']['exipered_date'] ?? null,
            'status' => (isset($request['payment']['status']) ? true : false)
        ]);
   
        $amount = 0;
        foreach ($request['payment']['products'] as $product) {
            $total_row = intval($product['quantity']) * floatval($product['unit_price']);
            $amount += $total_row;

            $new_product = new Product([
                'description_product' => $product['description_product'] ?? null,
                'quantity' => intval($product['quantity']),
                'unit_price' => floatval($product['unit_price']),
            ]);

            // Associa il nuovo prodotto al pagamento
            $payment->products()->save($new_product);
        }
        $payment->amount = $amount;
        $payment->save();
        $baseURL = 'http://192.168.1.21:8000/customer/pay/';
        $response = [
            'status' => true,
            'message'=> 'pagamento creato con successo',
            'data' => $payment,
            'results' => $baseURL . $payment->token
        ];
            // Converti l'array in JSON senza escape degli slash nell'URL
        $jsonResponse = json_encode($response, JSON_UNESCAPED_SLASHES);

        // Restituisci la risposta JSON
        return $jsonResponse;
    } 

}
