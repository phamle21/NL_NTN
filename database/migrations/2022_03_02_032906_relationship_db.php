<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        //TK - Loai TK
        Schema::table('TaiKhoan', function (Blueprint $table) {
            $table->foreign('id_LoaiTaiKhoan')
                ->references('id')->on('LoaiTaiKhoan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        //Hinh anh - San Pham
        Schema::table('HinhAnh', function (Blueprint $table) {
            $table->foreign('id_SanPham')
                ->references('id')->on('SanPham')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        //San pham - Loai san pham
        Schema::table('SanPham', function (Blueprint $table) {
            $table->foreign('id_LoaiSanPham')
                ->references('id')->on('LoaiSanPham')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        //Khuyen mai - Loai san pham
        Schema::table('KhuyenMai', function (Blueprint $table) {
            $table->foreign('id_LoaiSanPham')
                ->references('id')->on('LoaiSanPham')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        //Danh gia - San pham - Tai khoan
        Schema::table('DanhGia', function (Blueprint $table) {
            $table->foreign('id_TaiKhoan')
                ->references('id')->on('TaiKhoan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('id_SanPham')
                ->references('id')->on('SanPham')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        //Hoa don - Don hang
        Schema::table('HoaDon', function (Blueprint $table) {
            $table->foreign('id_DonHang')
                ->references('id')->on('DonHang')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        //Donhang - Tai khoan - hoa don?
        Schema::table('DonHang', function (Blueprint $table) {
            $table->foreign('id_TaiKhoan')
                ->references('id')->on('TaiKhoan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('id_HoaDon')
                ->references('id')->on('HoaDon')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        //Chi tiet don hang - San Pham - Don hàng
        Schema::table('ChiTietDonHang', function (Blueprint $table) {
            $table->foreign('id_SanPham')
                ->references('id')->on('SanPham')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('id_DonHang')
                ->references('id')->on('DonHang')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        //Bao hanh - Hoa don
        Schema::table('BaoHanh', function (Blueprint $table) {
            $table->foreign('id_HoaDon')
                ->references('id')->on('HoaDon')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};