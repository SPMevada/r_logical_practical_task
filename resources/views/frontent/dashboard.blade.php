@extends('frontent_layout.app')
@section('title','Front Users')
@yield('header-css')
<style>
    * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .product-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 18px;
        }

        .product-details {
            padding: 15px;
            text-align: center;
        }

        .product-title {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
        }

        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #1a73e8;
        }
</style>
@section('content')
<div class="container">
    {{-- <div class="sidebar">
        <div class="username">{{ Auth::guard('frontUser')->check() ? Auth::guard('frontUser')->user()->name : '' }}</div>
        <form action="{{ route('frontent.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link nav-link" style="text-decoration: none;">Logout</button>
        </form>
    </div> --}}
    
    @forelse ($products as $product)
        @if ($product['categories'])
            @forelse ($product['categories'] as $category)
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ Storage::url($category->image) }}" style="height: 100px; width: 100px">
                    </div>
                    <div class="product-details">
                        <div class="product-title">{{ !empty($product->name) ? $product->name : '' }}</div>
                        <div class="product-price">Price: {{ !empty($product->price) ?  $product->price : '' }}</div>
                        <p>
                            {{ !empty($product->description) ?  $product->description : '' }}
                        </p>
                        {{-- <div class="product-price">Quantity: {{ !empty($product->qty) ?  $product->qty : '' }}</div> --}}
                        <button type="button" class="btn btn-primary add_to_cart" data-category_id="{{ !empty($category->id) ?  $category->id : '' }}" data-product_id="{{ !empty($product->id) ?  $product->id : '' }}">Add to Cart</button>
                    </div>
                </div>
            @empty
                
            @endforelse
        @endif
    @empty
        
    @endforelse
</div>
@endsection
@section('script')
    <script type="text/javascript">
       $(document).ready(function() {
            $(document).on('click','.add_to_cart',function(e) {
              e.preventDefault();
              const self = $(this);
              const product_id = self.data('product_id');
              const category_id = self.data('category_id');
              $.ajax({
                  url: "{{ route('frontent.addtocart') }}",
                  type: "POST",
                  dataType: "JSON",
                  data: {
                    product_id: product_id,
                    category_id: category_id,
                  },
                  success: function(response) {
                      if(response.status) {
                          successToastMsg(response.message);
                          window.location.href = "{{ route('frontent.dashboard') }}";
                      } else {
                          toastFailedMsg(response.message)
                      }
                  },
                  error: function(response) {
                      toastFailedMsg(response.responseJSON.message)
                  }
              });
            });

            function successToastMsg(message) {
              Toastify({
                  text: message,
                  duration: 3000,
                  destination: "https://github.com/apvarun/toastify-js",
                  newWindow: true,
                  close: true,
                  gravity: "top",
                  position: "right",
                  stopOnFocus: true,
                  style: {
                      background: "linear-gradient(to right, #00b09b, #96c93d)"
                  },
              }).showToast();
            }

            function toastFailedMsg(message) {
                Toastify({
                    text: message,
                    duration: 3000,
                    destination: "https://github.com/apvarun/toastify-js",
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(98.3deg, rgb(0, 0, 0) 10.6%, rgb(255, 0, 0) 97.7%)"
                    },
                }).showToast();
            }
       });
    </script>
@endsection