<!DOCTYPE html>
<html>

<head>
    <title>Laravel jQuery DataTable</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        #imagePreview {
            max-width: 100%;
            height: auto;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>User Management</h2>
        <button class="btn btn-success mb-3" id="addUserBtn">Add User</button>
        <table id="userTable" class="display">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Date</th>
                    <th>Standard</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="userForm" enctype="multipart/form-data">
                <input type="hidden" id="userId" name="userId">
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Student</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="mobile">Mobile</label>
                                        <input type="text" class="form-control" name="mobile" id="mobile" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" class="form-control" name="date" id="date" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="standard">Standard</label>
                                        <input type="text" class="form-control" name="standard" id="standard" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="image">Image</label>
                                        <input type="file" class="form-control" name="image" id="image">
                                        <input type="hidden" id="existingImage" value="">
                                        <img id="imagePreview" src="" alt="Image Preview">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="street">Street</label>
                                        <input type="text" class="form-control" name="addresses[0][street]" id="street" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" name="addresses[0][city]" id="city" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="state">State</label>
                                        <input type="text" class="form-control" name="addresses[0][state]" id="state" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="country">Country</label>
                                        <input type="text" class="form-control" name="addresses[0][country]" id="country" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="postal_code">Postal Code</label>
                                        <input type="text" class="form-control" name="addresses[0][postal_code]" id="postal_code" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#userTable').DataTable({
                ajax: {
                    url: '/students',
                    dataSrc: ''
                },
                columns: [
                    { data: 'name' },
                    { data: 'mobile' },
                    { data: 'date' },
                    { data: 'standard' },
                    {
                        data: 'id',
                        render: function (data) {
                            return `
                                <button class="btn btn-info editBtn" data-id="${data}">Edit</button>
                                <button class="btn btn-danger deleteBtn" data-id="${data}">Delete</button>
                            `;
                        }
                    }
                ]
            });

            $("#addUserBtn").click(function () {
                $("#userForm")[0].reset();
                $("#userId").val('');
                $('#imagePreview').hide(); // Hide image preview on add
                $('#userModal').modal('show');
            });

            $('#userForm').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData(this);

                var id = $('#userId').val();
                if (id) {
                    formData.append('_method', 'PUT'); // Append _method for update
                }

                // // Log formData entries for debugging
                // for (var pair of formData.entries()) {
                //     console.log(pair[0] + ': ' + pair[1]);
                // }

                var url = id ? `/students/${id}` : '/students';
                var type = id ? 'POST' : 'POST'; // POST with _method override for update

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#userModal').modal('hide');
                        table.ajax.reload(); // Reload DataTable to reflect changes
                    },
                    error: function (response) {
                        console.error('Error:', response.responseText);
                        alert('Error: ' + response.responseText);
                    }
                });
            });

            $('#image').on('change', function () {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#userTable').on('click', '.editBtn', function () {
                var id = $(this).data('id');
                $.get(`/students/${id}`, function (data) {
                    $('#userId').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#date').val(data.date);
                    $('#standard').val(data.standard);

                    if (data.image_path) {
                        $('#existingImage').val(data.image_path);
                        $('#imagePreview').attr('src', `/storage/${data.image_path}`).show(); // Ensure the path is correct
                    } else {
                        $('#imagePreview').hide(); // Hide image preview if no image
                    }

                    if (data.addresses && data.addresses.length > 0) {
                        var address = data.addresses[0];
                        $('#street').val(address.street);
                        $('#city').val(address.city);
                        $('#state').val(address.state);
                        $('#country').val(address.country);
                        $('#postal_code').val(address.postal_code);
                    }

                    $('#userModal').modal('show');
                });
            });


            $('#userTable').on('click', '.deleteBtn', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Do you want to delete this Student?",
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: "Yes",
                    denyButtonText: "No"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/students/${id}`,
                            type: 'DELETE',
                            success: function (response) {
                                table.ajax.reload();
                                Swal.fire("Deleted!", "User has been deleted.", "success");
                            },
                            error: function (response) {
                                Swal.fire("Error", "There was an error deleting the user.", "error");
                            }
                        });
                    } else if (result.isDenied) {
                        Swal.fire("Cancelled", "User was not deleted.", "info");
                    }
                });
            });
        });

    </script>
</body>

</html>
