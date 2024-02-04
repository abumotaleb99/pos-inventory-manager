<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 center-screen">
            <div class="card animated fadeIn w-90  p-4">
                <div class="card-body">
                    <h4>EMAIL ADDRESS</h4>
                    <br/>
                    <label>Your email address</label>
                    <input id="email" placeholder="User Email" class="form-control" type="email"/>
                    <br/>
                    <button onclick="verifyEmail()"  class="btn w-100 float-end bg-gradient-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function verifyEmail() {
        try {
            let email = document.getElementById('email').value;
            showLoader();
            let res = await axios.post('/send-otp', { email: email });
            hideLoader();

            if (res.status === 200 && res.data['status'] === 'success') {
                successToast(res.data['message']);
                sessionStorage.setItem('email', email);
                setTimeout(function () {
                    window.location.href = '/verify-otp';
                }, 1000);
            } else {
                errorToast(res.data['message']);
            }
        } catch (error) {
            // Handle Axios errors here
            hideLoader();

            if (error.response) {
                if (error.response.status === 422 && error.response.data['status'] === 'error') {
                    displayValidationErrors(error.response.data['errors']);
                    return;
                }
                errorToast(error.response.data['message']);
            }
        }
    }

    function displayValidationErrors(errors) {
        for (let field in errors) {
            errorToast(errors[field][0]); // Display the first error for each field
        }
    }
</script>

