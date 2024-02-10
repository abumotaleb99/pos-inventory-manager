<div class="modal animated zoomIn" id="update-modal" tabindex="-1" aria-labelledby="update-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="update-modal-title">Update Category</h5>
            </div>
            <div class="modal-body">
                <form id="update-form">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 p-1">
                                <label for="categoryNameUpdate" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="categoryNameUpdate" aria-describedby="categoryNameUpdateHelp" required>
                                <small id="categoryNameUpdateHelp" class="form-text text-muted">Enter the updated category name.</small>
                                <input type="hidden" id="updateID">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="update-modal-close" class="btn bg-gradient-primary" data-bs-dismiss="modal">Close</button>
                <button onclick="updateCategory()" class="btn bg-gradient-success" >Update</button>
            </div>
        </div>
    </div>
</div>

<script>
    async function fillUpdateForm(id) {
        document.getElementById('updateID').value = id;
        showLoader();
        let response = await axios.post("/category-by-id", { id: id });
        hideLoader();
        document.getElementById('categoryNameUpdate').value = response.data['name'];
    }

    async function updateCategory() {
        let categoryName = document.getElementById('categoryNameUpdate').value;
        let updateID = document.getElementById('updateID').value;

        if (categoryName.length === 0) {
            errorToast("Category name is required!");
        } else {
            document.getElementById('update-modal-close').click();
            showLoader();
            try {
                let response = await axios.post("/update-category", { name: categoryName, id: updateID });
                hideLoader();
                if (response.status === 200 && response.data.status === 'success') {
                    document.getElementById("update-form").reset();
                    successToast(response.data.message); 
                    await getList();
                } else {
                    errorToast(response.data.message); 
                }
            } catch (error) {
                if (error.response && error.response.status === 422 && error.response.data['status'] === 'error') {
                    displayValidationErrors(error.response.data['errors']);
                } else {
                    errorToast("Request failed.");
                }
            }
        }
    }

    function displayValidationErrors(errors) {
        for (let field in errors) {
            errorToast(errors[field][0]); // Display the first error for each field
        }
    }
</script>
