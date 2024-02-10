<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card px-5 py-5">
                <div class="row justify-content-between">
                    <div class="align-items-center col">
                        <h4>Category</h4>
                    </div>
                    <div class="align-items-center col">
                        <button data-bs-toggle="modal" data-bs-target="#create-modal" class="float-end btn m-0 bg-gradient-primary">Create</button>
                    </div>
                </div>
                <hr class="bg-secondary"/>
                <div class="table-responsive">
                    <table class="table" id="categoryTable">
                        <thead>
                            <tr class="bg-light">
                                <th>No</th>
                                <th>Category Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="categoryList">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    getList();

    async function getList() {
        try {
            showLoader();
            let response = await axios.get("/category-list");
            hideLoader();

            let categoryListTable = $("#categoryList");
            let categoryDataTable = $("#categoryTable");

            // Destroy previous DataTable instance and clear the category list
            categoryDataTable.DataTable().destroy();
            categoryListTable.empty();

            response.data.forEach(function (item, index) {
                let row = `<tr>
                                <td>${index + 1}</td>
                                <td>${item['name']}</td>
                                <td>
                                    <button data-id="${item['id']}" class="btn editBtn btn-sm btn-outline-success">Edit</button>
                                    <button data-id="${item['id']}" class="btn deleteBtn btn-sm btn-outline-danger">Delete</button>
                                </td>
                            </tr>`;
                categoryListTable.append(row);
            });

            // Attach click event handlers for edit and delete buttons
            $('.editBtn').on('click', async function () {
                let id = $(this).data('id');
                await fillUpdateForm(id);
                $("#update-modal").modal('show');
            });

            $('.deleteBtn').on('click', function () {
                let id = $(this).data('id');
                $("#delete-modal").modal('show');
                $("#deleteID").val(id);
            });

            new DataTable('#categoryTable', {
                order: [[0, 'desc']],
                lengthMenu: [5, 10, 15, 20, 30]
            });
        } catch (error) {
            hideLoader();
            errorToast("Failed to fetch category list.");
        }
    }
</script>
