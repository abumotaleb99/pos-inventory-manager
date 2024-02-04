<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 center-screen">
            <div class="card animated fadeIn w-90  p-4">
                <div class="card-body">
                    <h4>ENTER OTP CODE</h4>
                    <br/>
                    <label>4 Digit Code Here</label>
                    <input id="otp" placeholder="Code" class="form-control" type="text"/>
                    <br/>
                    <button onclick="verifyOtp()"  class="btn w-100 float-end bg-gradient-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function verifyOtp() {
        try {
            let otp = document.getElementById('otp').value;

            if (otp.length !== 4) {
                errorToast('Invalid OTP');
            } else {
                showLoader();
                let res = await axios.post('/verify-otp', {
                    otp: otp,
                    email: sessionStorage.getItem('email')
                });
                hideLoader();

                if (res.status === 200 && res.data['status'] === 'success') {
                    successToast(res.data['message']);
                    // sessionStorage.clear();
                    setTimeout(() => {
                        window.location.href = '/reset-password';
                    }, 1000);
                } else {
                    errorToast(res.data['message']);
                }
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

