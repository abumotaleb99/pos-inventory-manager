<div class="modal animated zoomIn" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h3 class="mt-3 text-warning">Delete Confirmation</h3>
                <p class="mb-3">Once deleted, you can't recover it.</p>
                <input type="hidden" class="d-none" id="deleteID" />
                <input type="hidden" class="d-none" id="deleteFilePath" />
            </div>
            <div class="modal-footer justify-content-end">
                <div>
                    <button type="button" id="delete-modal-close" class="btn bg-gradient-primary mx-2" data-bs-dismiss="modal">Cancel</button>
                    <button onclick="confirmDelete()" type="button" id="confirmDelete" class="btn bg-gradient-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function confirmDelete() {
        try {
            let id = document.getElementById('deleteID').value;
            let deleteFilePath = document.getElementById('deleteFilePath').value;
            document.getElementById('delete-modal-close').click();
            showLoader();
            let res = await axios.post("/delete-product", { id: id, file_path: deleteFilePath });
            hideLoader();
            if (res.data.status === 'success') {
                successToast(res.data.message);
                await getList();
            } else {
                errorToast(res.data.message);
            }
        } catch (error) {
            hideLoader();
            console.error("Failed to delete item:", error);
            errorToast("Failed to delete item. Please try again.");
        }
    }
</script>
