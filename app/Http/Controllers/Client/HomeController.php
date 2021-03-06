<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    // public $products;

    public function __construct()
    {
        // $this->middleware('client');
    }

    //home
    public function home()
    {
        return view('client.home.home');
    }

    //shop
    public function shop(){
        return view('client.back.shop');
    }

    //payment
    public function payment(){
        return view('client.back.payment');
    }

    //about
    public function about(){
        return view('client.back.about');
    }

    //contact
    public function contact(){
        return view('client.back.contact');
    }

    //checkout
    public function checkout(){
        $products = Cart::getContent();
        $total = Cart::getTotal();

        if(count($products) == 0) {
            return redirect('/home');
        }

        return view('client.back.checkout', compact('products', 'total'));
    }

    //product_detail
    public function product_detail($id){

        $product = Product::findOrFail($id);
        $type = $product->type;
        $images = $product->images;

        $productsRelated = Product::query()
                            ->where('type', '=', $type)
                            ->take(8)
                            ->get();
        
        $totalReviews = count($product->evaluates);

        return view('client.back.product_detail', compact(
            'product',
            'productsRelated',
            'images',
            'totalReviews'
        ));
    }

    public function login() {
        if(Auth::user()){
            return redirect('/home');
        }
        return view('client.back.login');
    }

    public function submitLogin(Request $request) {
        $request->validate([
            'email' => 'required|min:2',
            'password' => 'required|min:8'
        ],
        [
            'email.required' => 'B???n ch??a nh???p email',
            'email.min' => 'Email ??t nh???t 2 k?? t???',
            'password.required' => 'B???n ch??a nh???p m???t kh???u',
            'password.min' => 'M???t kh???u ??t nh???t 8 k?? t???'
        ]);

        $isLogin = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if($isLogin){
            return redirect('/home/profile');
        }else{
            return redirect()->back()->withErrors([
                'T??i kho???n ho???c m???t kh???u kh??ng ????ng'
            ]);
        }
    }

    public function register() {
        if(Auth::user()){
            return redirect('/home');
        }
        return view('client.back.register');
    }


    public function submitRegister(Request $request) {
        $request->validate([
            'email' => 'required|min:2',
            'fullname' => 'required|min:2|max:255',
            'password' => 'required|confirmed|min:8|max:255',
            'address' => 'required|min:8|max:255',
            'phone' => 'required|min:10'
        ],
        [
            'email.required' => 'B???n ch??a nh???p email',
            'email.min' => 'Email ??t nh???t 2 k?? t???',

            'password.required' => 'B???n ch??a nh???p m???t kh???u',
            'password.min' => 'M???t kh???u ??t nh???t 8 k?? t???',
            'password.max' => 'M???t kh???u nhi???u nh???t 255 k?? t???',
            'password.confirmed' => 'M???t kh???u kh??ng ch??nh x??c',

            'fullname.required' => 'B???n ch??a nh???p h??? t??n',
            'fullname.min' => 'H??? t??n ??t nh???t 2 k?? t???',
            'fullname.max' => 'H??? t??n nhi???u n???t 255 k?? t???',

            'address.required' => 'B???n ch??a nh???p ?????a ch???',
            'address.min' => '?????a ch??? ??t nh???t 8 k?? t???',
            'address.max' => '?????a ch??? nhi???u n???t 255 k?? t???',

            'phone.required' => 'B???n ch??a nh???p s??? ??i???n tho???i',
            'phone.min' => '??i???n tho???i ??t nh???t 10 k?? t???',
        ]);

        $registeredUser = User::query()
                                ->where('email', $request->email)
                                ->orWhere('phone' , $request->phone)
                                ->get();

        if(count($registeredUser) == 0){
            // dd($request->fullname);
            User::create([
                'fullname' => $request->fullname,
                'email' => $request->email,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'role' => 3,
                'phone' => $request->phone,
                'gender' => $request->gender
            ]);
            return redirect('/home/login')->with('success', '????ng k?? th??nh c??ng');
        }else{
            return redirect()->back()->withErrors([
                'Email ho???c s??? ??i???n tho???i ???? t???n t???i'
            ]);
        }
    }

    public function logout() {
        if(Auth::check()){
            Auth::logout();

            return redirect('/home');
        }
    }

    public function profile() {
        if(!Auth::check()){
            return redirect('/home/login');
        }

        return view('client.back.profile');
    }

    public function trackOrder($id) {
        $order = Order::findOrFail($id);
        $order_details = OrderDetail::query()->where('order_id', $order->id)->get();
        $created_order = Carbon::parse($order->created_at)->format('d/m/Y');
        $warranty_order = Carbon::parse($order->created_at)->addMonths(1)->format('d/m/Y');

        return view('client.back.track_order', compact('order', 'order_details', 'created_order', 'warranty_order'));
    }
}
