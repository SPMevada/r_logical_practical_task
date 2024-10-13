@extends('layouts.app')
@section('title','Login')
@section('content')
<section class="bg-primary p-3 p-md-4 p-xl-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 p-md-4 p-xl-5">
              <div class="row">
                <div class="col-12">
                  <div class="mb-5">
                    <h2 class="h3">Admin Login</h2>
                  </div>
                </div>
              </div>
              <form id="loginForm" method="POST">
                @csrf
                <div class="row gy-3 overflow-hidden">
                  <div class="col-12">
                    <div class="form-group">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com">
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="d-grid">
                      <button class="btn bsb-btn-2xl btn-primary" type="submit">Login</button>
                      <a class="btn bsb-btn-2xl btn-primary mt-3" href="{{ route('user.register') }}" >Register</a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
@section('script')
    <script type="text/javascript">
      $(document).ready(function() {
          $('#loginForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        mixLength: 6
                    },
                },
                highlight: function (element) {
                    $(element).parent().addClass('error')
                },
                unhighlight: function (element) {
                    $(element).parent().removeClass('error')
                }
          });

          $(document).on('submit','#loginForm',function(e) {
              e.preventDefault();

              $.ajax({
                  url: "{{ route('user.login') }}",
                  type: "POST",
                  dataType: "JSON",
                  data: $(this).serialize(),
                  success: function(response) {
                      if(response.status) {
                          successToastMsg(response.message);
                          window.location.href = "{{ route('admin.dashboard') }}";
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