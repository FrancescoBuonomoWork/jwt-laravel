<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function getPayment($token){
     
        $payment = Payment::where('token',$token)->with('products')->first();
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Pagamento non trovato'], 404);
        }
        
        return response()->json(['status' => true, 'data' => $payment]);
    }
    public function filterPayments(Request $request)
    {
        // Recupera l'utente autenticato
      
        // Verifica se l'utente ha inviato il token
        if (!$request->has('token')) {
            return response()->json(['status' => false, 'message' => 'Devi fornire il token dell\'utente.'], 400);
        }
        $user = User::where('token',$request->input('token'))->first();
        // dd($user);
        // Filtra i pagamenti associati all'utente autenticato
        $payments = Payment::where('company_id', $user->id)->with('products');
        if(!$payments){
            return response()->json(['status' => false, 'message' => 'errore']);
        }
        
        // Recupera tutti i pagamenti
    
        // Definisci i parametri di filtro consentiti
        $allowedFilters = ['status_payment','status','name','description'];
    
       // Applica i filtri alla query
        foreach ($allowedFilters as $filter) {
            if ($request->has($filter)) {
                if ($filter === 'name' || $filter === 'description') {
                    $payments->where($filter, 'like', '%' . $request->input($filter) . '%');
                } 
                 else {
                    $payments->where($filter, $request->input($filter));
                }
            }
        }
            // Aggiungi la logica per il filtro sulla data di creazione
            if ($request->has('created_at_from') || $request->has('created_at_to')) {
                $from = Carbon::parse($request->input('created_at_from','1970-01-01'))->startOfDay();
                if ($request->has('created_at_to')) {
                    $to = Carbon::parse($request->input('created_at_to'))->endOfDay();
                } else {
                    $to = Carbon::now()->endOfDay();
                }
                $payments->whereBetween('created_at', [$from, $to]);
            } 
     
            // Esegui la query per ottenere i risultati
            $filteredPayments = $payments->get();
            // dd($payments);
            // Ritorna i pagamenti filtrati
            return response()->json(['status' => true,'nums_rows' => $filteredPayments->count(), 'data' => $filteredPayments]);
        }

}
