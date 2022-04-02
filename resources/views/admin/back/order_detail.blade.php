@extends('admin.template.master')

@section('title', 'Order Detail | Admin - NTN Shop')

@section('heading', '')

@section('des_heading', '')

@section('x_heading', 'Chi tiết đơn hàng')

@section('content')

    <section class="content invoice">
        <!-- title row -->
        <div class="row">
            <div class="  invoice-header">
                <h1>
                    <i class="fa fa-globe"></i> Hóa đơn
                    <small class="pull-right fs-5">Ngày lập: {{ date('d/m/Y') }}</small>
                </h1>
            </div>
            <!-- /.col -->
        </div>
        <br><br>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <div class="col-10">
                    Nhân viên xác nhận:
                    <hr class="w-50 my-2">
                    @if ($staff != null)
                        <address>
                            <strong class="fs-6">{{ $staff->fullname }}</strong><br>
                            <strong>Địa chỉ:</strong> <i>{{ $staff->address }}</i>
                            <br><strong>Số điện thoại:</strong> {{ $staff->phone }}
                            <br><strong>Email:</strong> {{ $staff->email }}
                        </address>
                    @else
                        <address>
                            <strong>Chưa có nhân viên xác nhận</strong>
                        </address>
                    @endif

                </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <div class="col-10">
                    Khách hàng:
                    <hr class="w-50 my-2">
                    <address>
                        <strong class="fs-6">{{ $user->fullname }}</strong><br>
                        <strong>Địa chỉ:</strong> <i>{{ $user->address }}</i>
                        <br><strong>Số điện thoại:</strong> {{ $user->phone }}
                        <br><strong>Email:</strong> {{ $user->email }}
                    </address>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Đơn hàng: #{{ $order->id }}</b>
                <br>
                <b>Ngày thanh toán:</b>
                @if ($order->payment_method == 'momo')
                    {{ date('d/m/Y', strtotime($order->created_at)) }}
                @else
                    @if ($order->delivery_date == null)
                        Chưa thanh toán
                    @else
                        {{ date('d/m/Y', strtotime($order->delivery_date)) }}
                    @endif
                @endif
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="  table">
                <table class="table table-striped">
                    <thead>
                        <tr class="text-center">
                            <th>Mã sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá </th>
                            <th>Bảo hành</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php($subtotal = 0)

                        @foreach ($order_detail as $v)
                            <tr class="text-center">
                                <td>{{ $v->product_id }}</td>
                                <td>{{ $v->name }}</td>
                                <td>{{ $v->quantity }}</td>
                                <td>{{ number_format($v->price) }} VNĐ</td>
                                <td>1 tháng kể từ ngày lập hóa đơn</td>
                                <td>{{ number_format($subtotal += $v->price * $v->quantity) }} VNĐ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- accepted payments column -->
            <div class="col-md-6">
                <p class="lead">Phương thức thanh toán: <b>{{ $order->payment_method }}</b></p>

                <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                    @if ($order->payment_method == 'momo')
                        Đơn hàng đã được thanh toán qua momo.
                    @else
                        Đơn hàng được thanh toán bằng tiền mặt khi nhận hàng.
                    @endif
                    <br><br>
                    Trong thời gian bảo hành nếu có bất cứ vấn đề gì
                    xin hãy liên hệ qua email hoặc số điện thoại của cửa hàng.
                    <br><br>
                    Số điện thoại: 099 978 9889<br>
                    Email: ntnsotre@gmail.com
                </p>
            </div>
            <!-- /.col -->
            <div class="col-md-6">
                <p class="lead">Tổng thành tiền</p>
                <br>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width:50%">Tổng phụ:</th>
                                <td>{{ number_format($subtotal) }} VNĐ</td>
                            </tr>
                            <tr>
                                <th>Khuyến mãi ({{ $order->percent ?: 0 }}%)</th>
                                <td>
                                    {{ number_format(($subtotal * $order->percent) / 100) }} VNĐ
                                </td>
                            </tr>
                            <tr>
                                <th>VAT:</th>
                                <td>Sản phẩm đã bao gồm VAT</td>
                            </tr>
                            <tr>
                                <th>Tổng:</th>
                                <td>{{ number_format($subtotal - ($subtotal * $order->percent) / 100) }} VNĐ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

    </section>
    <!-- this row will not appear when printing -->
    @if ($staff != null)
    <div class="row no-print">
        <div class=" ">
            <button class="btn btn-default" onclick="PrintElem()"><i class="fa fa-print"></i> Print</button>
            <button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i>
                Generate PDF</button>
        </div>
    </div>
    @endif

    <script>
        function PrintElem()
        {
            var mywindow = window.open('', 'PRINT', 'height=400,width=600');

            mywindow.document.write('<html><head>');
            mywindow.document.write('<link href="https://colorlib.com/polygon/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">');
            mywindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">');
            mywindow.document.write('<link href="https://colorlib.com/polygon/build/css/custom.css" rel="stylesheet">');
            mywindow.document.write('</head><body>');
            mywindow.document.write('<div>' + document.querySelector('.invoice').innerHTML  + '</div>');
            mywindow.document.write('</body></html>');

            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10*/

            mywindow.print();
            // mywindow.close();

            return true;
        }
    </script>

@stop
