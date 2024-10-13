@extends('layouts.app')
@section('title','Dashboard')
@section('content')
  <div class="justify-content-left p-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryAddModal">
        Add Category
    </button>
  </div>
  <table class="table" id="categoryTable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Description</th>
            <th scope="col">Image</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

    <!-- Add Category Modal :: START -->
    <div class="modal fade" id="categoryAddModal" tabindex="-1" aria-labelledby="categoryAddModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h1 class="modal-title fs-5" id="categoryAddModalLabel">Category Add</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <form id="categoryAddForm" enctype="multipart/form-data" method="POST">
                      @include('categories.form')
                  </form>
              </div>
          </div>
      </div>
    </div>
    <!-- Add Category Modal :: START -->

    <!-- Edit Category Modal :: START -->
    <div class="modal fade" id="categoryEditModal" tabindex="-1" aria-labelledby="categoryEditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="categoryEditModalLabel">Category Edit</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="categoryEditForm" enctype="multipart/form-data" method="POST">
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

        var table = $('#categoryTable').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "{{ route('category.index') }}",
                            "type": "GET"
                        },  
                        columns: [
                            { 
                                data: 'DT_RowIndex', name: '', orderable: true, searchable: false
                            },
                            { data: 'name', name: 'name' },
                            { data: 'description', name: 'description' },
                            { data: 'image', name: 'image' },
                            { data: 'status', name: 'status' },
                            { data: 'action', name: 'action' },
                        ]
                    });


            $('#categoryAddForm').validate({
                    rules: {
                        name: {
                            required: true,
                            maxlength: 255
                        },
                        description: {
                            required: true,
                        },
                        image: {
                            required: true,
                            extension: "jpg|jpeg|png"
                        },
                        status: {
                            required: true,
                        }
                    },
                    highlight: function (element) {
                        $(element).parent().addClass('error')
                    },
                    unhighlight: function (element) {
                        $(element).parent().removeClass('error')
                    }
            });

            $(document).on('submit','#categoryAddForm',function(e) {
                e.preventDefault();
                var self = $(this);
                var formData = new FormData(self[0]);

                $.ajax({
                    url: "{{ route('category.store') }}",
                    type: "POST",
                    data: formData,
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        if(response.status) {
                            $('#categoryAddModal').modal('hide');
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

            // Edit catefory
            $(document).on('click','.editBtn',function(e) {
                e.preventDefault();
                var self = $(this);
                var category_id = self.data('id');
                
                $.ajax({
                    url: "{{ route('category.edit') }}",
                    type: "GET",
                    data: {
                        category_id: category_id
                    },
                    success: function(response) {
                        if(response.status) {
                            $('.formData').html(response.data.categoryHtml);
                            $('#categoryEditModal').modal('show');
                        } else {
                            alert('something went wrong');
                        }
                    }
                });
            });

            $('#categoryEditForm').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 255
                    },
                    description: {
                        required: true,
                    },
                    check_image: {
                        required: true,
                    },
                    image: {
                        extension: "jpg|jpeg|png"
                    },
                    status: {
                        required: true,
                    }
                },
                highlight: function (element) {
                    $(element).parent().addClass('error')
                },
                unhighlight: function (element) {
                    $(element).parent().removeClass('error')
                }
            });

            $(document).on('submit','#categoryEditForm',function(e) {
                e.preventDefault();
                var self = $(this);
                var formData = new FormData(self[0]);
                
                $.ajax({
                    url: "{{ route('category.update') }}",
                    type: "POST",
                    data: formData,
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        if(response.status) {
                            $('#categoryEditModal').modal('hide');
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

            $(document).on('change','.status',function(e) {
                e.preventDefault();
                var self = $(this);
                var category_id = self.data('id');
                if(self.is(':checked')) {
                    var status  = 'active';
                } else {
                    var status  = 'inactive';
                }
                $.ajax({
                    url: "{{ route('category.status') }}",
                    type: "POST",
                    data: {
                        category_id: category_id,
                        status: status,
                    },
                    success: function(response) {
                        if(response.status) {
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
                var category_id = self.data('id');
                
                $.ajax({
                    url: "{{ route('category.delete') }}",
                    type: "POST",
                    data: {
                        category_id: category_id
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