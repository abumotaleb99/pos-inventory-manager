<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="card px-5 py-5">
                <div class="row justify-content-between">
                    <div class="align-items-center col">
                        <h4>Product</h4>
                    </div>
                    <div class="align-items-center col">
                        <button data-bs-toggle="modal" data-bs-target="#create-modal" class="float-end btn m-0 bg-gradient-primary">Create</button>
                    </div>
                </div>
                <hr class="bg-dark"/>
                <table class="table" id="productTable">
                    <thead>
                        <tr class="bg-light">
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Unit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="productList">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    getList();

    async function getList() {
        try {
            showLoader();
            // Fetch product list
            let res = await axios.get("/list-product");
            hideLoader();
            let productList = $("#productList");
            let productTable = $("#productTable");

            // Destroy existing DataTable
            productTable.DataTable().destroy();
            productList.empty();

            // Populate product list table
            res.data.forEach(function (item, index) {
                let row = `<tr>
                                <td><img class="w-15 h-auto" alt="" src="${item['img_url']}"></td>
                                <td>${item['name']}</td>
                                <td>${item['price']}</td>
                                <td>${item['unit']}</td>
                                <td>
                                    <button data-path="${item['img_url']}" data-id="${item['id']}" class="btn editBtn btn-sm btn-outline-success">Edit</button>
                                    <button data-path="${item['img_url']}" data-id="${item['id']}" class="btn deleteBtn btn-sm btn-outline-danger">Delete</button>
                                </td>
                            </tr>`;
                productList.append(row);
            });

            // Attach event listeners to edit and delete buttons
            $('.editBtn').on('click', async function () {
                let id = $(this).data('id');
                let filePath = $(this).data('path');
                await FillUpUpdateForm(id, filePath);
                $("#update-modal").modal('show');
            });

            $('.deleteBtn').on('click', function () {
                let id = $(this).data('id');
                let path = $(this).data('path');
                $("#delete-modal").modal('show');
                $("#deleteID").val(id);
                $("#deleteFilePath").val(path);
            });

            // Initialize DataTable
            new DataTable('#productTable', {
                order: [[0, 'desc']],
                lengthMenu: [5, 10, 15, 20, 30]
            });
        } catch (error) {
            console.error("Failed to fetch product list:", error);
        }
    }
</script>
