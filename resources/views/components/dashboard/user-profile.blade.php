<div class="container">
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card animated fadeIn w-100 p-3">
                <div class="card-body">
                    <h4>User Profile</h4>
                    <hr/>
                    <div class="container-fluid m-0 p-0">
                        <div class="row m-0 p-0">
                            <div class="col-md-4 p-2">
                                <label>Email Address</label>
                                <input readonly id="email" placeholder="User Email" class="form-control" type="email"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>First Name</label>
                                <input id="firstName" placeholder="First Name" class="form-control" type="text"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>Last Name</label>
                                <input id="lastName" placeholder="Last Name" class="form-control" type="text"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>Phone Number</label>
                                <input id="phone" placeholder="Mobile" class="form-control" type="mobile"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>Password</label>
                                <input id="password" placeholder="User Password" class="form-control" type="password"/>
                            </div>
                        </div>
                        <div class="row m-0 p-0">
                            <div class="col-md-4 p-2">
                                <button onclick="updateProfile()" class="btn mt-3 w-100  bg-gradient-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function getProfile() {
        try {
            showLoader();
            let res = await axios.get("/profile");
            hideLoader();
            
            if (res.status === 200 && res.data['status'] === 'success') {
                let data = res.data['data'];
                document.getElementById('email').value = data['email'];
                document.getElementById('firstName').value = data['first_name'];
                document.getElementById('lastName').value = data['last_name'];
                document.getElementById('phone').value = data['phone'];
                document.getElementById('password').value = data['password'];
            } else {
                errorToast(res.data['message']);
            }
        } catch (error) {
            hideLoader();
            
            if (error.response) {
                errorToast(error.response.data['message']);
            } else {
                errorToast('An error occurred while fetching user profile.');
            }
        }
    }

    getProfile();


    async function updateProfile() {
        try {
            let firstName = document.getElementById('firstName').value;
            let lastName = document.getElementById('lastName').value;
            let phone = document.getElementById('phone').value;
            let password = document.getElementById('password').value;

            showLoader();
            let res = await axios.post("/user-update", {
                first_name: firstName,
                last_name: lastName,
                phone: phone,
                password: password
            });
            hideLoader();

            if (res.status === 200 && res.data['status'] === 'success') {
                successToast(res.data['message']);
                await getProfile();
            }

        } catch (error) {
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

