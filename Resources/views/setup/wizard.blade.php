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
                    <form id="setup-wizard" class="relative">
                        @csrf

                        @include('base::setup.admin-account')
                        @include('base::setup.migrate-db')

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Function to retrieve form input values
    function getFormValues() {
        var form = document.getElementById('setup-wizard');

        var firstName = form.elements['first_name'].value;
        var lastName = form.elements['last_name'].value;
        var username = form.elements['username'].value;
        var password = form.elements['password'].value;
        var phone = form.elements['phone'].value;
        var email = form.elements['email'].value;

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

        var modules = [{
                name: 'base',
                icon: 'fa fa-base'
            },
            {
                name: 'core',
                icon: 'fa fa-core'
            }
        ];

        // Set up the fetch request for automigrator
        fetch('/automigrator-migrate', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.elements['_token'].value
                }
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Automigrator migration failed.');
                }
            });

        // Set up the fetch request
        fetch('{!! url(route('setup')) !!}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.elements['_token'].value
                },
                body: jsonData
            })
            .then(function(response) {
                if (response.ok) {
                    alert('Form data submitted successfully!');
                } else {
                    alert('Error occurred. Please try again.');
                }
            })
            .catch(function(error) {
                alert('Error occurred. Please try again.');
            });

        // Run the data processor
        fetch('/mybizna-dataprocessor', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.elements['_token'].value
                }
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Data processor migration failed.');
                }
            });

    }
</script>


<style>
    .terminal {
        border-radius: 5px 5px 0 0;
        position: relative;
    }

    .terminal .top {
        background: #E8E6E8;
        color: black;
        padding: 5px;
        border-radius: 5px 5px 0 0;
    }

    .terminal .btns {
        position: absolute;
        top: 7px;
        left: 5px;
    }

    .terminal .circle {
        width: 12px;
        height: 12px;
        display: inline-block;
        border-radius: 15px;
        margin-left: 2px;
        border-width: 1px;
        border-style: solid;
    }

    .title {
        text-align: center;
    }

    .red {
        background: #EC6A5F;
        border-color: #D04E42;
    }

    .green {
        background: #64CC57;
        border-color: #4EA73B;
    }

    .yellow {
        background: #F5C04F;
        border-color: #D6A13D;
    }

    .clear {
        clear: both;
    }

    .terminal .body {
        background: black;
        color: #7AFB4C;
        overflow: auto;
    }

    .space {
        margin: 25px;
    }

    .shadow {
        box-shadow: 0px 0px 10px rgba(0, 0, 0, .4)
    }

    .line4{
        background: black;
        color: #7AFB4C;
    }

    .cursor4 {
        -webkit-animation: blink 1s 11.5s infinite !important;
        -moz-animation: blink 1s 8.5s infinite !important;
        -o-animation: blink 1s 8.5s infinite !important;
        animation: blink 1s 8.5s infinite !important;
    }

    @-webkit-keyframes blink {
        0% {
            opacity: 0;
        }

        40% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    @-moz-keyframes blink {
        0% {
            opacity: 0;
        }

        40% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    @-o-keyframes blink {
        0% {
            opacity: 0;
        }

        40% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    @keyframes blink {
        0% {
            opacity: 0;
        }

        40% {
            opacity: 0;
        }

        50% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    @-webkit-keyframes type {
        to {
            width: 17em;
        }
    }

    @-moz-keyframes type {
        to {
            width: 17em;
        }
    }

    @-o-keyframes type {
        to {
            width: 17em;
        }
    }

    @keyframes type {
        to {
            width: 17em;
        }
    }
</style>
