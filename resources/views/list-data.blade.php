@extends('welcome')
@section('content')
    @include('utils.alert')
    <div class="table-wrap mb-5">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add
                Record</button>
        </div>
        <table class="table table-hover mt-3" id="all_data_table">
            <thead>
                <tr>
                    <th scope="col" style="cursor: pointer;" class="column" data-field="id" data-sort-by="">ID</th>
                    <th scope="col" style="cursor: pointer;" class="column" data-field="name" data-sort-by="">Name</th>
                    <th scope="col">Image</th>
                    <th scope="col">Address</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div class="edit_form" style="display: none;">
        <div class="d-flex justify-content-center align-item-center mb-5">
            <form id="edit_data_form">
                <input type="hidden" name="edit_id" id="edit_id" value="" disabled>
                <div class="d-flex justify-content-between">
                    <h4>Edit Data</h4>
                    <h4 class="close_edit_form text-danger" style="cursor: pointer;">X</h4>
                </div>
                <div class="mb-3">
                    <label for="edit_name" class="form-label">Name</label>
                    <input type="text" class="form-control" name="edit_name" id="edit_name" required>
                </div>
                <div class="mb-3">
                    <label for="edit_address" class="form-label">Address</label>
                    <input type="text" class="form-control" name="edit_address" id="edit_address" required>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" name="edit_gender" id="edit_gender" required>
                        <option value="" selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" class="form-control" accept="image/png, image/jpeg" name="image"
                        id="edit_image">
                    <div id="photo_error" class="form-text text-danger"></div>
                    <div class="preview_image"></div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <div class="user_details" style="display: none;">
        <h1>Data Details</h1>
        <p>ID: <span id="data-id"></span></p>
        <p>Name: <span id="data-name"></span></p>
        <p>Address: <span id="data-address"></span></p>
        <p>Gender: <span id="data-gender"></span></p>
        <p>Image: <span id="data-image"></span></p>
    </div>

    <style>
        #all_data_table img {
            width: 100px;
            height: auto;
        }
    </style>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="new_record_form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" id="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" name="gender" id="gender" required>
                                <option value="" selected>Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" accept="image/png, image/jpeg" name="image"
                                id="image">
                            <div id="photo_error" class="form-text text-danger"></div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            loadData();
        });

        function loadData() {
            $.ajax({
                url: "{{ route('show-data') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateTable(response);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        function populateTable(data) {
            var tableBody = $('#all_data_table tbody');
            tableBody.empty();

            $.each(data, function(index, item) {
                var row = $('<tr>');
                row.append($('<td>').text(item.id));
                row.append($('<td>').text(item.name));
                row.append($('<td>').append($('<img>').attr('src', 'uploads/' + item.image)));
                row.append($('<td>').text(item.address));
                row.append($('<td>').text(item.gender));

                var buttonGroup = $('<div>');
                var editButton = $('<button data-row-id="' + item.id + '">').addClass('btn btn-primary').text(
                    'Edit');
                var deleteButton = $('<button data-row-id="' + item.id + '">').addClass('btn btn-danger').text(
                    'Delete');
                var viewButton = $('<button data-row-id="' + item.id + '">').addClass('btn btn-secondary').text(
                    'View');
                buttonGroup.append(editButton, deleteButton, viewButton);

                row.append($('<td>').append(buttonGroup));

                tableBody.append(row);
            });
        }

        //Store new data request
        $('#new_record_form').on('submit', function(e) {
            e.preventDefault();

            let name = $('#name').val();
            let address = $('#address').val();
            let gender = $('#gender').val();
            const file = $(this).find('input[type="file"]')[0].files[0];

            var formData = new FormData();
            formData.append('photo', file);
            formData.append('name', name);
            formData.append('address', address);
            formData.append('gender', gender);

            if (!name) {
                $('.alert.alert-danger').html('The name field is required.').show();
            } else if (!address) {
                $('.alert.alert-danger').html('The address field is required.').show();
            } else if (!gender) {
                $('.alert.alert-danger').html('The gender field is required.').show();
            } else {
                $('.alert.alert-danger').hide();

                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('form-submit') }}",
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        loadData();
                        $('#new_record_form')[0].reset();
                        $('#exampleModal').modal('hide');
                    },
                    error: function(res) {
                        if (res.responseJSON.errors.photo[0]) {
                            $('#photo_error').html(res.responseJSON.errors.photo[0]);
                        }
                    }
                });
            }
        });

        //Delete data request
        $(document).on('click', '.btn.btn-danger', function() {
            let data_id = $(this).data('row-id');
            var formData = new FormData();
            formData.append('id', data_id);
            if (data_id) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('remove-data') }}",
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        loadData();
                    },
                    error: function(res) {
                        console.log(res.responseJSON.error);
                    }
                });
            }
        });

        //View data request
        $(document).on('click', '.btn.btn-secondary', function() {
            let data_id = $(this).data('row-id');
            var formData = new FormData();
            formData.append('id', data_id);
            if (data_id) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('get-data') }}",
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        $('#data-id').text(response.id);
                        $('#data-name').text(response.name);
                        $('#data-address').text(response.address);
                        $('#data-gender').text(response.gender);
                        $('#data-image').html($('<img style="width: 300px; height: auto;">').attr('src', 'uploads/' + response.image));

                        $('.user_details').show();
                    },
                    error: function(res) {
                        console.log(res.responseJSON.error);
                    }
                });
            }
        });

        //Show edit form
        $(document).on('click', '.btn.btn-primary', function() {
            let data_id = $(this).data('row-id');
            var formData = new FormData();
            formData.append('id', data_id);
            if (data_id) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('get-data') }}",
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        $('#edit_id').val(response.id);
                        $('#edit_name').val(response.name);
                        $('#edit_address').val(response.address);
                        $('#edit_gender').val(response.gender);
                        $('.preview_image').html($('<img style="width: 100px; height: auto;">').attr('src', 'uploads/' + response.image));

                        $('.edit_form').show();
                    },
                    error: function(res) {
                        console.log(res.responseJSON.error);
                    }
                });
            }
        });

        $(document).on('click', '.close_edit_form', function() {
            $('.edit_form').hide();
        });

        //Update data request
        $('#edit_data_form').on('submit', function(e) {
            e.preventDefault();

            let name = $('#edit_name').val();
            let address = $('#edit_address').val();
            let gender = $('#edit_gender').val();
            let edit_id = $('#edit_id').val();
            const file = $(this).find('#edit_image')[0].files[0];

            var formData = new FormData();
            formData.append('photo', file);
            formData.append('name', name);
            formData.append('address', address);
            formData.append('gender', gender);
            formData.append('id', edit_id);

            if (!name) {
                $('.alert.alert-danger').html('The name field is required.').show();
            } else if (!address) {
                $('.alert.alert-danger').html('The address field is required.').show();
            } else if (!gender) {
                $('.alert.alert-danger').html('The gender field is required.').show();
            } else {
                $('.alert.alert-danger').hide();

                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('update-data') }}",
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        loadData();
                        $('#edit_data_form')[0].reset();
                        $('.edit_form').hide();
                    },
                    error: function(res) {
                        console.log(res.responseJSON.error);
                    }
                });
            }
        });

        //Short by id
        $('#all_data_table .column').on('click', function(e) {
            let field_name = $(this).data('field');
            let sort_by = $(this).data('sort-by');
            if (!sort_by || sort_by === 'desc') {
                sort_by = 'asc';
                $(this).data('sort-by', sort_by);
            } else if (sort_by === 'asc') {
                sort_by = 'desc';
                $(this).data('sort-by', sort_by);
            }

            var formData = new FormData();
            formData.append('sort_by', sort_by);
            formData.append('field_name', field_name);

            if (field_name) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('sort-data') }}",
                    type: 'POST',
                    contentType: 'multipart/form-data',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(response) {
                        populateTable(response);
                    },
                    error: function(res) {
                        console.log(res.responseJSON.error);
                    }
                });
            }
        });
    </script>
@endsection
