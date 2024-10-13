@extends('layouts.app')
@section('title','Product')
@section('content')
  <div class="justify-content-left p-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productAddModal">
        Add Product
    </button>
  </div>
  <table class="table" id="productTable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Title</th>
            <th scope="col">Description</th>
            <th scope="col">Price</th>
            <th scope="col">Qty</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

    <!-- Add Category Modal :: START -->
    <div class="modal fade" id="productAddModal" tabindex="-1" aria-labelledby="productAddModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h1 class="modal-title fs-5" id="productAddModalLabel">Product Add</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <form id="addForm" enctype="multipart/form-data" method="POST">
                      @include('product.form');
                  </form>
              </div>
          </div>
      </div>
    </div>
    <!-- Add Category Modal :: START -->

    <!-- Edit Category Modal :: START -->
    <div class="modal fade" id="productEditModal" tabindex="-1" aria-labelledby="productEditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="productEditModalLabel">Category Edit</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" enctype="multipart/form-data" method="POST">
                    <div class="modal-body formData">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Category Modal :: START -->

@endsection
@section('script')
    <script type="text/javascript">
       $(document).ready(function() {
            // $('#category').select2();

            $.validator.addMethod("decimal", function(value, element) {
                return this.optional(element) || /^\d+(\.\d{1,2})?$/.test(value);
            }, "Please enter a valid price (e.g., 150.75 or 150).");

            // Custom validation method for quantity
            $.validator.addMethod("wholeNumber", function(value, element) {
                return this.optional(element) || /^[0-9]+$/.test(value);
            }, "Please enter a whole number (e.g., 100, 50).");
            
            var table = $('#productTable').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "{{ route('product.index') }}",
                            "type": "GET"
                        },  
                        columns: [
                            { 
                                data: 'DT_RowIndex', name: '', orderable: true, searchable: false
                            },
                            { data: 'name', name: 'name' },
                            { data: 'description', name: 'description' },
                            { data: 'price', name: 'price' },
                            { data: 'qty', name: 'qty' },
                            { data: 'status', name: 'status' },
                            { data: 'action', name: 'action' },
                        ]
                    });

            $('#addForm').validate({
                    rules: {
                        name: {
                            required: true,
                            maxlength: 255
                        },
                        description: {
                            required: true,
                        },
                        "categories[]": {
                            required: true,
                        },
                        price: {
                            required: true,
                            decimal: true
                        },
                        qty: {
                            required: true,
                            wholeNumber: true
                        },
                        status: {
                            required: true,
                        },
                    },
                    highlight: function (element) {
                        $(element).parent().addClass('error')
                    },
                    unhighlight: function (element) {
                        $(element).parent().removeClass('error')
                    }
            });
            
            $('#editForm').validate({
                    rules: {
                        name: {
                            required: true,
                            maxlength: 255
                        },
                        description: {
                            required: true,
                        },
                        "categories[]": {
                            required: true,
                        },
                        price: {
                            required: true,
                            decimal: true
                        },
                        qty: {
                            required: true,
                            wholeNumber: true
                        },
                        status: {
                            required: true,
                        },
                    },
                    highlight: function (element) {
                        $(element).parent().addClass('error')
                    },
                    unhighlight: function (element) {
                        $(element).parent().removeClass('error')
                    }
            });

            $(document).on('submit','#addForm',function(e) {
                e.preventDefault();
                var self = $(this);
                var formData = new FormData(self[0]);

                $.ajax({
                    url: "{{ route('product.store') }}",
                    type: "POST",
                    data: formData,
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        if(response.status) {
                            $('#productAddModal').modal('hide');
                            table.draw();
                            toastsuccessMessage(response.message)
                        } else {
                            toastFailedMessage(response.message)
                        }
                    },
                    error: function(response) {
                      toastFailedMsg(response.responseJSON.message)
                    }
                });
            });

            $(document).on('click','.editBtn',function(e) {
                e.preventDefault();
                var self = $(this);
                var product_id = self.data('id');
                
                $.ajax({
                    url: "{{ route('product.edit') }}",
                    type: "GET",
                    data: {
                        product_id: product_id
                    },
                    success: function(response) {
                        if(response.status) {
                            $('.formData').html(response.data.productHtml);
                            $('#productEditModal').modal('show');
                        } else {
                            alert('something went wrong');
                        }
                    }
                });
            });

            $(document).on('submit','#editForm',function(e) {
                e.preventDefault();
                var self = $(this);
                var formData = new FormData(self[0]);
                
                $.ajax({
                    url: "{{ route('product.update') }}",
                    type: "POST",
                    data: formData,
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        if(response.status) {
                            $('#productEditModal').modal('hide');
                            table.draw();
                            toastsuccessMessage(response.message)
                        } else {
                            toastFailedMessage(response.message)
                        }
                    },
                    error: function(response) {
                      toastFailedMsg(response.responseJSON.message)
                    }
                });
            });

            $(document).on('click','.deleteBtn',function(e) {
                e.preventDefault();
                var self = $(this);
                var product_id = self.data('id');
                
                $.ajax({
                    url: "{{ route('product.delete') }}",
                    type: "POST",
                    data: {
                        product_id: product_id
                    },
                    success: function(response) {
                        if(response.status) {
                            table.draw();
                            toastsuccessMessage(response.message)
                        } else {
                            toastFailedMessage(response.message)
                        }
                    }
                });
            });

            function toastsuccessMessage(message) {
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

            function toastFailedMessage(message) {
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