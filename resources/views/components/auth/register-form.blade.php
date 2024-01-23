<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-10 center-screen">
            <div class="card animated fadeIn w-100 p-3">
                <div class="card-body">
                    <h4>Sign Up</h4>
                    <hr/>
                    <div class="container-fluid m-0 p-0">
                        <div class="row m-0 p-0">
                            <div class="col-md-4 p-2">
                                <label>Email Address</label>
                                <input id="email" placeholder="User Email" class="form-control" type="email"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>First Name</label>
                                <input id="first_name" placeholder="First Name" class="form-control" type="text"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>Last Name</label>
                                <input id="last_name" placeholder="Last Name" class="form-control" type="text"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>Mobile Number</label>
                                <input id="phone" placeholder="Mobile" class="form-control" type="mobile"/>
                            </div>
                            <div class="col-md-4 p-2">
                                <label>Password</label>
                                <input id="password" placeholder="User Password" class="form-control" type="password"/>
                            </div>
                        </div>
                        <div class="row m-0 p-0">
                            <div class="col-md-4 p-2">
                                <button onclick="onRegistration()" class="btn mt-3 w-100  bg-gradient-primary">Complete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>


  async function onRegistration() {
    try {    
        let email = document.getElementById('email').value;
        let firstName = document.getElementById('first_name').value;
        let lastName = document.getElementById('last_name').value;
        let phone = document.getElementById('phone').value;
        let password = document.getElementById('password').value;

        showLoader();
        let res=await axios.post("/user-register", { 
            email: email,
            first_name: firstName,
            last_name: lastName,
            phone: phone,
            password: password
        });
        hideLoader();
        
        if(res.status === 201 && res.data['status']==='success'){
            successToast(res.data['message']);
            setTimeout(function (){
                window.location.href='/user-login'
            },2000)
        }
    }catch(error) {
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
