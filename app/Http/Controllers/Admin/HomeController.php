<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\UserType;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductType;

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
        $product_list = DB::table('product as a')
            ->leftJoin('product_type as b', 'a.type', 'b.id')
            ->select('a.*', 'b.name_type')
            ->get();
        return view('admin.back.product', compact('product_list'));
    }

    //add product
    public function addproduct()
    {
        $product_type = DB::table('product_type')->get();
        return view('admin.back.addproduct', compact('product_type'));
    }

    //post add product
    public function postaddproduct(Request $request)
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
            //Kiểm tra file img1
            if ($request->hasFile('product_img1')) {
                $img1 = $request->product_img1;

                $name_img1 = "img1_" . date("Y_m_d", time()) . "_" . $lastid . "." . $img1->getClientOriginalExtension();
                $img1->move('images/products/', $name_img1);
            }
            //Kiểm tra file img2
            if ($request->hasFile('product_img2')) {
                $img2 = $request->product_img2;

                $name_img2 = "img2_" . date("Y_m_d", time()) . "_" . $lastid . "." . $img2->getClientOriginalExtension();
                $img2->move('images/products/', $name_img2);
            }
            //Kiểm tra file img3
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
                return back()->with('notify_success', 'Thêm sản phẩm thành công');
            } else {
                return back()->with('notify_fail', 'Lỗi thêm hình ảnh sản phẩm thất bại!!!');
            }
        } else {
            return back()->with('notify_fail', 'Lỗi thêm sản phẩm thất bại!!!');
        }
    }

    //ajax product detail
    public function product_detail($id)
    {
        $product = DB::table('product as a')
            ->leftJoin('product_type as b', 'a.type', 'b.id')
            ->select('a.*', 'b.name_type')
            ->where('a.id', $id)->first();

        $img = DB::table('product_images')->where('product_id', $id)->get();
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

        $update = Product::find($request->id);
        $update->name = $request->name;
        $update->type = $request->type;
        $update->price = $request->price;
        $update->quantity = $request->quantity;
        $update->description = $request->description;

        if ($update->save()) {

            //Kiểm tra file img1
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

            //Kiểm tra file img2
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

            //Kiểm tra file img3
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

            return redirect('/admin/product')->with('notify_success', 'Thay đổi thông tin sán phẩm thành công!');
        } else {
            return redirect('/admin/product')->with('notify_fail', 'Thay đổi thông tin sản phẩm thất bại');
        }
    }

    //delete product
    public function delete_product($id)
    {

        //Xoa file hinh trong public/images/products
        $arr_img = DB::table('product_images')->where('product_id', $id)->get();
        foreach ($arr_img as $k => $v) {
            $file_path = public_path() . "/images/products/" . $v->name;

            File::delete($file_path);
        }

        $delete = DB::table('product')->where('id', $id)->delete();

        if ($delete) {
            return redirect('/admin/product')->with('notify_success', 'Xóa sản phẩm thành công');
        } else {
            return redirect('/admin/product')->with('notify_fail', 'Xóa sản phẩm thất bại!!!');
        }
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
            return back()->with('notify_success', 'Thêm loại sản phẩm thành công');
        } else {
            return back()->with('notify_fail', 'Thêm loại sản phẩm thất bại!!!');
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
            return redirect('/admin/product_type')->with('notify_success', 'Đã thay đổi tên từ "' . $request->old_name . '" thành "' . $request->name_type . '" thành công!');
        } else {
            return redirect('/admin/product_type')->with('notify_fail', 'Thay đổi tên loại sản phẩm thất bại');
        }
    }

    //delete product type
    public function delete_addproduct_type($id)
    {
        $del = DB::table('product_type')->where('id', $id)->delete();
        if ($del) {
            return back()->with('notify_success', 'Xóa loại sản phẩm thành công');
        } else {
            return back()->with('notify_fail', 'Xóa loại sản phẩm thất bại!!!');
        }
    }


    // =====================================PROMOTION==================================================
    //promotion
    public function promotion()
    {
        return view('admin.back.promotion');
    }

    // ================================STATISTICAL (Thống kê)===========================================
    //statistical
    public function statistical()
    {
        return view('admin.back.statistical');
    }

    // ======================================STAFF======================================================
    //staff list
    public function staff()
    {
        $staff = DB::table('users')
            ->where([
                ['role', '<', 3],
                ['role', '<>', 1],
            ])
            ->get();
        return view('admin.back.staff', compact('staff'));
    }

    //Form thêm tài khoản nhân viên
    public function addstaff()
    {
        return view('admin.back.addstaff');
    }

    //post add staff
    public function postaddstaff(Request $request)
    {
        $checkTK = DB::table('users')
            ->where('phone', $request->phone)
            ->orWhere('email', $request->email)
            ->get();

        if (count($checkTK) > 0) {
            return back()->with('notify_fail', 'Sđt hoặc email đã tồn tại!');
        }

        if ($request->password != $request->repass) {
            return back()->with('notify_fail', 'Mật khẩu xác nhận không chính xác');
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
            return back()->with('notify_success', 'Tạo tài khoản thành công!!!');
        } else {
            return back()->with('notify_fail', 'Lỗi tạo tài khoản không thành công!!!');
        }
    }

    // ====================================ORDER==================================================
    //order
    public function order()
    {
        return view('admin.back.order');
    }

    // ====================================PROFILE==================================================
    //profile
    public function profile($id)
    {
        $info = DB::table('users')->where('id', $id)->first();
        return view('admin.back.profile', compact('info'));
    }
}
