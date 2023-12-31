@extends('layouts.admin.app')

@section('title', translate('brokers rate list'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/banner.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('brokers rate list')}}
                </span>
            </h1>
        </div>
        
        

            <div class="row g-3">
            @foreach($rows as $row)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-3">{{ $row->brokerDetail->f_name .' '.$row->brokerDetail->l_name}}</h5>
                       
                        <form action="{{route('admin.broker-rate-list.wh_receiver_post_order')}}"
                              method="post">
                            @csrf
                            
                            <input type="hidden" name="broker_rate_list_id" value="{{$row->id}}">
                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('name')}}</strong>:{{$row->title}}
                                    </label>
                                    <label class="form-label text--title" style="float: right">
                                        <strong>{{translate('date')}}</strong>:{{ date('d-m-Y H:i A', strtotime($row->date_time))}}
                                    </label>
                                </div>

                                <div class="d-flex flex-wrap mb-4">
                                    <table class="table table-striped">
                                        <thead>
                                            <th>#</th>
                                         <th>Product</th>
                                         <th>Available Qty</th>
                                         <th>Unit</th>
                                         <th>Rate</th>
                                         <th>Order Qty</th>
                                        </thead>
                                        <tbody>
                                            
                                            @foreach($row->rateListDetail as $key => $value)
                                                <tr class="">
                                                    <input type="hidden" name="products[]" value="{{($value->product_id)}}">
                                                <td>{{ $key+1}}</td>
                                                <td>{{ $value->productDetail->name }}</td>
                                                <td>{{ $value->available_qty }}</td>
                                                <td>{{ $value->unit }}</td>
                                                <td>{{ $value->rate }}</td>
                                                <td><input type="text" name="order_qty[]"></td>
                                                </tr>
                                          @endforeach
                                          
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                           
                                
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
    </div>

@endsection

@push('script_2')
  

@endpush
