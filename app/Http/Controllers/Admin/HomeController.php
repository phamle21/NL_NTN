<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\UserType;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\Promotion;
use App\Models\StaffHistory;

class HomeController extends Controller
{
    public function __construct()
    {
        @session_start();
        $this->middleware('CheckLoginAdmin');
    }

    //Logout
    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }

    // ======================================HOME=======================================================
    //home
    public function home()
    {
        return view('admin.home.home');
    }

    // =====================================PRODUCT====================================================
    //product list
    public function product()
    {
        $product_list = Product::leftJoin('product_type as b', 'product.type', 'b.id')
            ->select('product.*', 'b.name_type')
            ->get();
        $product_type = ProductType::all();

        return view('admin.back.product', compact('product_list', 'product_type'));
    }
    //product delete list
    public function product_delete_list()
    {
        $product_list = Product::onlyTrashed()
            ->leftJoin('product_type as b', 'product.type', 'b.id')
            ->select('product.*', 'b.name_type')
            ->get();
        $product_type = ProductType::all();
        return view('admin.back.product_delete', compact('product_list', 'product_type'));
    }

    //post add product
    public function post_add_product(Request $request)
    {

        $addP = DB::table('product')->insert([
            'type' => $request->product_type,
            'name' => $request->product_name,
            'description' => $request->product_des,
            'price' => $request->product_price,
            'quantity' => $request->product_quantity,
        ]);

        $lastid =  DB::getPdo('product')->lastInsertId();


        if ($addP) {

            $history = new StaffHistory;
            $history->staff_id = Auth::id();
            $history->title = "Th??m s???n ph???m m???i";
            $history->content = "??? T??n s???n ph???m: $request->product_name\n" .
                "??? Lo???i s???n ph???m: " . ProductType::whereId($request->product_type)->first()->name_type . "\n" .
                "??? Gi?? s???n ph???m: " . number_format($request->product_price) . "\n" .
                "??? S??? l?????ng s???n ph???m: $request->product_quantity\n" .
                "??? M?? t??? s???n ph???m: \n$request->product_des";
            $history->save();

            //Ki???m tra file img1
            if ($request->hasFile('product_img1')) {
                $img1 = $request->product_img1;

                $name_img1 = "img1_" . date("Y_m_d", time()) . "_" . $lastid . "." . $img1->getClientOriginalExtension();
                $img1->move('images/products/', $name_img1);
            }
            //Ki???m tra file img2
            if ($request->hasFile('product_img2')) {
                $img2 = $request->product_img2;

                $name_img2 = "img2_" . date("Y_m_d", time()) . "_" . $lastid . "." . $img2->getClientOriginalExtension();
                $img2->move('images/products/', $name_img2);
            }
            //Ki???m tra file img3
            if ($request->hasFile('product_img3')) {
                $img3 = $request->product_img3;

                $name_img3 = "img3_" . date("Y_m_d", time()) . "_" . $lastid . "." . $img3->getClientOriginalExtension();
                $img3->move('images/products/', $name_img3);
            }

            $addImg = ProductImage::insert([
                ['name' => $name_img1, 'product_id' => $lastid],
                ['name' => $name_img2, 'product_id' => $lastid],
                ['name' => $name_img3, 'product_id' => $lastid]
            ]);

            if ($addImg) {
                return back()->with('notify_success', 'Th??m s???n ph???m th??nh c??ng');
            } else {
                return back()->with('notify_fail', 'L???i th??m h??nh ???nh s???n ph???m th???t b???i!!!');
            }
        } else {
            return back()->with('notify_fail', 'L???i th??m s???n ph???m th???t b???i!!!');
        }
    }

    //add product list
    public function add_product_list()
    {
        $product_type = ProductType::all();

        return view('admin.back.add_product_list', compact('product_type'));
    }

    //post add product list
    public function post_add_product_list(Request $request)
    {
        $sl = count($request->name);
        $content_history = "";
        for ($i = 0; $i < $sl; $i++) {
            $addP = DB::table('product')->insert([
                'type' => $request->type[$i],
                'name' => $request->name[$i],
                'description' => $request->description[$i],
                'price' => $request->price[$i],
                'quantity' => $request->quantity[$i],
            ]);

            $lastid =  DB::getPdo('product')->lastInsertId();

            //Ki???m tra file img1
            // if ($request->hasFile('product_img1')) {
            $img1 = $request->product_img1[$i];

            $name_img1 = "img1_" . date("Y_m_d", time()) . "_" . $lastid . "." . $img1->getClientOriginalExtension();
            $img1->move('images/products/', $name_img1);
            // }
            //Ki???m tra file img2
            // if ($request->hasFile('product_img2')) {
            $img2 = $request->product_img2[$i];

            $name_img2 = "img2_" . date("Y_m_d", time()) . "_" . $lastid . "." . $img2->getClientOriginalExtension();
            $img2->move('images/products/', $name_img2);
            // }
            //Ki???m tra file img3
            // if ($request->hasFile('product_img3')) {
            $img3 = $request->product_img3[$i];

            $name_img3 = "img3_" . date("Y_m_d", time()) . "_" . $lastid . "." . $img3->getClientOriginalExtension();
            $img3->move('images/products/', $name_img3);
            // }

            $addImg = ProductImage::insert([
                ['name' => $name_img1, 'product_id' => $lastid],
                ['name' => $name_img2, 'product_id' => $lastid],
                ['name' => $name_img3, 'product_id' => $lastid]
            ]);

            $content_history .= "??? ID s???n ph???m: $lastid\n??? T??n s???n ph???m: " . $request->name[$i] . "\n\n";
        }

        $history = new StaffHistory;
        $history->staff_id = Auth::id();
        $history->title = "Th??m danh s??ch s???n ph???m m???i";
        $history->content = "C??c s???n ph???m v???a th??m:\n" . $content_history;
        $history->save();

        return redirect()->route('admin.product')->with('notify_success', '???? th??m danh s??ch s???n ph???m m???i th??nh c??ng!');
    }

    //ajax product detail
    public function product_detail($id)
    {
        if (!Product::withTrashed()->find($id)) {
            return "<h1>Kh??ng t??m th???y th??ng tin s???n ph???m</h1>";
        }

        $product = Product::withTrashed()->find($id);
        $product->name_type = ProductType::find(Product::withTrashed()->find($id)->type)->name_type;

        $img = Product::withTrashed()->find($id)->images;
        return view('admin.ajax.product_detail', compact('product', 'img'));
    }

    //ajax edit product
    public function edit_product($id)
    {
        $product = Product::where('id', $id)->first();
        $product_type = ProductType::get();
        $img = ProductImage::where('product_id', $id)->get();
        return view('admin.ajax.edit_product', compact('product', 'product_type', 'img'));
    }
    //post edit product
    public function post_edit_product(Request $request, $id)
    {
        $name = $type = $price = $description = $quantity = $image1 = $image2 = $image3 = null;

        $old = Product::find($request->id);

        $history = new StaffHistory;
        $history->staff_id = Auth::id();
        $history->title = "Ch???nh s???a s???n ph???m (ID Product: " . $id . ", $old->name)";

        if ($old->name != $request->name) {
            $name = "??? T??n: $old->name ??? $request->name \n";
        }
        if ($old->type != $request->type) {
            $type = "??? Lo???i: "
                . ProductType::whereId($old->type)->first()->name_type .
                "  ??? "
                . ProductType::whereId($request->type)->first()->name_type . "\n";
        }
        if ($old->price != $request->price) {
            $price = "??? Gi??: " . number_format($old->price) . " ??? " . number_format($request->price) . " \n";
        }
        if ($old->quantity != $request->quantity) {
            $quantity = "??? S??? l?????ng: $old->quantity ??? $request->quantity \n";
        }
        if ($old->description != $request->description) {
            $description = "??? M?? t??? c??: \n$old->description\n??? M?? t??? m???i:\n$request->description";
        }
        if ($request->hasFile('img1')) {
            $image1 = "??? ???? thay ?????i ???nh 1\n";
        }
        if ($request->hasFile('img2')) {
            $image2 = "??? ???? thay ?????i ???nh 2\n";
        }
        if ($request->hasFile('img3')) {
            $image3 = "??? ???? thay ?????i ???nh 3\n";
        }

        $history->content = $name . $image1 . $image2 . $image3 .  $type . $price . $quantity . $description;

        $history->save();

        $update = Product::find($request->id);
        $update->name = $request->name;
        $update->type = $request->type;
        $update->price = $request->price;
        $update->quantity = $request->quantity;
        $update->description = $request->description;

        if ($update->save()) {

            //Ki???m tra file img1
            if ($request->hasFile('img1')) {
                $img1 = $request->img1;

                $name_img1 = "img1_" . date("Y_m_d", time()) . "_" . $id . "." . $img1->getClientOriginalExtension();
                $img1->move('images/products/', $name_img1);

                //Xoa file hinh trong public/images/products
                $file_img1 = ProductImage::whereId($request->id_img1_old)->first();
                $file_path1 = public_path() . "/images/products/" . $file_img1->name;
                File::delete($file_path1);

                ProductImage::whereId($request->id_img1_old)
                    ->update([
                        'name' => $name_img1,
                    ]);
            }

            //Ki???m tra file img2
            if ($request->hasFile('img2')) {
                $img2 = $request->img2;

                $name_img2 = "img2_" . date("Y_m_d", time()) . "_" . $id . "." . $img2->getClientOriginalExtension();
                $img2->move('images/products/', $name_img2);

                //Xoa file hinh trong public/images/products
                $file_img2 = ProductImage::whereId($request->id_img2_old)->first();
                $file_path2 = public_path() . "/images/products/" . $file_img2->name;
                File::delete($file_path2);

                ProductImage::whereId($request->id_img2_old)
                    ->update([
                        'name' => $name_img2,
                    ]);
            }

            //Ki???m tra file img3
            if ($request->hasFile('img3')) {
                $img3 = $request->img3;

                $name_img3 = "img3_" . date("Y_m_d", time()) . "_" . $id . "." . $img3->getClientOriginalExtension();
                $img3->move('images/products/', $name_img3);

                //Xoa file hinh trong public/images/products
                $file_img3 = ProductImage::whereId($request->id_img3_old)->first();
                $file_path3 = public_path() . "/images/products/" . $file_img3->name;
                File::delete($file_path3);

                ProductImage::whereId($request->id_img3_old)
                    ->update([
                        'name' => $name_img3,
                    ]);
            }

            return back()->with('notify_success', 'Thay ?????i th??ng tin s??n ph???m th??nh c??ng!');
        } else {
            return back()->with('notify_fail', 'Thay ?????i th??ng tin s???n ph???m th???t b???i');
        }
    }

    //delete product
    public function delete_product($id)
    {

        //Xoa file hinh trong public/images/products
        // $arr_img = DB::table('product_images')->where('product_id', $id)->get();
        // foreach ($arr_img as $k => $v) {
        //     $file_path = public_path() . "/images/products/" . $v->name;

        //     File::delete($file_path);
        // }

        $product = Product::find($id);
        $history = new StaffHistory;
        $history->staff_id = Auth::id();
        $history->title = "X??a s???n ph???m";
        $history->content = "S???n ph???m v?? c??c h??nh ???nh li??n quan ???? b??? x??a\nTh??ng tin s???n ph???m ???? x??a:\n" .
            "??? T??n s???n ph???m: $product->name\n" .
            "??? Lo???i s???n ph???m: " . ProductType::whereId($product->type)->first()->name_type . "\n" .
            "??? Gi?? s???n ph???m: " . number_format($product->price) . "\n" .
            "??? S??? l?????ng s???n ph???m: $product->quantity\n" .
            "??? M?? t??? s???n ph???m: \n$product->description";
        $history->save();

        $delete = Product::find($id)->delete();

        if ($delete) {
            return redirect('/admin/product')->with('notify_success', 'X??a s???n ph???m th??nh c??ng');
        } else {
            return redirect('/admin/product')->with('notify_fail', 'X??a s???n ph???m th???t b???i!!!');
        }
    }

    //delete list product
    public function delete_product_list(Request $request)
    {
        $list_product_id = $request->product_records;

        $content_history = "";

        foreach ($list_product_id as $value) {
            $product = Product::find($value);

            $content_history .= "??? ID s???n ph???m: $value\n" .
                "??? T??n s???n ph???m: $product->name\n" .
                "??? Lo???i s???n ph???m: " . ProductType::whereId($product->type)->first()->name_type . "\n\n";

            $del  = Product::withTrashed()->find($value)->delete();

            if (!$del) {
                return redirect()->route('admin.product')->with('notify_fail', 'X??a s???n ph???m "' . Product::find($value)->name . '" th???t b???i');
            }
        }

        $history = new StaffHistory;
        $history->staff_id = Auth::id();
        $history->title = "X??a danh s??ch s???n ph???m";
        $history->content = "S???n ph???m v?? c??c h??nh ???nh li??n quan ???? b??? x??a\nTh??ng tin danh s??ch s???n ph???m ???? x??a:\n" . $content_history;
        $history->save();

        return redirect()->route('admin.product')->with('notify_success', '???? x??a c??c s???n ph???m th??nh c??ng');
    }

    //post restore product
    public function post_restore_product($id)
    {

        $restore  = Product::withTrashed()->find($id)->restore();

        if ($restore) {
            return redirect()->route('admin.product')->with('notify_success', '???? kh??i ph???c s???n ph???m th??nh c??ng');
        } else {
            return redirect()->route('admin.product.deletelist')->with('notify_fail', 'Kh??i ph???c s???n ph???m th???t b???i');
        }
    }

    //post restore products list
    public function post_restore_product_list(Request $request)
    {
        $list_product_id = $request->product_records;
        foreach ($list_product_id as $value) {
            $restore  = Product::withTrashed()->find($value)->restore();

            if (!$restore) {
                return redirect()->route('admin.product.deletelist')->with('notify_fail', 'Kh??i ph???c s???n ph???m "' . Product::find($value)->name . '" th???t b???i');
            }
        }

        return redirect()->route('admin.product')->with('notify_success', '???? kh??i ph???c c??c s???n ph???m th??nh c??ng');
    }

    // ===================================PRODUCT TYPE====================================================
    //product_type list
    public function product_type()
    {
        $list = DB::table('product_type')->get();
        return view('admin.back.product_type', compact('list'));
    }
    //add product type (giao dien)
    public function addproduct_type()
    {
        return view('admin.back.addproduct_type');
    }
    //post add product type
    public function postaddproduct_type(Request $request)
    {
        $add = DB::table('product_type')->insert([
            'name_type' => $request->product_type_name
        ]);

        if ($add) {

            $history = new StaffHistory;
            $history->staff_id = Auth::id();
            $history->title = "Th??m lo???i s???n ph???m";
            $history->content = "???? th??m lo???i s???n ph???m m???i: \"$request->product_type_name\"\n";
            $history->save();

            return back()->with('notify_success', 'Th??m lo???i s???n ph???m th??nh c??ng');
        } else {
            return back()->with('notify_fail', 'Th??m lo???i s???n ph???m th???t b???i!!!');
        }
    }

    //edit product type
    public function edit_product_type($id)
    {
        $pt = ProductType::where('id', $id)->first();
        return view('admin.ajax.edit_product_type', compact('pt'));
    }

    //post edit product type
    public function post_edit_product_type(Request $request, $id)
    {
        $update = ProductType::find($id);
        $update->name_type = $request->name_type;
        if ($update->save()) {
            return redirect('/admin/product_type')->with('notify_success', '???? thay ?????i t??n t??? "' . $request->old_name . '" th??nh "' . $request->name_type . '" th??nh c??ng!');
        } else {
            return redirect('/admin/product_type')->with('notify_fail', 'Thay ?????i t??n lo???i s???n ph???m th???t b???i');
        }
    }

    //delete product type
    public function delete_addproduct_type($id)
    {
        //Xoa file hinh trong public/images/products
        $arr_product = Product::where('type', $id)->get();
        foreach ($arr_product as $value) {
            $arr_img = ProductImage::where('product_id', $value->id)->get();
            foreach ($arr_img as $v) {
                $file_path = public_path() . "/images/products/" . $v->name;

                File::delete($file_path);
            }
        }

        $del = DB::table('product_type')->where('id', $id)->delete();

        if ($del) {
            return back()->with('notify_success', 'X??a lo???i s???n ph???m th??nh c??ng');
        } else {
            return back()->with('notify_fail', 'X??a lo???i s???n ph???m th???t b???i!!!');
        }
    }


    // =====================================PROMOTION==================================================
    //promotion
    public function promotion()
    {
        $list = Promotion::all();

        return view('admin.back.promotion', compact('list'));
    }

    //post add promotion
    public function post_add_promotion(Request $request)
    {
        if ($request->start_date >= $request->end_date) {
            return redirect()->route('admin.promotion')->with('notify_fail', 'Th???i gian khuy???n m??i kh??ng h???p l???, ng??y b???t ?????u ph???i nh??? h??n ng??y k???t th??c!');
        }

        $check = Promotion::where('code', $request->code)->get();

        if (count($check) > 0) {
            return redirect()->route('admin.promotion')->with('notify_fail', 'M?? khuy???n m??i ???? t???n t???i!');
        }

        $add = Promotion::create($request->all());
        $add->code = strtoupper($request->code);

        if ($add->save()) {

            $history = new StaffHistory;
            $history->staff_id = Auth::id();
            $history->title = "Th??m m?? khuy???n m??i";
            $history->content = "???? th??m m?? khuy???n m??i m???i: \"$request->code\"\n";
            $history->save();

            return redirect()->route('admin.promotion')->with('notify_success', '???? th??m m?? khuy???n m??i m???i');
        } else {
            return redirect()->route('admin.promotion')->with('notify_fail', 'Th??m m?? khuy???n m??i th???t b???i');
        }
    }

    //delete promotion
    public function delete_promotion($id)
    {

        $promotion = Promotion::find($id);

        $history = new StaffHistory;
        $history->staff_id = Auth::id();
        $history->title = "X??a m?? khuy???n m??i";
        $history->content = "Th??ng tin m?? khuy???n m??i ???? x??a:\n" .
            "??? M?? khuy???n m??i: $promotion->code\n" .
            "??? Gi?? tr???: $promotion->percent%\n" .
            "??? Ng??y b???t ?????u: " . date('d/m/Y', strtotime($promotion->start_date)) . "\n" .
            "??? Ng??y k???t th??c: " . date('d/m/Y', strtotime($promotion->end_date)) . "";
        $history->save();

        if (Promotion::find($id)->delete()) {
            return redirect()->route('admin.promotion')->with('notify_success', 'X??a m?? khuy???n m??i th??nh c??ng');
        } else {
            return redirect()->route('admin.promotion')->with('notify_fail', 'X??a m?? khuy???n m??i th???t b???i!!!');
        }
    }

    // ================================STATISTICAL (Th???ng k??)===========================================
    //statistical
    public function statistical()
    {
        $order = Order::all();

        $out_of_stock = Product::where('quantity', '<=', 10)->orderBy('quantity', 'ASC')->get();

        $ton_kho = Product::where('quantity', '>', 0)->get();

        $order_detail = OrderDetail::withTrashed()->get();

        $order_detail_groupby = OrderDetail::withTrashed()->groupby('product_id')
            ->selectRaw('product_id, sum(quantity) as quantity')
            ->orderBy('quantity', 'DESC')
            ->take(5)
            ->get();

        return view('admin.back.statistical', compact('order', 'order_detail', 'order_detail_groupby', 'out_of_stock', 'ton_kho'));
    }

    //statistical product | statis product
    public function statistical_product()
    {
        $out_of_stock = Product::where('quantity', '<=', 10)->orderBy('quantity', 'ASC')->get();

        $ton_kho = Product::where('quantity', '>', 0)->get();

        return view('admin.back.statis_product', compact('out_of_stock', 'ton_kho'));
    }

    //submit statistical
    public function submit_statistical(Request $request)
    {
        $start = $request->start_date;

        $end = $request->end_date;

        $out_of_stock = Product::where('quantity', '<=', 10)->orderBy('quantity', 'ASC')->get();

        $ton_kho = Product::where('quantity', '>', 0)->get();

        $order = Order::whereBetween('created_at', [$start, $end])->get();

        $order_detail = OrderDetail::withTrashed()->whereBetween('created_at', [$start, $end])->get();

        $order_detail_groupby = OrderDetail::withTrashed()->groupby('product_id')
            ->selectRaw('product_id, sum(quantity) as quantity')
            ->orderBy('quantity', 'DESC')
            ->take(5)
            ->get();

        $product_buy = OrderDetail::withTrashed()->groupby('product_id')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('product_id, sum(quantity) as quantity')
            ->get();

        return view(
            'admin.back.statistical',
            compact('order', 'order_detail', 'start', 'end', 'order_detail_groupby', 'out_of_stock', 'ton_kho')
        );
    }

    // ======================================STAFF======================================================
    //staff list
    public function staff()
    {
        $staff = User::where('role', '<', 3)->where('role', '<>', 1)->get();
        return view('admin.back.staff', compact('staff'));
    }

    //Form th??m t??i kho???n nh??n vi??n
    public function addstaff()
    {
        return view('admin.back.add_staff');
    }

    //post add staff
    public function postaddstaff(Request $request)
    {
        $checkTK = DB::table('users')
            ->where('phone', $request->phone)
            ->orWhere('email', $request->email)
            ->get();

        if (count($checkTK) > 0) {
            return back()->with('notify_fail', 'S??t ho???c email ???? t???n t???i!');
        }

        if ($request->password != $request->repass) {
            return back()->with('notify_fail', 'M???t kh???u x??c nh???n kh??ng ch??nh x??c');
        }

        $createTK = DB::table('users')->insert([
            'fullname' => $request->fullname,
            'address' => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'password' => bcrypt($request->password),
            'role' => 2,
        ]);

        if ($createTK) {
            return back()->with('notify_success', 'T???o t??i kho???n th??nh c??ng!!!');
        } else {
            return back()->with('notify_fail', 'L???i t???o t??i kho???n kh??ng th??nh c??ng!!!');
        }
    }

    //delete staff
    public function delete_staff($id)
    {
        $del = User::withTrashed()->find($id);

        if ($del->delete()) {
            return back()->with('notify_success', 'X??a nh??n vi??n "' . $del->fullname . '" th??nh c??ng!!!');
        } else {
            return back()->with('notify_fail', 'X??a nh??n vi??n "' . $del->fullname . '" th???t b???i!!!');
        }
    }

    //edit staff
    public function edit_staff(Request $request)
    {
        $update = User::find($request->id);
        $update->email = $request->email;
        $update->phone = $request->phone;

        if (isset($request->new_pass) && $request->new_pass != null) {
            $update->new_pass = bcrypt($request->new_pass);
        }

        if ($update->save()) {
            return back()->with('notify_success', 'Thay ?????i th??ng tin nh??n vi??n th??nh c??ng');
        } else {
            return back()->with('notify_fail', 'Thay ?????i th??ng tin nh??n vi??n th???t b???i!!!');
        }
    }

    // ====================================ORDER==================================================
    //status:
    //Ch??a x??c nh???n
    //??ang giao h??ng
    //???? ho??n th??nh
    //Th???t b???i

    //order
    public function order()
    {
        $order = Order::leftjoin('users as b', 'user_id', 'b.id')
            ->leftjoin('users as c', 'admin_id', 'c.id')
            ->select('order.*', 'b.fullname as user_fullname', 'c.fullname as admin_fullname')
            ->get();
        return view('admin.back.order', compact('order'));
    }

    //order detail
    public function order_detail($id)
    {
        $order = Order::leftjoin('promotion as a', 'promotion_id', 'a.id')
            ->select('order.*', 'a.percent')
            ->where('order.id', $id)->first();

        $order_detail = OrderDetail::leftjoin('product as b', 'order_detail.product_id', 'b.id')
            ->select('order_detail.*', 'b.name', 'b.description', 'b.price')
            ->where('order_detail.order_id', $id)
            ->get();
        // return $order_detail;

        $user = User::whereId($order->user_id)->first();

        $staff = User::whereId($order->admin_id)->first();

        return view('admin.back.order_detail', compact('order', 'order_detail', 'user', 'staff'));
    }

    //order action
    public function order_action(Request $request)
    {
        $list_id = $request->order_records;
        $content_history = "";

        //X??c nh???n ????n h??ng
        if (isset($request->submit_confirm)) {
            foreach ($list_id as $c) {
                if (Order::find($c)->status != "Ch??a x??c nh???n") {
                    return redirect()->route('admin.order')->with('notify_fail', 'H??nh ?????ng kh??ng h???p l??, c?? ????n h??ng ???? x??c nh???n. Xin ki???m tra l???i!!!');
                }
            }

            foreach ($list_id as $v) {
                $update = Order::find($v);

                $content_history = "??? ID ????n h??ng: $v\n" .
                    "??? Tr???ng th??i ????n h??ng: \"$update->status\" ??? \"??ang giao h??ng\" \n\n";

                $update->status = "??ang giao h??ng";
                $update->admin_id = Auth::id();
                $update->delivery_date = date('Y-m-d');
                $update->save();
            }
        }

        //????n h??ng ho??n th??nh
        if (isset($request->submit_done)) {

            foreach ($list_id as $c) {
                if (Order::find($c)->status != "??ang giao h??ng") {
                    return redirect()->route('admin.order')->with('notify_fail', 'H??nh ?????ng kh??ng h???p l??. Xin ki???m tra l???i!!!');
                }
                if (Auth::id() != Order::find($c)->admin_id && Order::find($c)->admin_id != null) {
                    return redirect()->route('admin.order')->with('notify_fail', 'B???n kh??ng ph???i ng?????i ???? duy???t ????n h??ng n??y.');
                }
            }


            foreach ($list_id as $v) {
                $update = Order::find($v);

                $content_history = "??? ID ????n h??ng: $v\n" .
                    "??? Tr???ng th??i ????n h??ng: \"$update->status\" ??? \"???? ho??n th??nh\" \n\n";

                $update->status = "???? ho??n th??nh";
                $update->receiving_date = date('Y-m-d');
                $update->save();
            }
        }

        //????n h??ng th???t b???i
        if (isset($request->submit_fail)) {

            foreach ($list_id as $c) {
                if (Order::find($c)->status != "??ang giao h??ng") {
                    return redirect()->route('admin.order')->with('notify_fail', 'H??nh ?????ng kh??ng h???p l??. Xin ki???m tra l???i!!!');
                }
                if (Auth::id() != Order::find($c)->admin_id && Order::find($c)->admin_id != null) {
                    return redirect()->route('admin.order')->with('notify_fail', 'B???n kh??ng ph???i ng?????i ???? duy???t ????n h??ng n??y.');
                }
            }


            foreach ($list_id as $v) {
                $update = Order::find($v);

                $content_history = "??? ID ????n h??ng: $v\n" .
                    "??? Tr???ng th??i ????n h??ng: \"$update->status\" ??? \"Th???t b???i\" \n\n";

                $update->status = "Th???t b???i";
                $update->save();
            }
        }

        $history = new StaffHistory;
        $history->staff_id = Auth::id();
        $history->title = "C???p nh???t tr???ng th??i ????n h??ng";
        $history->content = "Trang th??i ????n h??ng ???? ???????c c???p nh???t:\n" . $content_history;
        $history->save();

        return redirect()->route('admin.order')->with('notify_success', 'C???p nh???t tr???ng th??i ????n h??ng th??nh c??ng');
    }

    // ====================================PROFILE==================================================
    //profile
    public function profile($id)
    {
        $info = User::whereId($id)->first();
        $history = StaffHistory::where('staff_id', $id)->get();
        $order = Order::leftjoin('users', 'admin_id', 'users.id')
            ->select('*', 'users.fullname')
            ->where('admin_id', $id)
            ->get();
        return view('admin.back.profile', compact('info', 'history', 'order'));
    }

    //Edit profile
    public function post_edit_profile(Request $request)
    {
        $update = User::find($request->id);
        $update->fullname = $request->fullname;
        $update->gender = $request->gender;
        $update->address = $request->address;
        $update->birthday = $request->birthday;

        if ($request->hasFile('avatar')) {
            $img = $request->avatar;

            $name_img = "avatar_" . date("Y_m_d", time()) . "_$request->id." . $img->getClientOriginalExtension();
            $img->move('images/avatar/', $name_img);

            $update->avatar = $name_img;
        }

        if ($update->save()) {
            return redirect('/admin/profile/' . Auth::id())->with('notify_success', 'C???p nh???t th??ng tin c?? nh??n th??nh c??ng!');
        } else {
            return redirect('/admin/profile/' . Auth::id())->with('notify_fail', 'C???p nh???t th??ng tin c?? nh??n th???t b???i!');
        }
    }
}
