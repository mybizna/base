<div>

    <div class="w-full max-w-full px-3 text-center flex-0">
        <h3 class="mt-12">Setup Wizard.</h3>
        <p class="font-normal dark:text-white text-slate-400">
            System not set yet. Please follow the steps below to set up the system.
        </p>
        <div multisteps-form class="mb-12">

            <div class="w-full max-w-full px-3 m-auto flex-0 lg:w-8/12">
                @include('base::setup.tabs')
            </div>

            <div class="flex flex-wrap">
                <div class="w-full max-w-full px-3 m-auto flex-0 lg:w-8/12">
                    <form class="relative">

                        @include('base::setup.admin-account')
                        @include('base::setup.migrate-db')
                        @include('base::setup.default-data')

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Function to retrieve form input values
    function getFormValues() {
      var firstName = document.getElementById('first_name').value;
      var lastName = document.getElementById('last_name').value;
      var username = document.getElementById('username').value;
      var password = document.getElementById('password').value;
      var phone = document.getElementById('phone').value;
      var email = document.getElementById('email').value;
      
      // Validate the inputs
      if (firstName === '' || lastName === '' || username === '' || password === '' || phone === '' || email === '') {
        alert('Please fill in all the fields.');
        return;
      }
      
      // Create an object with the form data
      var formData = {
        first_name: firstName,
        last_name: lastName,
        username: username,
        password: password,
        phone: phone,
        email: email
      };
      
      // Convert the object to JSON
      var jsonData = JSON.stringify(formData);
      
      // Create a new XHR object
      var xhr = new XMLHttpRequest();
      
      // Set up the request
      xhr.open('POST', 'http://127.0.0.1/setup', true);
      xhr.setRequestHeader('Content-type', 'application/json');
      
      // Handle the response
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          alert('Form data submitted successfully!');
        } else if (xhr.readyState === 4 && xhr.status !== 200) {
          alert('Error occurred. Please try again.');
        }
      };
      
      // Send the request
      xhr.send(jsonData);
    }
  </script>
  
