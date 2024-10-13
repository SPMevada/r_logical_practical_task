@extends('frontent_layout.app')
@section('title','Cart Items')
@section('content')
  <table class="table" id="productTable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Product</th>
            <th scope="col">Image</th>
            <th scope="col">Price</th>
            <th scope="col">Quantity</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

@endsection
@section('script')
    <script type="text/javascript">
       $(document).ready(function() {
        $('#productTable').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
                "url": "{{ route('frontent.carditem') }}",
                "type": "GET"
            },  
            columns: [
                { 
                    data: 'DT_RowIndex', name: '', orderable: true, searchable: false
                },
                { data: 'name', name: 'name' },
                { data: 'image', name: 'image' },
                { data: 'price', name: 'price' },
                { data: 'qty', name: 'qty' },
            ]
        });
       });
    </script>
@endsection